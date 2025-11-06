<?php

namespace App\Modules\Page\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Base\Entity\Photo;
use App\Modules\Page\Entity\Gallery;
use App\Modules\Page\Repository\GalleryRepository;
use App\Modules\Page\Service\GalleryService;
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
        $this->middleware(['auth:admin', 'can:options']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $galleries = $this->repository->getIndex($request);
        //dd($galleries);
        return Inertia::render('Page/Gallery/Index', [
                'galleries' => $galleries,
            ]
        );
    }

    public function show(Gallery $gallery): Response
    {
        return Inertia::render('Page/Gallery/Show', [
                'gallery' => $this->repository->GalleryWithToArray($gallery),
            ]
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $gallery = $this->service->createGallery($request);
        return redirect()->route('admin.page.gallery.show', $gallery)->with('success', 'Галерея создана');
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
}
