<?php declare(strict_types=1);

use App\Http\Middleware\EnsureJsonRequest;
use App\Http\Middleware\InjectRequestId;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Database\QueryException;
use App\Support\ApiResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global API middlewares
        $middleware->api(prepend: [
            InjectRequestId::class,
            EnsureJsonRequest::class,
        ]);

        // Rate limiting
        $middleware->throttleApi();

        // Trust proxies if behind load balancer
        // $middleware->trustProxies(at: ['*']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Validation errors (422)
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                $errors = collect($e->errors())
                    ->map(fn ($messages, $field) => [
                        'code' => 'validation_error',
                        'field' => $field,
                        'detail' => is_array($messages) ? $messages[0] : $messages,
                    ])
                    ->values()
                    ->all();

                return ApiResponse::error(
                    message: 'Validation failed',
                    errors: $errors,
                    statusCode: 422,
                );
            }
        });

        // Authentication errors (401)
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    message: $e->getMessage() ?: 'Unauthenticated',
                    errors: [['code' => 'unauthenticated', 'detail' => 'Authentication required']],
                    statusCode: 401,
                );
            }
        });

        // Authorization errors (403)
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    message: 'Forbidden',
                    errors: [['code' => 'forbidden', 'detail' => $e->getMessage()]],
                    statusCode: 403,
                );
            }
        });

        // Model not found (404)
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                $model = class_basename($e->getModel());
                return ApiResponse::error(
                    message: "{$model} not found",
                    errors: [['code' => 'resource_not_found', 'detail' => 'The requested resource does not exist']],
                    statusCode: 404,
                );
            }
        });

        // Not found HTTP (404)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    message: 'Endpoint not found',
                    errors: [['code' => 'endpoint_not_found', 'detail' => 'The requested endpoint does not exist']],
                    statusCode: 404,
                );
            }
        });

        // Method not allowed (405)
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    message: 'Method not allowed',
                    errors: [['code' => 'method_not_allowed', 'detail' => 'The HTTP method is not allowed for this endpoint']],
                    statusCode: 405,
                );
            }
        });

        // Rate limit exceeded (429)
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    message: 'Too many requests',
                    errors: [['code' => 'rate_limit_exceeded', 'detail' => 'You have exceeded the rate limit']],
                    statusCode: 429,
                );
            }
        });

        // Query exceptions (500 with safe message)
        $exceptions->render(function (QueryException $e, Request $request) {
            if ($request->is('api/*')) {
                $debug = config('app.debug') ? [
                    'sql' => $e->getSql(),
                    'bindings' => $e->getBindings(),
                ] : null;

                return ApiResponse::error(
                    message: 'Database error',
                    errors: [['code' => 'database_error', 'detail' => 'An error occurred while processing your request']],
                    statusCode: 500,
                    debug: $debug,
                );
            }
        });

        // HTTP exceptions with their status codes
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    message: $e->getMessage() ?: 'An error occurred',
                    errors: [['code' => 'http_exception', 'detail' => $e->getMessage()]],
                    statusCode: $e->getStatusCode(),
                );
            }
        });

        // Generic throwable (500)
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                $debug = config('app.debug') ? [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
                ] : null;

                return ApiResponse::error(
                    message: config('app.debug') ? $e->getMessage() : 'Internal server error',
                    errors: [['code' => 'internal_error', 'detail' => 'An unexpected error occurred']],
                    statusCode: 500,
                    debug: $debug,
                );
            }
        });
    })->create();
