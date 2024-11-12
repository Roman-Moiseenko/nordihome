<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

interface AccountingDocumentInterface
{
    public function setComment(string $comment): void;
    public function getComment(): string;

}
