<?php

namespace App\Modules\Content\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Content\Application\Actions\ContentBlock\ListContentBlockByContainerUseCase;
use App\Modules\Content\Application\Actions\Post\CreatePostUseCase;
use App\Modules\Content\Application\Actions\Post\IndexPostUseCase;
use App\Modules\Content\Application\Actions\Post\RemovePostUseCase;
use App\Modules\Content\Application\Actions\Post\UpdatePostUseCase;
use App\Modules\Content\Application\Actions\Post\ViewPostUseCase;
use App\Modules\Content\Application\DTOs\ContentBlock\ContentBlockContainerData;
use App\Modules\Content\Application\DTOs\Post\PostUpdateData;
use App\Modules\Content\Application\DTOs\Post\PostViewData;
use App\Modules\Content\Domain\ValueObjects\ContainerType;
use App\Modules\Content\Entity\PostCategory;
use App\Modules\Content\Infrastructure\Models\Post;
use App\Modules\Content\Repository\PostRepository;
use App\Modules\Content\Repository\TemplateRepository;
use App\Modules\Content\Service\PostService;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{

    private PostService $service;
    private PostRepository $repository;
    private TemplateRepository $templates;

    public function __construct(
        PostService        $service,
        TemplateRepository $templates,
        PostRepository     $repository,
        private readonly ListContentBlockByContainerUseCase $listContentBlockByContainerUseCase,
        private readonly ViewPostUseCase $viewPostUseCase,
        private readonly UpdatePostUseCase $updatePostUseCase,
        private readonly IndexPostUseCase $postUseCase,
        private readonly CreatePostUseCase $createPostUseCase,
        private readonly RemovePostUseCase $removePostUseCase,
    )
    {
        $this->service = $service;
        $this->templates = $templates;
        $this->repository = $repository;
    }

    public function categories(Request $request): Response
    {
        $categories = $this->repository->getCategories($request);
        $templates = $this->templates->getTemplates('posts');

        return Inertia::render('Content/Post/Categories', [
            'categories' => $categories,
            'templates' => $templates,
        ]);
    }

    public function category(PostCategory $category, Request $request): Response
    {
        $templates = $this->templates->getTemplates('posts');
        $post_templates = $this->templates->getTemplates('post');

        return Inertia::render('Content/Post/Category', [
            'category' => Inertia::always($this->repository->CategoryWithToArray($category)),
            'templates' => $templates,
            'post_templates' => $post_templates,
            'tiny_api' => config('shop.tinymce'),
        ]);
    }

    public function category_create(Request $request): RedirectResponse
    {
        $category = $this->service->createCategory($request);
        return redirect()->route('admin.content.post-category.show', $category)->with('success', 'Рубрика создана');
    }

    public function category_set_info(PostCategory $category, Request $request): RedirectResponse
    {
        $this->service->setInfoCategory($category, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function category_destroy(PostCategory $category): RedirectResponse
    {
        $this->service->destroyCategory($category);
        return redirect()->back()->with('success', 'Удалено');
    }

    public function posts(Request $request)
    {

    }

    public function post(int $id, UserPermission $userPermission): Response
    {
        //$templates = $this->templates->getTemplates('post');
        $post = $this->viewPostUseCase->execute($id, $userPermission);

        $dto = new ContentBlockContainerData($post->id, ContainerType::POST);
        $blocks = $this->listContentBlockByContainerUseCase->execute($dto);


        return Inertia::render('Content/Post/Post', [
            'post' => Inertia::always(PostViewData::fromEntity($post)), //Заменить на useCase $this->repository->PostWithToArray($post)
            //'templates' => $templates, //Удалить
            'tiny_api' => config('shop.tinymce'), //Удалить
            'blocks' => $blocks,
        ]);
    }

    public function post_create(Request $request): RedirectResponse
    {
        $category = PostCategory::find($request->integer('category_id'));
        $post = $this->service->createPost($category, $request);

        return redirect()->route('admin.content.post.show', $post)->with('success', 'Запись создана');
    }

    public function post_set_info(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = PostUpdateData::validateAndCreate($request->all());
        $this->updatePostUseCase->execute($id, $dto, $userPermission);
        //$this->service->setInfoPost($post, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function post_destroy(Post $post): RedirectResponse
    {
        $this->service->destroyPost($post);
        return redirect()->back()->with('success', 'Удалено');
    }

    public function post_toggle(Post $post): RedirectResponse
    {
        $message = $this->service->togglePost($post);
        return redirect()->back()->with('success', $message);
    }

    public function post_set_text(Post $post, Request $request): JsonResponse
    {
        $this->service->setTextPost($post, $request);
        return \response()->json(true);
    }


}
