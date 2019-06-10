<?php
namespace App\Helpers;

use Illuminate\Http\Response;

class ResponseHelper
{
    /**
     * @param string $statusMessage
     * @param array $data
     * @param int $statusCode
     *
     * @return Response
     */
    public static function makeResponse($statusMessage, $data = [], $statusCode = 200)
    {
        return response()->json($data)->setStatusCode($statusCode, $statusMessage);
    }

    /**
     * @param string $statusMessage
     * @param array $data
     * @param int $statusCode
     *
     * @return Response
     */
    public static function makeError($statusMessage, $data = [], $statusCode = 404)
    {
        return self::makeResponse($statusMessage, $data, $statusCode);
    }
}
