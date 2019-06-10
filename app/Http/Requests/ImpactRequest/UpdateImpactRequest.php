<?php

namespace App\Http\Requests\ImpactRequest;

use App\Models\Impact;
use Illuminate\Foundation\Http\FormRequest;

class UpdateImpactRequest extends FormRequest
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
        $updateRule = Impact::returnUpdateImpactRules();
        return $updateRule;
    }
}
