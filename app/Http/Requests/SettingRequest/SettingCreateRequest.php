<?php

namespace App\Http\Requests\SettingRequest;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class SettingCreateRequest extends FormRequest
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
        return $rule;
    }
}
