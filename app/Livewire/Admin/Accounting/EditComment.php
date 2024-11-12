<?php

namespace App\Livewire\Admin\Accounting;

use App\Modules\Accounting\Entity\AccountingDocumentInterface;
use Livewire\Component;

class EditComment extends Component
{

    public AccountingDocumentInterface $document;
    public string $comment;

    public function mount(AccountingDocumentInterface $document)
    {
        $this->document = $document;
        $this->comment = $document->getComment();
    }

    public function save()
    {
        $this->document->setComment($this->comment);
    }

    public function render()
    {
        return view('livewire.admin.accounting.edit-comment');
    }
}
