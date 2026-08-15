<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException as SpatieUnauthorizedException;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\QueryException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // 401 — Non authentifié (token absent ou invalide)
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous devez être authentifié pour accéder à cette ressource.',
                    'data' => null,
                ], 401);
            }
        });

        // 422 — Erreur de validation ($request->validate() ou ValidationException levée manuellement)
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Les données fournies sont invalides.',
                    'errors' => $e->errors(),
                    'data' => null,
                ], 422);
            }
        });

        // 409/422 — Erreurs de contraintes PostgreSQL (unique, clé étrangère, type de données invalide)
        $exceptions->render(function (QueryException $e, Request $request) {
            if ($request->is('api/*')) {
                $codeSQL = $e->getCode();

                return match ($codeSQL) {
                    '23505' => response()->json([
                        'success' => false,
                        'message' => 'Cette donnée existe déjà (violation de contrainte d\'unicité).',
                        'data' => null,
                    ], 409),

                    '23503' => response()->json([
                        'success' => false,
                        'message' => 'Cette opération référence une donnée liée inexistante ou est bloquée par une dépendance.',
                        'data' => null,
                    ], 409),

                    '22P02' => response()->json([
                        'success' => false,
                        'message' => 'Le format d\'un des identifiants fournis est invalide.',
                        'data' => null,
                    ], 422),

                    default => response()->json([
                        'success' => false,
                        'message' => config('app.debug') ? $e->getMessage() : 'Une erreur de base de données est survenue.',
                        'data' => null,
                    ], 500),
                };
            }
        });

        // 403 — Interdit par un rôle/permission Spatie (role:admin, permission:...)
        $exceptions->render(function (SpatieUnauthorizedException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'avez pas les droits nécessaires pour effectuer cette action.',
                    'data' => null,
                ], 403);
            }
        });

        // 404 — Route inexistante OU Route Model Binding échoué (ex: /eleves/9999 qui n'existe pas)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                // Si l'exception d'origine est un ModelNotFoundException, l'ID demandé n'existe pas
                if ($e->getPrevious() instanceof ModelNotFoundException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La ressource demandée est introuvable.',
                        'data' => null,
                    ], 404);
                }

                // Sinon, c'est vraiment une route qui n'existe pas du tout
                return response()->json([
                    'success' => false,
                    'message' => 'La route demandée est introuvable.',
                    'data' => null,
                ], 404);
            }
        });

        // 500 — Filet de sécurité : toute autre exception non gérée explicitement ci-dessus
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                // En debug (développement), on garde le détail pour t'aider à corriger.
                // En production (APP_DEBUG=false), on cache les détails techniques.
                if (config('app.debug')) {
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage(),
                        'exception' => get_class($e),
                        'data' => null,
                    ], 500);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur inattendue est survenue.',
                    'data' => null,
                ], 500);
            }
        });
    })->create();
