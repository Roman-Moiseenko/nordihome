<?php
declare(strict_types=1);

namespace Modules\Accounting;

use App\Modules\Accounting\Entity\Currency;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\Organization;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Service\ArrivalService;
use App\Modules\Accounting\Service\MovementService;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Entity\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MovementTest extends TestCase
{

    use DatabaseTransactions;

    protected ArrivalService $arrivalService;
    protected MovementService $movementService;

    private Category $category;
    private Organization $organization;
    private Distributor $distributor;

    public function setUp(): void
    {
        parent::setUp();
        $this->arrivalService = $this->app->make('App\Modules\Accounting\Service\ArrivalService');
        $this->movementService = $this->app->make('App\Modules\Accounting\Service\MovementService');
    }

    public function testMovement()
    {
        $this->actualData();
        $product_01 = $this->product();
        $product_02 = $this->product();
        $product_03 = $this->product();

        $storage_01 = Storage::register($this->organization->id, 'склад 1', true, true);
        $storage_02 = Storage::register($this->organization->id, 'склад 2', true, true);

        //Поступления товара
        $arrival_01 = $this->arrivalService->create($this->distributor->id);
        $arrival_02 = $this->arrivalService->create($this->distributor->id);

        $arrival_01_product_01 = $this->arrivalService->add($arrival_01, $product_01->id, 10);
        $arrival_01_product_02 = $this->arrivalService->add($arrival_01, $product_02->id, 5);

        $arrival_02_product_01 = $this->arrivalService->add($arrival_02, $product_02->id,  5);
        $arrival_02_product_02 = $this->arrivalService->add($arrival_02, $product_03->id,  10);


        $this->arrivalService->completed($arrival_01);
        $this->arrivalService->completed($arrival_02);
        $product_01->refresh();
        $product_02->refresh();
        $product_03->refresh();

        self::assertEquals(10, $storage_01->getQuantity($product_01));
        self::assertEquals(5, $storage_01->getQuantity($product_02));

        self::assertEquals(5, $storage_02->getQuantity($product_02));
        self::assertEquals(10, $storage_02->getQuantity($product_03));

        self::assertEquals(10, $product_01->getCountSell());
        self::assertEquals(10, $product_02->getCountSell());
        self::assertEquals(10, $product_03->getCountSell());


        //TODO Добавить товар в резерв

        //Перемещение
        ///Черновик
        $movement = $this->movementService->create(['number' => '01', 'storage_out' => $storage_02->id, 'storage_in' => $storage_01->id]);
        $this->movementService->add($movement, $product_02->id, 5);
        $this->movementService->add($movement, $product_03->id, 5);

        ///На отправку
        $this->movementService->activate($movement);
        $movement->refresh();
        //Общее кол-во
        self::assertEquals(5, $storage_02->getQuantity($product_02));
        self::assertEquals(10, $storage_02->getQuantity($product_03));
        //Ушло из хранилища
        self::assertEquals(5, $storage_02->getDeparture($product_02));
        self::assertEquals(5, $storage_02->getDeparture($product_03));
        //Доступно
        self::assertEquals(0, $storage_02->getAvailable($product_02));
        self::assertEquals(5, $storage_02->getAvailable($product_03));

        /// На прибытие
        $this->movementService->departure($movement);
        $movement->refresh();
        self::assertEquals(5, $storage_01->getQuantity($product_02));
        self::assertEquals(0, $storage_01->getQuantity($product_03));
        //Прибывает в хранилище
        self::assertEquals(5, $storage_01->getArrival($product_02));
        self::assertEquals(5, $storage_01->getArrival($product_03));
        //Доступно
        self::assertEquals(5, $storage_01->getAvailable($product_02));
        self::assertEquals(0, $storage_01->getAvailable($product_03));


        ///Прибыл/Завершено
        $this->movementService->arrival($movement);
        $movement->refresh();
        $storage_01->refresh();
        $storage_02->refresh();
        $product_02->refresh();

        self::assertEquals(10, $storage_01->getAvailable($product_01));
        self::assertEquals(10, $storage_01->getAvailable($product_02));
        self::assertEquals(5, $storage_01->getAvailable($product_03));

        self::assertEquals(0, $storage_02->getAvailable($product_02));
        self::assertEquals(5, $storage_02->getAvailable($product_03));
        self::assertEquals(10, $product_02->getCountSell());
    }


    protected function product(): Product
    {
        $random = rand(1, 999);
        $product = Product::register('Товар ' . $random, '001.00-' . $random, $this->category->id);
        $product->setPrice(rand(1, 99) * 100);
        $product->published = true;
        //$product->setCountSell(0);
        $product->save();
        return $product;
    }

    private function actualData()
    {
        $staff = Admin::find(1);
        $this->actingAs($staff, 'admin');
        $this->category = Category::register('Main');
        $this->organization = Organization::register('Company');
        $currency = Currency::register('Валюта', 'Вл', 100);
        $this->distributor = Distributor::register('Поставщик', $currency->id);
    }
}
