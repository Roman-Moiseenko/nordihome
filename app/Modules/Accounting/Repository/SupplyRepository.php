<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Repository;

use App\Modules\Accounting\Entity\SupplyDocument;
use App\Modules\Accounting\Entity\SupplyStack;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

class SupplyRepository
{
    public function getIndex(Request $request, &$filters): Arrayable
    {
        $query = SupplyDocument::orderByDesc('created_at');
        $filters = [];

        if (!is_null($begin = $request->date('date_from'))) {
            $filters['date_from'] = $begin->format('Y-m-d');
            $query->where('created_at', '>', $begin);
        }
        if (!is_null($end = $request->date('date_to'))) {
            $filters['date_to'] = $end->format('Y-m-d');
            $query->where('created_at', '<=', $end);
        }
        if (($request->has('draft')) > 0) {
            $filters['draft'] = true;
            $query->where('completed', false);
        }
        if (($distributor = $request->integer('distributor')) > 0) {
            $filters['distributor'] = $distributor;
            $query->where('distributor_id', $distributor);
        }
        if (($comment = $request->string('comment')->trim()->value()) != '') {
            $filters['comment'] = $comment;
            $query->where('comment', 'like', "%$comment%");
        }
        if (($staff_id = $request->integer('staff_id')) > 0) {
            $filters['staff_id'] = $staff_id;
            $query->where('staff_id', $staff_id);
        }

        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('p', 20))
            ->withQueryString()
            ->through(function(SupplyDocument $document) {
                return $this->SupplyToArray($document);
            });
    }

    public function getStacks(Request $request, &$filters)
    {
        $query = SupplyStack::where('supply_id', null);
        $filters = [];
        /*
        if (($founded = $request->string('draft')) != '') {
            $filters['founded'] = $founded;
            if ($founded == 'order') $query->where('completed', false);
            if ($founded == 'staff') $query->where('completed', false);
        }*/

        if (($brand = $request->integer('brand')) > 0) {
            $filters['brand'] = $brand;
            $query->whereHas('product', function ($query) use($brand) {
                $query->where('brand_id', $brand);
            });
        }
        if (($staff_id = $request->integer('staff_id')) > 0) {
            $filters['staff_id'] = $staff_id;
            $query->where('staff_id', $staff_id);
        }
        if (count($filters) > 0) $filters['count'] = count($filters);

        return $query->paginate($request->input('p', 20))
            ->withQueryString()
            ->through(function(SupplyStack $stack) {
                return [
                    'id' => $stack->id,
                    'name' => $stack->product->name,
                    'code' => $stack->product->code,
                    'quantity' => $stack->quantity,
                    'founded' => $stack->comment,
                    'order_id' => !is_null($stack->orderItem) ? $stack->orderItem->order_id : null,
                    'created_at' => $stack->created_at,
                    'brand' => $stack->product->brand->name,
                    'staff' => !is_null($stack->staff) ? $stack->staff->fullname->getFullName() : '-',
                ];
            });
    }

    public function SupplyToArray(SupplyDocument $document): array
    {
        return [
            'id' => $document->id,
            'created_at' => $document->created_at,
            'date' => $document->htmlDate(),
            'number' => $document->number,
            'completed' => $document->isCompleted(),
            'distributor_id' => $document->distributor_id,
            'distributor' => $document->distributor->name,
            'quantity' => $document->getQuantity(),
            'comment' => $document->comment,
            'staff' => !is_null($document->staff) ? $document->staff->fullname->getFullName() : '-',
            //'is_delete' => !$document->isCompleted(),
            'exchange_fix' => $document->exchange_fix,
            'currency' => $document->distributor->currency->sign,
            'incoming_number' => $document->incoming_number,
            'incoming_at' => $document->incoming_at,
        ];
    }

    public function SupplyWithToArray(SupplyDocument $document): array
    {
        $withData = [
            'products' => $document->products()->with('product')->get()->toArray(),
        ];

        return array_merge($this->SupplyToArray($document), $withData);
    }
}
