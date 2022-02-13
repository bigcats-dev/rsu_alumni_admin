<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecruitmentRequest extends FormRequest
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
            "company" => "required",
            "business_type" => "required",
            "position" => "required",
            "number_of_applications" => "required",
            "nature_of_work" => "required",
            "qualification" => "required",
            "gender" => "required",
            "age" => "required",
            "education" => "required",
            "experience" => "required",
            "salary" => "required",
            "workplace" => "required",
            "end_date" => "required",
            "contact_name" => "required",
            "tel" => "required",
            "email" => "required",
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }
}
