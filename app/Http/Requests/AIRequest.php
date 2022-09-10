<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AIRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            // "search" => "required|min:6|max:50",
            // "lang" => "required|min:2|max:5",
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            "search.required" => "Search field can't be empty.",
            "search.min" => "Search field must be min 6 and maximum 50 character.",
            "search.max" =>  "Search field must be min 6 and maximum 50 character.",
            "lang.required" => "Lang field can't be empty.",
            "lang.max" =>  "Search field must be min 2 and maximum 5 character.",
            "lang.max" =>  "Search field must be min 2 and maximum 5 character.",
        ];
    }


    /**
     * @throws \HttpResponseException When the validation rules is not valid
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors.',
            'data'      => [],
            'error'     => $validator->errors(),
        ]));
    }
}
