<?php declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\JsonResponse;

final class ApiResponse
{
    /**
     * Success response
     */
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        array $meta = [],
        int $statusCode = 200,
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => array_merge([
                'request_id' => request()->attributes->get('request_id'),
                'timestamp' => now()->toIso8601String(),
            ], $meta),
            'errors' => null,
        ], $statusCode);
    }

    /**
     * Error response
     */
    public static function error(
        string $message = 'Error',
        array $errors = [],
        int $statusCode = 400,
        ?array $debug = null,
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
            'data' => null,
            'meta' => [
                'request_id' => request()->attributes->get('request_id'),
                'timestamp' => now()->toIso8601String(),
            ],
            'errors' => $errors,
        ];

        if ($debug !== null && config('app.debug')) {
            $response['debug'] = $debug;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Paginated response
     */
    public static function paginated(
        mixed $data,
        string $message = 'Success',
        array $meta = [],
    ): JsonResponse {
        $paginationMeta = [
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ],
        ];

        return self::success(
            data: $data->items(),
            message: $message,
            meta: array_merge($paginationMeta, $meta),
        );
    }
}
