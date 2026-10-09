<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs\Modification;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class ModificationRenameData extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public readonly string $name,
    ) {
    }

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:1', 'max:255'],
        ];
    }
}
