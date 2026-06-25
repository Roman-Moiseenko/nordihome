<?php


namespace App\Modules\Admin\Request;


use App\Modules\Auth\Infrastructure\Models\Staff;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property Staff $staff
 */
class StaffUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

 public function rules(): array
 {
     return [
         'name' => ['required', Rule::unique('admins')->ignore($this->staff->id),],
        // 'email' => ['email', 'required', Rule::unique('admins')->ignore($this->staff->id),],
        // 'phone' => 'required',
         'surname' => 'required|string|max:33',
         'firstname' => 'required|string|max:33',
         'secondname' => 'string|max:33',
         'post' => 'string|max:33',
         'role' => 'string|max:33',
     ];
 }
}
