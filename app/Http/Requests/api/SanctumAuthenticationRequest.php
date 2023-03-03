<?php

namespace App\Http\Requests\api;

use Illuminate\Foundation\Http\FormRequest;

class SanctumAuthenticationRequest extends FormRequest
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
            //
            'guard' => 'required|string|in:admin_api,supervisor_api,keeper_api,parent_api,student_api',
            'username' => 'required|string',
            'password' => 'required|string',
        ];
    }
}
