<?php

namespace App\Modules\Page\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Page\Entity\Post;
use App\Modules\Page\Entity\PostCategory;
use App\Modules\Page\Repository\PostRepository;
use App\Modules\Page\Repository\TemplateRepository;
use App\Modules\Page\Service\PostService;
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
    )
    {
        $this->middleware(['auth:admin', 'can:options']);
        $this->service = $service;
        $this->templates = $templates;
        $this->repository = $repository;
    }

    public function categories(Request $request): Response
    {
        $categories = $this->repository->getCategories($request);
        $templates = $this->templates->getTemplates('posts');

        return Inertia::render('Page/Post/Categories', [
            'categories' => $categories,
            'templates' => $templates,
        ]);
    }

    public function category(PostCategory $category, Request $request): Response
    {
        $templates = $this->templates->getTemplates('posts');
        $post_templates = $this->templates->getTemplates('post');

        return Inertia::render('Page/Post/Category', [
            'category' => Inertia::always($this->repository->CategoryWithToArray($category)),
            'templates' => $templates,
            'post_templates' => $post_templates,
            'tiny_api' => config('shop.tinymce'),
        ]);
    }

    public function category_create(Request $request): RedirectResponse
    {
        $category = $this->service->createCategory($request);
        return redirect()->route('admin.page.post-category.show', $category)->with('success', 'Рубрика создана');
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

    public function post(Post $post): Response
    {
        $templates = $this->templates->getTemplates('post');

        return Inertia::render('Page/Post/Post', [
            'post' => Inertia::always($this->repository->PostWithToArray($post)),
            'templates' => $templates,
            'tiny_api' => config('shop.tinymce'),
        ]);
    }

    public function post_create(Request $request): RedirectResponse
    {
        $category = PostCategory::find($request->integer('category_id'));
        $post = $this->service->createPost($category, $request);

        return redirect()->route('admin.page.post.show', $post)->with('success', 'Запись создана');
    }

    public function post_set_info(Post $post, Request $request): RedirectResponse
    {
        $this->service->setInfoPost($post, $request);
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
