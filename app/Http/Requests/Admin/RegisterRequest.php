<?php


namespace App\Http\Requests\Admin;


use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|unique:admins|max:255',
            'email' => 'required|email|unique:admins',
            'phone' => 'required|numeric',
            'password' => 'required|string|min:6',
            'surname' => 'required|string|max:33',
            'firstname' => 'required|string|max:33',
            'secondname' => 'string|max:33',
            'post' => 'string|max:33',
            'role' => 'required|string|max:33',
        ];
    }
}
