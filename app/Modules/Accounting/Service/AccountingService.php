<?php

namespace App\Modules\Accounting\Service;

use App\Modules\Accounting\Entity\AccountingDocument;
use App\Modules\Product\Entity\Product;
use Illuminate\Support\Facades\DB;

abstract class AccountingService
{
    final public function destroy(AccountingDocument $document): void
    {
        if ($document->isCompleted()) throw new \DomainException('Документ проведен');

        DB::transaction(function () use ($document) {
            $document->delete(); //Удаление каскадно связанных документов
        });
    }

    final public function restore(AccountingDocument $document): void
    {
        DB::transaction(function () use ($document) {
            $document->restore();//Восстановление каскадно связанных документов
        });

    }

    final public function fullDestroy(AccountingDocument $document): void
    {
        DB::transaction(function () use ($document) {
            $document->forceDelete();//Удаление каскадно связанных документов
        });
    }
}
