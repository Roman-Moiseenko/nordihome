<?php
declare(strict_types=1);

namespace App\Modules\Admin\Request;

use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        //dd($this->id);
    }

    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password' => 'required|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'Пароль не может быть пустым',
            'password.min' => 'Минимальное кол-во знаков - 6',
        ];
    }
}
