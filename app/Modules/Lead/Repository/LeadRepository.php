<?php

namespace App\Modules\Lead\Repository;

use App\Modules\Lead\Infrastructure\Models\Lead;
use App\Modules\Lead\Infrastructure\Models\LeadStatus;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Entity\Order\OrderItem;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ExpectedValues;

class LeadRepository
{

    public function getIndex(Request $request): array
    {
        $staff = auth()->user()->profileable;
        $query = Lead::where('staff_id', $staff->id);
        $query_new = Lead::where('staff_id', null);

        return [
            LeadStatus::NEW_LEAD => $this->getLeadsByStatus($query_new, LeadStatus::NEW_LEAD),
            LeadStatus::IN_WORK => $this->getLeadsByStatus($query, LeadStatus::IN_WORK), //'in_work'
            LeadStatus::NOT_DECIDED => $this->getLeadsByStatus($query, LeadStatus::NOT_DECIDED), //'not_decide'
            LeadStatus::INVOICE => $this->getLeadsByStatus($query, LeadStatus::INVOICE), //'invoice'
            LeadStatus::PAID => $this->getLeadsByStatus($query, LeadStatus::PAID), //'paid'
            LeadStatus::ASSEMBLY => $this->getLeadsByStatus($query, LeadStatus::ASSEMBLY), //'assembly'
            LeadStatus::DELIVERY => $this->getLeadsByStatus($query, LeadStatus::DELIVERY), //'delivery'
        ];

        //return Lead::get()->map(fn(Lead $lead) => $this->LeadToArray($lead))->toArray();
    }

    public function getFreeLeads(): array
    {
        $leads = Lead::where('staff_id', null)->whereHas('status', function ($query) {
            $query->where('value', LeadStatus::NEW_LEAD);
        });

        return $leads->get()->map(fn(Lead $lead) => $this->LeadToArray($lead))->toArray();
    }




    public function getMyLeads(): array
    {
        $staff = auth()->user()->profileable;
        $query = Lead::where('staff_id', $staff->id);

        return [
            LeadStatus::IN_WORK => $this->getLeadsByStatus($query, LeadStatus::IN_WORK), //'in_work'
            LeadStatus::NOT_DECIDED => $this->getLeadsByStatus($query, LeadStatus::NOT_DECIDED), //'not_decide'
            LeadStatus::INVOICE => $this->getLeadsByStatus($query, LeadStatus::INVOICE), //'invoice'
            LeadStatus::PAID => $this->getLeadsByStatus($query, LeadStatus::PAID), //'paid'
            LeadStatus::ASSEMBLY => $this->getLeadsByStatus($query, LeadStatus::ASSEMBLY), //'assembly'
            LeadStatus::DELIVERY => $this->getLeadsByStatus($query, LeadStatus::DELIVERY), //'delivery'
        ];
    }
    private function getLeadsByStatus($query, #[ExpectedValues(valuesFromClass: LeadStatus::class)] string $status)
    {
        $new_query = clone $query;
        return $new_query->whereHas('status', function ($query) use ($status) {
            $query->where('value', $status);
        })->get()->map(fn(Lead $lead) => $this->LeadToArray($lead))->toArray();
    }

    private function LeadToArray(Lead $lead): array
    {
        return array_merge($lead->toArray(), [
            'type' => $lead->getType(),
            'status' => $lead->status->value,
            'user' => is_null($lead->client) ? null : [
                'id' => $lead->client->id,
                'fullname' => $lead->client->fullname,
                'email' => $lead->client->email,
                'phone' => $lead->client->phone,
            ],
            'order' => $this->OrderToArray($lead->order),
            'items' => $lead->items()->get()->toArray(),
            'leads' => $this->getRelatedLeads($lead),
        ]);
    }

    private function OrderToArray(?Order $order):? array
    {
        if (is_null($order)) return null;
        return [
            'id' => $order->id,
            'number' => $order->number,
            'status' => $order->status->value(),
            'amount' => $order->getTotalAmount(),
            'products' => $order->items()->get()->map(fn(OrderItem $item) => [
                'code' => $item->product->code,
                'quantity' => $item->quantity,
            ])->toArray(),
            'expenses' => $order->expenses()->get()->map(fn(OrderExpense $expense) => [
                'id' => $expense->id,
                'created_at' => $expense->created_at,
                'status' => $expense->statusHTML(),
            ])->toArray(),
        ];
    }

    public function getBoards(): array
    {
        $result = [];
        foreach (LeadStatus::STATUSES as $key => $label)
        {
            if ($key < LeadStatus::CANCELED) {
                $result[$key] = $label;
            }
        }

        return $result;
    }

    private function getRelatedLeads(Lead $lead): array
    {
        $data = $lead->data;
        if (!is_array($data)) return [];
        $email = null;
        $phone = null;

        foreach ($lead->data as $key => $value) {
            if ($key == 'email') $email = $value;
            if ($key == 'phone') $phone = $value;
        }
        if (empty($email) && empty($phone)) return [];

        return Lead::where('client_id', null)->where(function ($query) use ($email, $phone) {
            if (empty($email)) {
                $query->where('data', 'LIKE', "%$phone%");
            } else {
                $query->where('data', 'LIKE', "%$email%");
                if (!empty($phone)) $query->orWhere('data', 'LIKE', "%$phone%");
            }

        })->where('id', '<>', $lead->id)->get()->map(fn(Lead $lead) => [
            'id' => $lead->id,
            'created_at' => $lead->created_at,
            'status' => $lead->status->getName(),
        ])->toArray();

    }


}
