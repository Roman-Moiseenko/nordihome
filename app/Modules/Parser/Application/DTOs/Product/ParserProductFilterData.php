<?php

declare(strict_types=1);

namespace App\Modules\Parser\Application\DTOs\Product;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

class ParserProductFilterData extends Data
{
    public function __construct(
        public readonly ?string $code = null,
        public readonly ?string $show = null,
        public readonly int $perPage = 20,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            code: $request->string('code')->trim()->value() ?: null,
            show: $request->input('show'),
            perPage: $request->integer('size', 20),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $filters = [];

        if ($this->code !== null) {
            $filters['code'] = $this->code;
        }

        if ($this->show !== null) {
            $filters['show'] = $this->show;
        }

        if (count($filters) > 0) {
            $filters['count'] = count($filters);
        }

        return $filters;
    }
}
