<?php

namespace TokoBot\Http\Requests;

use TokoBot\Core\Validation\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'password' => 'required|min:6'
        ];
    }
}
