<?php

namespace App\Modules\Mail\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Mail\Entity\Outbox;
use App\Modules\Mail\Mailable\TestMail;
use App\Modules\Mail\Requests\OutboxRequest;
use App\Modules\Mail\Repository\OutboxRepository;
use App\Modules\Mail\Service\OutboxService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OutboxController extends Controller
{
    private OutboxService $service;
    private OutboxRepository $repository;
    private mixed $tiny_api;

    public function __construct(OutboxService $service, OutboxRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->tiny_api = config('shop.tinymce');
    }

    public function index(Request $request)
    {
        $outboxes = $this->repository->getIndex($request, $filters);

        return Inertia::render('Mail/Outbox/Index', [
                'outboxes' => $outboxes,
                'filters' => $filters,
            ]
        );
    }

    public function create(Request $request)
    {
        return Inertia::render('Mail/Outbox/Create', [
            'tiny_api' => $this->tiny_api,
            'email' => $request->string('email')->value(),
            'subject' => $request->string('subject')->value(),
        ]);
    }

    public function store(OutboxRequest $request)
    {
        $request->validated();
        $outbox = $this->service->create($request);

        if ($request->boolean('send')) {
            $flash = 'Письмо поставлено в очередь на отправку';
        } else {
            $flash = 'Новое письмо создано.';
        }
        return redirect()
            ->route('admin.mail.outbox.show', $outbox)
            ->with('success', $flash);
    }

    public function show(Outbox $outbox)
    {
        return Inertia::render('Mail/Outbox/Show', [
                'outbox' => $outbox,
            ]
        );
    }

    public function edit(Outbox $outbox)
    {
        return Inertia::render('Mail/Outbox/Edit', [
            'outbox' => $outbox,
            'tiny_api' => $this->tiny_api,
        ]);
    }

    public function send(Outbox $outbox)
    {
        $this->service->send_mail($outbox);
        return redirect()->back()->with('success', 'Письмо поставлено в очередь на отправку');
    }

    public function update(OutboxRequest $request, Outbox $outbox)
    {
        $request->validated();
        $this->service->update($outbox, $request);
        return redirect()
            ->route('admin.mail.outbox.show', $outbox)
            ->with('success', 'Сохранение прошло успешно');
    }

    public function delete_attachment(Request $request, Outbox $outbox)
    {
        $this->service->delete_attachment($outbox, $request);
        return redirect()->back()->with('success', 'Удаление файла прошло успешно');
    }

    public function destroy(Outbox $outbox)
    {
        $this->service->destroy($outbox);
        return redirect()->back()->with('success', 'Удаление прошло успешно');
    }

    public function attachment(Request $request)
    {
        $path = storage_path('app/');
        ob_end_clean();
        ob_start();
        return response()->download($path .
            $request->string('file')->value());
    }
}
