<?php

namespace App\Modules\Lead\Service;

use App\Modules\Feedback\Entity\FormBack;
use App\Modules\Lead\Entity\Lead;
use App\Modules\Lead\Entity\LeadStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LeadService
{

    public function createLeadFromForm(FormBack $form): void
    {
        // $lead = Lead::register();
        // Log::info('Либ был создан!');

        //TODO
    }

    public function setStatus(Lead $lead, Request $request): bool
    {
        $newStatus = $request->integer('status');
        if (!isset(LeadStatus::STATUSES[$newStatus])) return false;

        if ($lead->isNew()) {
            if ($newStatus != LeadStatus::STATUS_NEW) {
                $lead->staff_id = \Auth::guard('admin')->user()->id;
                $lead->setStatus($newStatus);
                $lead->save();
                return true;
            } else {
                return false;
            }
        } else {
            if ($newStatus == LeadStatus::STATUS_NEW) {
                $lead->staff_id = null;
                foreach ($lead->statuses as $status) {
                    $status->delete();
                }
            }
            $lead->setStatus($newStatus);
            $lead->save();
            return true;
        }

    }

    public function setName(Lead $lead, Request $request): void
    {
        $lead->name = $request->string('name')->trim()->value();
        $lead->save();
    }

    public function setComment(Lead $lead, Request $request): void
    {
        $lead->comment = $request->string('comment')->trim()->value();
        $lead->save();
    }

    public function setFinished(Lead $lead, Request $request): void
    {
        $lead->finished_at = Carbon::parse($request->input('finished_at'));
        $lead->save();
    }
}
