<?php

namespace App\Livewire\Admin\Accounting;

use App\Modules\Accounting\Entity\AccountingDocument;
use Livewire\Component;

class EditComment extends Component
{

    public AccountingDocument $document;
    public string $comment;

    public function mount(AccountingDocument $document)
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
