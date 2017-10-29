<?php
declare(strict_types=1);

namespace LaraQuiz\Helpers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Class Api
 *
 * @package LaraQuiz\Helpers
 */
class Api
{
    const HTTP_OK = 200;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_INTERNAL_SERVER_ERROR = 500;


    /**
     * @param Carbon|null $date
     * @return int|null
     */
    public static function convertDate(Carbon $date): int
    {
        return $date->getTimestamp();
    }

    /**
     * @param string|array $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public static function respondNotFound($msg = 'Resource not found.'): JsonResponse
    {
        return static::respondError($msg, self::HTTP_NOT_FOUND);
    }

    /**
     * @param string|array $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public static function respondBadRequest($msg = 'Bad request'): JsonResponse
    {
        return static::respondError($msg, self::HTTP_BAD_REQUEST);
    }

    /**
     * @param string|array $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public static function respondForbidden($msg = 'Forbidden.'): JsonResponse
    {
        return static::respondError($msg, self::HTTP_FORBIDDEN);
    }

    /**
     * @param string|array $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public static function respondUnauthorized($msg = 'Unauthorized.'): JsonResponse
    {
        return static::respondError($msg, self::HTTP_UNAUTHORIZED);
    }

    /**
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public static function respondServerError(string $msg = 'Internal server error.'): JsonResponse
    {
        return static::respondError($msg, self::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param string|array $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public static function respondPaymentRequired($msg = 'Payment required'): JsonResponse
    {
        return static::respondError($msg, self::HTTP_PAYMENT_REQUIRED);
    }

    /**
     * @param string|array $msg
     * @param int $code
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public static function respondError($msg = 'Error occurred', int $code = 500, array $headers = []): JsonResponse
    {
        return response()->json([
            'error' => [
                'messages' => is_array($msg) ? $msg : ['common' => [$msg]]
            ]
        ], $code, $headers);
    }

    /**
     * @param mixed $data
     * @param array $headers
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function respond($data, array $headers = [], int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
        ], $statusCode, $headers);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public static function respondSuccess(): Response
    {
        return response('', 204);
    }
}