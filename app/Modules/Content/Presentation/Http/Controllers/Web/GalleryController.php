<?php

namespace App\Modules\Content\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Content\Infrastructure\Models\Gallery;
use App\Modules\Content\Repository\GalleryRepository;
use App\Modules\Content\Service\GalleryService;
use App\Modules\Shared\Infrastructure\Models\Photo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GalleryController extends Controller
{
    private GalleryService $service;
    private GalleryRepository $repository;

    public function __construct(
        GalleryService    $service,
        GalleryRepository $repository,
    )
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $galleries = $this->repository->getIndex($request);
        //dd($galleries);
        return Inertia::render('Content/Gallery/Index', [
                'galleries' => $galleries,
            ]
        );
    }

    public function show(Gallery $gallery): Response
    {
        return Inertia::render('Content/Gallery/Show', [
                'gallery' => $this->repository->GalleryWithToArray($gallery),
            ]
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $gallery = $this->service->createGallery($request);
        return redirect()->route('admin.content.gallery.show', $gallery)->with('success', 'Галерея создана');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        $this->service->deleteGallery($gallery);
        return redirect()->back()->with('success', 'Галерея удалена');
    }

    public function set_info(Gallery $gallery, Request $request): RedirectResponse
    {
        $this->service->setInfo($gallery, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function image_del(Photo $photo): RedirectResponse
    {
        $this->service->delPhoto($photo);
        return redirect()->back()->with('success', 'Удалено');
    }

    public function image_set(Photo $photo, Request $request): RedirectResponse
    {
        $this->service->setPhoto($photo, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function image_add(Gallery $gallery, Request $request): JsonResponse
    {
        $photo = $this->service->addPhoto($gallery, $request);
        return \response()->json([
            'id' => $photo->id,
            'url' => $photo->getUploadUrl(),
        ]);
    }

    public function all_images(Request $request): JsonResponse
    {
        $images = $this->repository->AllImages($request);
        return \response()->json($images);
    }

    /**
     * Возвращает все галереи (только данные, без изображений) для виджета выбора фото.
     * Список изображений галереи запрашивается отдельно через admin.photo.get-by-entity.
     */
    public function get_tree(): JsonResponse
    {
        $galleries = Gallery::orderBy('name')
            ->get()
            ->map(fn(Gallery $gallery) => [
                'id' => $gallery->id,
                'name' => $gallery->name,
                'slug' => $gallery->slug,
            ]);

        return response()->json($galleries);
    }

    /**
     * Возвращает id галереи "Виджет" (widget), создавая её при необходимости.
     * Используется как галерея по умолчанию для загрузки изображений в ImagePicker.
     */
    public function widget_id(): JsonResponse
    {
        $gallery = Gallery::firstOrCreate(
            ['slug' => 'widget'],
            ['name' => 'Виджет']
        );

        return response()->json($gallery->id);
    }

    /**
     * Загрузить изображение в галерею "Виджет" (widget) и вернуть данные фото.
     */
    public function upload_to_widget(Request $request): JsonResponse
    {
        $file = $request->file('file');
        if (is_null($file)) {
            return response()->json(['message' => 'Нет файла'], 422);
        }

        $gallery = Gallery::firstOrCreate(
            ['slug' => 'widget'],
            ['name' => 'Виджет']
        );

        $photo = $this->service->addPhoto($gallery, $request);

        return response()->json([
            'id' => $photo->id,
            'url' => $photo->getUploadUrl(),
            'alt' => $photo->alt,
            'title' => $photo->title,
            'description' => $photo->description,
        ]);
    }

    /**
     * Установить alt/title/description для фото (из окна выбора изображения виджета).
     */
    public function image_set_widget(Photo $photo, Request $request): JsonResponse
    {
        $photo->alt = $request->string('alt')->trim()->value();
        $photo->title = $request->string('title')->trim()->value();
        $photo->description = $request->string('description')->trim()->value();
        $photo->save();

        return response()->json(['message' => 'Сохранено']);
    }
}
