<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VenderRequest extends FormRequest
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
        $rules = [
            "corporation_name" => "required",
            "corporation_no" => "required",
            "address" => "required",
            "files_1.*" => "mimes:pdf",
            "files_2.*" => "mimes:pdf",
            "files_3.*" => "mimes:pdf",
            "files_4.*" => "mimes:pdf",
        ];

        if ($this->route()->getName() == "vender.update") {
            $rules["email"] = "required|unique:\App\Models\Vender,email," . $this->route("vender")->vender_id;
        } else {
            $rules["email"] = "required|unique:\App\Models\Vender,email";
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            "email.unique" => "อีเมล :input นี้มีผู้ใช้งานแล้ว",
            "files_1.*.mimes" => "กรุณาเลือกไฟล์ที่มีนามสกุล pdf เท่านั้น"
        ];
    }
}
