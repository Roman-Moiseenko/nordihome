<?php

namespace App\Listeners;

use App\Events\OrderHasAwaiting;
use App\Mail\OrderAwaiting;
use App\Modules\Mail\Job\SendSystemMail;
use App\Modules\Mail\Mailable\OrderAwaitingMail;
use App\Modules\Service\Report\InvoiceReport;
use App\Modules\Service\Report\ReportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotificationOrderAwaiting
{
    private InvoiceReport $invoiceReport;

    /**
     * Create the event listener.
     */
    public function __construct(InvoiceReport $invoiceReport)
    {
        //
        $this->invoiceReport = $invoiceReport;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderHasAwaiting $event): void
    {
        //Создать счет.
        $invoice = $this->invoiceReport->xlsx($event->order);
        $event->order->invoice()->create(['file' => $invoice, 'name' => 'Счет на оплату (первоначальный)']);
        SendSystemMail::dispatch($event->order->user, new OrderAwaitingMail($event->order, $invoice));

        Mail::to($event->order->user->email)->queue(new OrderAwaiting($event->order));
    }
}
