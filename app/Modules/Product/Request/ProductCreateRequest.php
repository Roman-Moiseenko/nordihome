<?php
declare(strict_types=1);

namespace App\Modules\Product\Request;

use Illuminate\Foundation\Http\FormRequest;

class ProductCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|unique:products',
            'name_print' => 'required|unique:products',
            'code' => 'required|unique:products',
            'category_id' => 'required',
            'brand_id' => 'required',
            'country_id' => 'required',
            'vat_id' => 'required',
            'measuring_id' => 'required',
        ];
    }

    public function messages(): array
    {
        return [

            'name.required' => 'Введите название товара',
            'name.unique' => 'Название уже существует',
            'name_print.required' => 'Введите название товара',
            'name_print.unique' => 'Название уже существует',
            'code.required' => 'Введите артикул',
            'code.unique' => 'Артикул уже существует',
            'category_id.required' => 'Выберите основную категорию',
            'brand_id.required' => 'Выберите бренд',
            'country_id.required' => 'Выберите страну происхождения',
            'vat_id.required' => 'Укажите налог',
            'measuring_id.required' => 'Обязательное поле',

        ];
    }
}
