<?php

namespace App\Modules\Content\Database\Seeders;

use App\Modules\Catalog\Entity\Group;
use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Catalog\Infrastructure\Models\Room;
use App\Modules\Content\Application\Actions\CreateMetaTemplateUseCase;
use App\Modules\Content\Application\DTOs\MetaTemplateCreateData;
use App\Modules\Content\Entity\Page;
use App\Modules\Content\Entity\PostCategory;
use App\Modules\Content\Infrastructure\Models\Post;
use App\Modules\Discount\Entity\Promotion;
use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use App\Modules\Parser\Infrastructure\Models\ParserProduct;
use Illuminate\Database\Seeder;

class MetaSeeder extends Seeder
{
    public function __construct(public readonly CreateMetaTemplateUseCase $useCase)
    {

    }
    public function run(): void
    {
        // MetaTemplate::register(Product::class);
        $dto = new MetaTemplateCreateData(
            class: Product::class,
            entity: 'catalog.product',
        );
        $this->useCase->execute($dto);

        $dto = new MetaTemplateCreateData(
            class: Category::class,
            entity: 'catalog.category',
        );
        $this->useCase->execute($dto);

        $dto = new MetaTemplateCreateData(
            class: Page::class,
            entity: 'content.page',
        );
        $this->useCase->execute($dto);

        $dto = new MetaTemplateCreateData(
            class: Post::class,
            entity: 'content.post',
        );
        $this->useCase->execute($dto);

        $dto = new MetaTemplateCreateData(
            class: PostCategory::class,
            entity: 'content.post_category',
        );
        $this->useCase->execute($dto);

        $dto = new MetaTemplateCreateData(
            class: Group::class,
            entity: 'catalog.group',
        );
        $this->useCase->execute($dto);

        $dto = new MetaTemplateCreateData(
            class: Promotion::class,
            entity: 'discount.promotion',
        );
        $this->useCase->execute($dto);
        $dto = new MetaTemplateCreateData(
            class: Room::class,
            entity: 'catalog.room',
        );
        $this->useCase->execute($dto);
        $dto = new MetaTemplateCreateData(
            class: ParserProduct::class,
            entity: 'parser.product',
        );
        $this->useCase->execute($dto);

        $dto = new MetaTemplateCreateData(
            class: ParserCategory::class,
            entity: 'parser.category',
        );
        $this->useCase->execute($dto);
    }
}
