<?php

declare(strict_types=1);

namespace App\Modules\Content\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Content\Application\Actions\Contact\CreateContactUseCase;
use App\Modules\Content\Application\Actions\Contact\IndexContactQuery;
use App\Modules\Content\Application\Actions\Contact\MoveContactUseCase;
use App\Modules\Content\Application\Actions\Contact\RemoveContactUseCase;
use App\Modules\Content\Application\Actions\Contact\ToggleContactUseCase;
use App\Modules\Content\Application\Actions\Contact\UpdateContactUseCase;
use App\Modules\Content\Application\DTOs\Contact\ContactCreateData;
use App\Modules\Content\Application\DTOs\Contact\ContactUpdateData;
use App\Modules\Content\Domain\ValueObjects\ContactChannel;
use App\Modules\Shared\Domain\Entities\UserPermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function __construct(
        private readonly IndexContactQuery $indexContactQuery,
        private readonly CreateContactUseCase $createContactUseCase,
        private readonly UpdateContactUseCase $updateContactUseCase,
        private readonly ToggleContactUseCase $toggleContactUseCase,
        private readonly MoveContactUseCase $moveContactUseCase,
        private readonly RemoveContactUseCase $removeContactUseCase,
    ) {
    }

    public function index(Request $request, UserPermission $userPermission): Response
    {
        return Inertia::render('Content/Contact/Index', [
            'contacts' => $this->indexContactQuery->execute($userPermission),
        ]);
    }

    public function store(Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = ContactCreateData::validateAndCreate($request->all());
        $this->createContactUseCase->execute($dto, $userPermission);

        return redirect()->back()->with('success', 'Контакт добавлен');
    }

    public function update(int $id, Request $request, UserPermission $userPermission): RedirectResponse
    {
        $dto = ContactUpdateData::validateAndCreate($request->all());
        $this->updateContactUseCase->execute($id, $dto, $userPermission);

        return redirect()->back()->with('success', 'Сохранено');
    }

    public function toggle(int $id, UserPermission $userPermission): RedirectResponse
    {
        $message = $this->toggleContactUseCase->execute($id, $userPermission);

        return redirect()->back()->with('success', $message);
    }

    public function up(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->moveContactUseCase->execute($id, 'up', $userPermission);

        return redirect()->back()->with('success', 'Сохранено');
    }

    public function down(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->moveContactUseCase->execute($id, 'down', $userPermission);

        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(int $id, UserPermission $userPermission): RedirectResponse
    {
        $this->removeContactUseCase->execute($id, $userPermission);

        return redirect()->back()->with('success', 'Контакт удалён');
    }

    public function listContacts(): JsonResponse
    {
        return response()->json(ContactChannel::CONTACTS);
    }
}
