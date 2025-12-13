<?php

namespace App\Modules\Lead\Service;

use App\Modules\Accounting\Entity\Trader;
use App\Modules\Feedback\Entity\FormBack;
use App\Modules\Lead\Entity\Lead;
use App\Modules\Lead\Entity\LeadStatus;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\User\Entity\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LeadService
{

    public function __construct()
    {
    }

    public function createLeadFromForm(FormBack $form): void
    {
        // $lead = Lead::register();
        // Log::info('Либ был создан!');
        $form->createLead($form->data());
        //TODO Вытащить из $form->data()  email и name

       /*
        if ($request->has('email')) {
            $email = $request->string('email')->trim()->value();
            $user = User::where('email', $email)->first();
            $form->lead->user_id = $user->id;
            $form->lead->save();
        } else if ($request->has('name')) {
            $form->lead->name = $request->string('name')->trim()->value();
            $form->lead->save();
        }
        */

    }

    public function createLeadFromOrder(Order $order): void
    {
        //TODO
        $data = [];
        $order->lead->create_lead($data);
        $order->lead->order_id = $order->id;
        $order->lead->user_id = $order->user_id;
        $order->lead->save();


    }

    public function setStatus(Lead $lead, Request $request): bool
    {
        $newStatus = $request->integer('status');
        if (!isset(LeadStatus::STATUSES[$newStatus])) return false;

        if (!$this->checkStatus($lead, $newStatus)) return false;

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

    private function checkStatus(Lead $lead, int $status): bool
    {
        if (is_null($lead->order_id) && $status > LeadStatus::STATUS_NOT_DECIDED) return false;
        //TODO Другие варианты
        return true;
    }

    public function canceled(Lead $lead, Request $request): void
    {
        $lead->setStatus(LeadStatus::STATUS_CANCELED);
        $lead->comment = $request->string('comment')->trim()->value();
        $lead->finished_at = null;
        $lead->save();
    }

    public function completed(Lead $lead, Request $request): void
    {
        $lead->setStatus(LeadStatus::STATUS_COMPLETED);
        //$lead->comment = $request->string('comment')->trim()->value();
        $lead->finished_at = null;
        $lead->save();
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
        $lead->finished_at = is_null($request->input('finished_at')) ? null : Carbon::parse($request->input('finished_at'));
        $lead->save();
    }

    public function createUser(Lead $lead, Request $request): void
    {
        $user = User::new(
            $request->string('email')->trim()->value(),
            $request->string('phone')->trim()->value()
        );
        $user->setNameField(
            $request->string('surname')->trim()->value(),
            $request->string('firstname')->trim()->value(),
            $request->string('secondname')->trim()->value(),

        );
        $lead->user_id = $user->id;
        $lead->save();
    }

    public function createOrder(Lead $lead, Request $request): Order
    {
        $trader_id = Trader::default()->organization->id;
        $order = Order::register($lead->user_id, Order::ONLINE, $trader_id);

        $lead->order_id = $order->id;
        $lead->save();
        return $order;
    }
}
