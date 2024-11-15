<?php

namespace App\Livewire\Admin\Accounting;

use Livewire\Component;

class EditComment extends Component
{

    public mixed $document;
    public string $comment;

    public function mount($document)
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
