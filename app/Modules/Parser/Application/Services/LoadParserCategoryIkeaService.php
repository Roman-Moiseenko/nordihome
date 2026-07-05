<?php

namespace App\Modules\Parser\Application\Services;

use App\Modules\Base\Service\HttpPage;
use App\Modules\Base\Service\TranslateService;
use App\Modules\Parser\Application\Actions\Category\CreateParserCategoryUseCase;
use App\Modules\Parser\Application\DTOs\Category\ParserCategoryCreateData;
use App\Modules\Parser\Infrastructure\Jobs\LoadCategoryIkeaJob;
use App\Modules\Parser\Infrastructure\Persistence\ParserCategoryRepository;
use App\Modules\Shared\Application\DTOs\JobPhotoLoadData;
use App\Modules\Shared\Domain\Entities\UserPermission;
use App\Modules\Shared\Infrastructure\Job\LoadPhotoByUrlJob;

readonly class LoadParserCategoryIkeaService
{
    private UserPermission $userPermission;
    const string API_URL_CATEGORIES = 'https://www.ikea.com/pl/pl/navigation/catalog-products-slim.json?cb=85p6e40iet';

    public function __construct(
        private HttpPage                    $httpPage,
        private TranslateService            $translate,
        private CreateParserCategoryUseCase $createCategoryUseCase,
        private ParserCategoryRepository $parserCategoryRepository,
    )
    {
        $this->userPermission = new UserPermission(
            null,
            ['admin'],
            ['storage.photo.upload', 'parser.category.create', 'parser.category.edit']
        );
    }

    public function load(): void
    {

        $data = $this->httpPage->getPage(self::API_URL_CATEGORIES);

        foreach (json_decode($data, true) as $categoryData) {
            LoadCategoryIkeaJob::dispatch($categoryData, null);
            //$this->addCategory($categoryData);
        }
    }

    //FIXME Сделать через очередь
    public function addCategory($categoryData, $parent_id = null): void
    {
        if (!is_null($this->parserCategoryRepository->getByIkeaId($categoryData['id']))) return;

        $name = $this->translate->translate($categoryData['name']);
        $dto = new ParserCategoryCreateData(
            name: $name,
            ikeaId: $categoryData['id'],
            parentId: $parent_id,
        );
        $category = $this->createCategoryUseCase->execute($dto, $this->userPermission);
        if (isset($categoryData['im'])) {
            $dto = new JobPhotoLoadData(
                imageableId: $category->id,
                modelType: 'parser.category',
                type: 'image',
                url: $categoryData['im']
            );
            LoadPhotoByUrlJob::dispatch($dto, new UserPermission(null, ['admin'], ['storage.photo.unload']));
        }
        if (isset($categoryData['subs']))
            foreach ($categoryData['subs'] as $child) {
                LoadCategoryIkeaJob::dispatch($child, $category->id);
                //$this->addCategory($child, $category->id);
            }
    }

}
