<?php

namespace App\Http\Requests\SettingRequest;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class SettingUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rule = Setting::returnCreateUpdateSettingRules();
        $rule['name'] = $rule['name'] . ',' . $this->route()->parameter('id');
        return $rule;
    }
}
