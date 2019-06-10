<?php
namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Lang;

class ActivityHelper
{
    /**
     * @param string $fieldList
     *
     * @return string
     */
    public static function renderFieldList($fieldList)
    {
        $fields = @unserialize($fieldList);
        $html = '';
        $yesLbl = Lang::get('preylang.confidential.label.yes');
        $noLbl = Lang::get('preylang.confidential.label.no');
        if (is_array($fields)) {
            foreach ($fields as $field => $params) {
                $fieldName = $params['field'];
                $fieldLabel = Lang::get('preylang.activity.field.' . lcfirst($fieldName));
                $oldValue = $params['oldValue'];
                $newValue = $params['newValue'];
                if (in_array($fieldName, ['agreement', 'burnedWood', 'byVisual', 'byAudio', 'byTrack', 'excluded'])) {
                    $newValue = (int)$newValue ? $yesLbl : $noLbl;
                    $oldValue = (int)$oldValue ? $yesLbl : $noLbl;
                } elseif ($fieldName === 'excludedReason') {
                    $newExcludedReason = Setting::find($newValue);
                    $oldExcludedReason = Setting::find($oldValue);
                    $newValue = $newExcludedReason ? $newExcludedReason->name : $newValue;
                    $oldValue = $oldExcludedReason ? $oldExcludedReason->name : $oldValue;
                }
                $html .= $fieldLabel . ' : ' . $oldValue . ' =>&nbsp;' . $newValue . '<br />';
            }
            $html = rtrim($html, '<br />');
        }

        return $html;
    }
}
