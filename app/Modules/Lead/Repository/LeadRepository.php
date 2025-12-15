<?php

namespace App\Modules\Lead\Repository;

use App\Modules\Lead\Entity\Lead;
use App\Modules\Lead\Entity\LeadStatus;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ExpectedValues;

class LeadRepository
{

    public function getIndex(Request $request): array
    {
        $staff = \Auth::guard('admin')->user();
        $query = Lead::where('staff_id', $staff->id);
        $query_new = Lead::where('staff_id', null);

        return [
            LeadStatus::STATUS_NEW => $this->getLeadsByStatus($query_new, LeadStatus::STATUS_NEW),
            LeadStatus::STATUS_IN_WORK => $this->getLeadsByStatus($query, LeadStatus::STATUS_IN_WORK), //'in_work'
            LeadStatus::STATUS_NOT_DECIDED => $this->getLeadsByStatus($query, LeadStatus::STATUS_NOT_DECIDED), //'not_decide'
            LeadStatus::STATUS_INVOICE => $this->getLeadsByStatus($query, LeadStatus::STATUS_INVOICE), //'invoice'
            LeadStatus::STATUS_PAID => $this->getLeadsByStatus($query, LeadStatus::STATUS_PAID), //'paid'
            LeadStatus::STATUS_ASSEMBLY => $this->getLeadsByStatus($query, LeadStatus::STATUS_ASSEMBLY), //'assembly'
            LeadStatus::STATUS_DELIVERY => $this->getLeadsByStatus($query, LeadStatus::STATUS_DELIVERY), //'delivery'
        ];

        //return Lead::get()->map(fn(Lead $lead) => $this->LeadToArray($lead))->toArray();
    }

    public function getFreeLeads(): array
    {
        $leads = Lead::where('staff_id', null)->whereHas('status', function ($query) {
            $query->where('value', LeadStatus::STATUS_NEW);
        });

        return $leads->get()->map(fn(Lead $lead) => $this->LeadToArray($lead))->toArray();
    }




    public function getMyLeads(): array
    {
        $staff = \Auth::guard('admin')->user();
        $query = Lead::where('staff_id', $staff->id);

        return [
            LeadStatus::STATUS_IN_WORK => $this->getLeadsByStatus($query, LeadStatus::STATUS_IN_WORK), //'in_work'
            LeadStatus::STATUS_NOT_DECIDED => $this->getLeadsByStatus($query, LeadStatus::STATUS_NOT_DECIDED), //'not_decide'
            LeadStatus::STATUS_INVOICE => $this->getLeadsByStatus($query, LeadStatus::STATUS_INVOICE), //'invoice'
            LeadStatus::STATUS_PAID => $this->getLeadsByStatus($query, LeadStatus::STATUS_PAID), //'paid'
            LeadStatus::STATUS_ASSEMBLY => $this->getLeadsByStatus($query, LeadStatus::STATUS_ASSEMBLY), //'assembly'
            LeadStatus::STATUS_DELIVERY => $this->getLeadsByStatus($query, LeadStatus::STATUS_DELIVERY), //'delivery'
        ];
    }
    private function getLeadsByStatus($query, #[ExpectedValues(valuesFromClass: LeadStatus::class)] int $status)
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
            'user' => is_null($lead->user) ? null : [
                'id' => $lead->user->id,
                'fullname' => $lead->user->fullname,
                'email' => $lead->user->email,
                'phone' => $lead->user->phone,
            ],
            'order' => is_null($lead->order) ? null : [
                'id' => $lead->order->id,
                'number' => $lead->order->number,
                'status' => $lead->order->status->value(),
                'amount' => $lead->order->getTotalAmount(),
            ],
            'items' => $lead->items()->get()->toArray(),
            'leads' => $this->getRelatedLeads($lead),
        ]);
    }

    public function getBoards(): array
    {
        $result = [];
        foreach (LeadStatus::STATUSES as $key => $label)
        {
            if ($key < LeadStatus::STATUS_CANCELED) {
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

        foreach ($lead->data as $item) {
            if ($item->slug == 'email') $email = $item->value;
            if ($item->slug == 'phone') $phone = $item->value;
        }
        if (empty($email) && empty($phone)) return [];

        return Lead::where('user_id', null)->where(function ($query) use ($email, $phone) {
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
