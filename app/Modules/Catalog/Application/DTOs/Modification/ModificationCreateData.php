<?php

namespace App\Modules\Catalog\Application\DTOs\Modification;

use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ModificationCreateData extends Data
{
    /**
     * @param int[] $attributes
     */
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $name,
        #[Required, Numeric]
        public int $productId,
        #[Required, ArrayType]
        public array $attributes,
    ) {
    }

    public static function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'min:1', 'max:255'],
            'productId'    => ['required', 'integer', 'exists:products,id'],
            'attributes'   => ['required', 'array', 'min:1', 'max:3'],
            'attributes.*' => ['integer', 'distinct', 'exists:attributes,id'],
        ];
    }
}
