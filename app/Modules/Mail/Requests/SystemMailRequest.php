<?php

namespace App\Modules\Mail\Requests;

use App\Modules\Mail\Entity\SystemMail;
use Illuminate\Foundation\Http\FormRequest;

class SystemMailRequest extends FormRequest
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
            'name' => ['required', 'string'],
        ];
    }
}
