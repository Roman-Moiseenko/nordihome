<?php

namespace App\Modules\Parser\Application\Services;

use App\Modules\Base\Service\HttpPage;
use App\Modules\Base\Service\TranslateService;
use App\Modules\Parser\Application\Actions\Category\CreateParserCategoryUseCase;
use App\Modules\Parser\Application\DTOs\Category\ParserCategoryCreateData;
use App\Modules\Parser\Infrastructure\Jobs\LoadCategoryIkeaJob;
use App\Modules\Parser\Infrastructure\Persistence\ParserCategoryRepository;
use App\Modules\Setting\Entity\Settings;
use App\Modules\Shared\Application\Actions\GetPhotoByEntityUseCase;
use App\Modules\Shared\Application\DTOs\JobPhotoLoadData;
use App\Modules\Shared\Application\DTOs\Photo\PhotoByEntityData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Domain\ValueObjects\QueueName;
use App\Modules\Shared\Infrastructure\Job\LoadPhotoByUrlJob;

readonly class LoadParserCategoryIkeaService
{
    private UserPermission $userPermission;
    private bool $isProxy;
    const string API_URL_CATEGORIES = 'https://www.ikea.com/pl/pl/navigation/catalog-products-slim.json?cb=85p6e40iet';

    public function __construct(
        private HttpPage                    $httpPage,
        private TranslateService            $translate,
        private CreateParserCategoryUseCase $createCategoryUseCase,
        private ParserCategoryRepository    $parserCategoryRepository,
        private GetPhotoByEntityUseCase     $getPhotoByEntityUseCase,
    )
    {
        $this->userPermission = new UserPermission(
            null,
            ['admin'],
            ['storage.photo.upload', 'parser.category.create', 'parser.category.edit']
        );

        $settings = app()->make(Settings::class);
        $this->isProxy = $settings->parser->with_proxy ?? false;
    }

    public function load(): void
    {
        $data = $this->httpPage->getPage(self::API_URL_CATEGORIES);

        // \Log::warning('data', [$data]);
        foreach (json_decode($data, true) as $categoryData) {
            LoadCategoryIkeaJob::dispatch($categoryData, null); //$this->addCategory($categoryData);
        }
    }

    public function addCategory($categoryData, $parent_id = null): void
    {

        $category = $this->parserCategoryRepository->getByIkeaId($categoryData['id']);

        if (is_null($category)) {
            $name = $this->translate->translate($categoryData['name']);
            $dto = new ParserCategoryCreateData(
                name: $name,
                ikeaId: $categoryData['id'],
                parentId: $parent_id,
            );
            $category = $this->createCategoryUseCase->execute($dto, $this->userPermission);
            $photo = null;
        } else {
            $dto = new PhotoByEntityData($category->id, 'parser.category', 'image');
            $photo = $this->getPhotoByEntityUseCase->execute($dto);
        }

        if (empty($photo) && !is_null($categoryData['im'])) {
            $dto = new JobPhotoLoadData(
                imageableId: $category->id,
                modelType: 'parser.category',
                type: 'image',
                url: $categoryData['im'],
                isProxy: $this->isProxy,
            );
            LoadPhotoByUrlJob::dispatch($dto)->onQueue(QueueName::PHOTO);
        }


        if (isset($categoryData['subs']))
            foreach ($categoryData['subs'] as $child) {
                LoadCategoryIkeaJob::dispatch($child, $category->id);
            }
    }
}
