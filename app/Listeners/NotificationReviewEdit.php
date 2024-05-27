<?php

namespace App\Listeners;

use App\Events\ReviewHasEdit;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Notifications\StaffMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationReviewEdit
{
    private StaffRepository $staffs;

    public function __construct(StaffRepository $staffs)
    {
        $this->staffs = $staffs;
    }

    /**
     * Handle the event.
     */
    public function handle(ReviewHasEdit $event): void
    {
        $staffs = $this->staffs->getStaffsByCode(Responsibility::MANAGER_REVIEW);

        foreach ($staffs as $staff) {

            $staff->notify(new StaffMessage(
                'Отзыв на модерацию',
                'Отзыв на товар ' . $event->review->product->name,
                route('admin.feedback.review.show', $event->review),
                'message-square-warning'
            ));

        }
    }
}
