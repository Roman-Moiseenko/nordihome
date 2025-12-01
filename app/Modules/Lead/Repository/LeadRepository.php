<?php

namespace App\Modules\Lead\Repository;

use App\Modules\Lead\Entity\Lead;
use App\Modules\Lead\Entity\LeadStatus;
use Illuminate\Http\Request;

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
        $queryInWork = clone $query;
        $queryCompleted= clone $query;

        return [
            'in_work' => $queryInWork->whereHas('status', function ($query) {
                $query->where('value', LeadStatus::STATUS_IN_WORK);
            })->get()->map(fn(Lead $lead) => $this->LeadToArray($lead))->toArray(),
            'completed' => $queryCompleted->whereHas('status', function ($query) {
                $query->where('value', LeadStatus::STATUS_COMPLETED);
            })->get()->map(fn(Lead $lead) => $this->LeadToArray($lead))->toArray(),

        ];
    }

    private function LeadToArray(Lead $lead): array
    {
        return array_merge($lead->toArray(), [
            'type' => $lead->getType(),
            'status' => $lead->status->value,
        ]);
    }



}
