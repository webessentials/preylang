<?php
namespace App\Http\Controllers\API;

use App\Helpers\ImpactHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Impact;
use App\Models\Villager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ImpactAPIController extends Controller
{
    /**
     * Create impact
     *
     * @SWG\Post(
     *      path="/api/impact",
     *      summary="Impact API",
     *      tags={"Impact"},
     *      description="Create Impact API",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *       name="Authorization",
     *       required=true,
     *       type="string",
     *       description="Login access token security key",
     *       default="Bearer {accessToken}",
     *       in="header"
     *      ),
     *     @SWG\Parameter(
     *       name="phoneId",
     *       required=true,
     *       type="string",
     *       in="formData"
     *      ),
     *     @SWG\Parameter(
     *       name="category",
     *       required=true,
     *       type="string",
     *       in="formData"
     *      ),
     *     @SWG\Parameter(
     *       name="subCategory1",
     *       type="string",
     *       in="formData"
     *      ),
     *     @SWG\Parameter(
     *       name="subCategory2",
     *       type="string",
     *       in="formData"
     *      ),
     *     @SWG\Parameter(
     *       name="subCategory3",
     *       type="string",
     *       in="formData"
     *      ),
     *     @SWG\Parameter(
     *       name="subCategory4",
     *       type="string",
     *       in="formData"
     *      ),
     *     @SWG\Parameter(
     *       name="subCategory5",
     *       type="string",
     *       in="formData"
     *      ),
     *     @SWG\Parameter(
     *       name="victimType",
     *       type="string",
     *       in="formData"
     *      ),
     *     @SWG\Parameter(
     *       name="reason",
     *       type="string",
     *       in="formData"
     *      ),
     *     @SWG\Parameter(
     *       name="numberOfItems",
     *       type="string",
     *       in="formData"
     *      ),
     *     @SWG\Parameter(
     *       name="type",
     *       type="string",
     *       in="formData"
     *      ),
     *     @SWG\Parameter(
     *       name="patrollerNote",
     *       type="string",
     *       in="formData"
     *      ),
     *     @SWG\Parameter(
     *       name="excluded",
     *       type="string",
     *       in="formData"
     *      ),
     *      @SWG\Response(
     *          response=201,
     *          description="Created",
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
     *     @SWG\Response(
     *          response=422,
     *          description="Unprocessable Entity",
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
    public function createRecords(Request $request)
    {
        $returnData = [
            'message' => '',
            'impactId' => ''
        ];
        $validator = Validator::make($request->all(), Impact::$apiRule);
        if ($validator->fails()) {
            $returnFailedMessage['message'] = 'Invalid Data';
            return ResponseHelper::makeError('Invalid Data', $returnFailedMessage, 422);
        }
        $villager = Villager::whereDeviceImei($request->get('phoneId'))->first();
        if ($villager instanceof Villager) {
            $impactData = array();
            $impactData['device_imei'] = $request->get('phoneId');
            $impactData['report_date'] = new \DateTime();

            // Check category.
            $impactData['category'] = $request->get('category');
            if ($request->has('subCategory1')) {
                $impactData['sub_category_1'] = $request->get('subCategory1');
            }
            if ($request->has('subCategory2')) {
                $impactData['sub_category_2'] = $request['subCategory2'];
            }
            if ($request->has('subCategory3')) {
                $impactData['sub_category_3'] = $request['subCategory3'];
            }
            if ($request->has('subCategory4')) {
                $impactData['sub_category_4'] = $request['subCategory4'];
            }
            if ($request->has('subCategory5')) {
                $impactData['sub_category_5'] = $request['subCategory5'];
            }

            // For type & permit.
            $impactData['permit'] = '';
            $type = $request->get('type');
            $lowercaseType = strtolower($type);
            if ($request->has('type')) {
                if ($lowercaseType === 'visual') {
                    $impactData['by_visual'] = 1;
                } elseif ($lowercaseType === 'audio') {
                    $impactData['by_audio'] = 1;
                } elseif ($lowercaseType === 'tracks') {
                    $impactData['by_track'] = 1;
                } else {
                    $impactData['permit'] = $type;
                }
            }

            // For excluded & excluded reason.
            $impactData['excluded'] = $request->has('excluded') ? intval($request->get('excluded')) : 0;
            if ($impactData['excluded']) {
                $argExcludedReason = $request->has('excludedReason') ? $request->get('excludedReason') : 'Testing entry';
                $impactData['excluded_reason'] = $argExcludedReason;
            }

            // For victim type.
            if ($request->has('victimType')) {
                $impactData['victim_type'] = $request->get('victimType');
            }

            // For reason.
            if ($request->has('reason')) {
                $impactData['reason'] = $request->get('reason');
            }

            // For numberOfItems.
            if ($request->has('numberOfItems')) {
                $impactData['number_of_items'] = $request->get('numberOfItems');
            }

            // For patrollerNote.
            if ($request->has('patrollerNote')) {
                $impactData['patroller_note'] = $request->get('patrollerNote');
            }

            $impact = ImpactHelper::saveImpact($impactData);

            $returnData['message'] = 'success';
            $returnData['impactId'] = $impact->id;
            return ResponseHelper::makeResponse('Created', $returnData, 201);
        } else {
            $returnData['message'] = 'invalid params';
            return ResponseHelper::makeError('Not Found', $returnData, 404);
        }
    }
}
