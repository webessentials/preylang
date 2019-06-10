<?php

namespace App\Http\Requests\VillagerRequest;

use App\Models\Villager;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVillagerRequest extends FormRequest
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
        $rules = Villager::updateRule();
        $rules['device_imei'] = $rules['device_imei'] . ',device_imei,' . $this->route()->parameter('villagerId');
        return $rules;
    }
}
