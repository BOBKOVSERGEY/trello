<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeskUpdateRequest extends FormRequest
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

    public function rules()
    {
        return [
            'name' => 'required|max:255|unique:desks,name,' .$this->desk->id
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'Имя достки должно быть уникальное'
        ];
    }
}
