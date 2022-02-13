<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainingNewRequest extends FormRequest
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
        return [
            "title" => "required",
            "introduction" => "required",
            "detail" => "required",
            "start_date" => "required",
            "end_date" => "required"
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            "title.required" => "A title is required",
            "introduction.required" => "A introduction is required",
            "detail.required" => "A detail is required",
            "start_date.required" => "A start date is required",
            "end_date.required" => "A end date is required",
        ];
    }
}
