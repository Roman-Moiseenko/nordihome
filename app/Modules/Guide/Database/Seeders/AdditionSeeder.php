<?php

namespace App\Modules\Guide\Database\Seeders;

use App\Modules\Guide\Application\Actions\Addition\CreateAdditionUseCase;
use App\Modules\Guide\Application\DTOs\Addition\AdditionCreateData;
use App\Modules\Guide\Domain\ValueObjects\AdditionType;
use App\Modules\Guide\Infrastructure\Models\Addition;
use App\Modules\Order\Entity\Addition\AssemblyCalculate;
use App\Modules\Order\Entity\Addition\DeliveryPolandCalculate;
use App\Modules\Order\Entity\Addition\PackingCalculate;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Database\Seeder;

class AdditionSeeder extends Seeder
{
    public function __construct(
        private readonly CreateAdditionUseCase $createAdditionUseCase,
    )
    {
    }
    public function run(): void
    {
        $array = [
            new AdditionCreateData(
                name: 'Сборка мебели 15%',
                base: 15,
                type: AdditionType::ASSEMBLY,
                class: AssemblyCalculate::class,
                slug:'assembly-15',
            ),
            new AdditionCreateData(
                name: 'Упаковка товара',
                base: 1,
                type: AdditionType::PACKING,
                class: PackingCalculate::class,
                slug: 'packing',
            ),
            new AdditionCreateData(
                name: 'Доставка из Польши',
                base: 0,
                type: AdditionType::DELIVERY,
                class: DeliveryPolandCalculate::class,
                slug: 'poland',
            ),
            new AdditionCreateData(
                name: 'Доставка в Россию',
                base: 0,
                type: AdditionType::DELIVERY,
                class: null,
                slug: 'russia',
                manual: true,
            ),
            new AdditionCreateData(
                name: 'Доставка по региону',
                base: 0,
                type: AdditionType::DELIVERY,
                class: null,
                slug: 'koenig',
                manual: true,
            ),
           // ['name' => 'Упаковка товара', 'base' => 1, 'type' => AdditionType::PACKING, 'class' => PackingCalculate::class, 'slug' => 'packing'],
            //['name' => 'Доставка из Польши', 'base' => 0, 'type' => AdditionType::DELIVERY, 'class' => DeliveryPolandCalculate::class, 'slug' => 'poland'],
            //['name' => 'Доставка в Россию', 'base' => 0, 'type' => AdditionType::DELIVERY, 'class' => null, 'slug' => 'russia', 'manual' => true],
            //['name' => 'Доставка по региону', 'base' => 0, 'type' => AdditionType::DELIVERY, 'class' => null, 'slug' => 'koenig', 'manual' => true],
        ];
        $permission = new UserPermission(null, [], ['guide.guide.create']);
        foreach ($array as $item) {
            $this->createAdditionUseCase->execute($item, $permission);
        }
    }
}
