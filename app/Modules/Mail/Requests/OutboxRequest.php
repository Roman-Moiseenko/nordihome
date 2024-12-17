<?php

namespace App\Modules\Mail\Requests;

use App\Modules\Mail\Entity\Outbox;
use Illuminate\Foundation\Http\FormRequest;

class OutboxRequest extends FormRequest
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
            'emails' => ['required', 'array'],
            'subject' => ['required', 'string'],
        ];
    }
}
