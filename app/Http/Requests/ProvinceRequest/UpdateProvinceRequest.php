<?php

namespace App\Http\Requests\ProvinceRequest;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProvinceRequest extends FormRequest
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
        $updateRule = Setting::returnUpdateProvinceRules();
        $updateRule['name'] = $updateRule['name'] . ',' . $this->route()->parameter('id');
        return $updateRule;
    }
}
