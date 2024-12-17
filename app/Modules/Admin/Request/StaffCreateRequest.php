<?php
declare(strict_types=1);

namespace App\Modules\Admin\Request;

use Illuminate\Foundation\Http\FormRequest;

class StaffCreateRequest extends FormRequest
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
            'name' => 'required|unique:admins|max:255',
            'phone' => 'required',
            'password' => 'required|min:6',
            'surname' => 'required|max:33',
            'firstname' => 'required|max:33',
            'post' => 'max:33',
            'role' => 'required|max:33',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Логин - обязательное поле',
            'name.unique' => 'Такой логин уже есть',
            'phone.required' => 'Укажите телефон',
            'password.required' => 'Введите пароль',
            'password.min' => 'Введите короткий',

            'surname.required' => 'Укажите фамилию',
            'firstname.required' => 'Введите имя',
            'post.max' => 'Слишком длинное поле',
            'role.required' => 'Выберите роль сотрудника',



        ];
    }
}
