<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use App\Models\Villager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @SWG\Info(
 *    version="1.0.0",
 *    title="Preylang API",
 *    description="",
 * )
 */
class AuthController extends Controller
{
    /**
     * Login from device
     *
     * @SWG\Post(
     *      path="/api/login",
     *      summary="Login API",
     *      tags={"Authentication"},
     *      description="Login API",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *       name="api_key",
     *       required=true,
     *       type="string",
     *       in="formData"
     *      ),
     *     @SWG\Parameter(
     *       name="api_secret",
     *       required=true,
     *       type="string",
     *       in="formData"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="status",
     *                  type="string"
     *              )
     *          )
     *      ),
     *      @SWG\Response(
     *          response=404,
     *          description="Not Found",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="status",
     *                  type="string"
     *              )
     *          )
     *      ),
     *      @SWG\Response(
     *          response=406,
     *          description="Not Acceptable",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="status",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     * @param Request $request
     *
     * @return Response
     */
    public function login(Request $request)
    {
        $responseData = [
            'access_token' => '',
            'token_type' => '',
            'expires_at' => ''
        ];
        if (!$request->has('api_key') || !$request->has('api_secret')) {
            return ResponseHelper::makeError('Not Acceptable', $responseData, 406);
        } elseif ($request->get('api_key') == '' || $request->get('api_key') == '') {
            return ResponseHelper::makeError('Not Acceptable', $responseData, 406);
        }
        $deviceImei = $request->get('api_key');
        $password = $request->get('api_secret');
        /* @var Villager $villager */
        $villager = Villager::where('device_imei', '=', $deviceImei)
            ->where('password', '=', $password)
            ->first();
        if ($villager === null) {
            return ResponseHelper::makeError('Not Found', $responseData, 404);
        } else {
            $tokenResult = $villager->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addYear(1);
            $token->save();
            $villager->access_token = $tokenResult->accessToken;
            $villager->token_expiration_date = Carbon::parse($tokenResult->token->expires_at)->toDateTimeString();
            $villager->save();
            $responseData['access_token'] = $tokenResult->accessToken;
            $responseData['token_type'] = 'Bearer';
            $responseData['expires_at'] = Carbon::parse($tokenResult->token->expires_at)->toDateTimeString();
            return ResponseHelper::makeResponse('Success', $responseData);
        }
    }
}
