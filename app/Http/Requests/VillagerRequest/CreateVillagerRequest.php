<?php

namespace App\Http\Requests\VillagerRequest;

use App\Models\Villager;
use Illuminate\Foundation\Http\FormRequest;

class CreateVillagerRequest extends FormRequest
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
        return Villager::createRule();
    }
}
