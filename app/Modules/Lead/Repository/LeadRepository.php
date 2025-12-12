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
        return Lead::get()->map(fn(Lead $lead) => $this->LeadToArray($lead))->toArray();
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
            'in_work' => $this->getLeadsByStatus($query, LeadStatus::STATUS_IN_WORK),
            'not_decide' => $this->getLeadsByStatus($query, LeadStatus::STATUS_NOT_DECIDED),
            'invoice' => $this->getLeadsByStatus($query, LeadStatus::STATUS_INVOICE),
            'paid' => $this->getLeadsByStatus($query, LeadStatus::STATUS_PAID),
            'assembly' => $this->getLeadsByStatus($query, LeadStatus::STATUS_ASSEMBLY),
            'delivery' => $this->getLeadsByStatus($query, LeadStatus::STATUS_DELIVERY),
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
                'status' => $lead->order->status->value(),
                'amount' => $lead->order->getTotalAmount(),
            ],
        ]);
    }



}
