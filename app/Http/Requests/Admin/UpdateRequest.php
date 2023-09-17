<?php


namespace App\Http\Requests\Admin;


use App\Entity\Admin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property Admin $staff
 */
class UpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

 public function rules(): array
 {
     return [
         'name' => ['required', Rule::unique('admins')->ignore($this->staff->id),],
         'email' => ['email', 'required', Rule::unique('admins')->ignore($this->staff->id),],
         'phone' => 'required|numeric',
         'surname' => 'required|string|max:33',
         'firstname' => 'required|string|max:33',
         'secondname' => 'string|max:33',
         'post' => 'string|max:33',
         'role' => 'string|max:33',
     ];
 }
}
