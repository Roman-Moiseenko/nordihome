<?php

namespace App\Listeners;

use App\Events\ReviewHasEdit;
use App\Modules\Auth\Application\Actions\Staff\ListStaffByPositionUseCase;
use App\Modules\Auth\Domain\ValueObjects\StaffPosition;

class NotificationReviewEdit
{
    public function __construct(private readonly ListStaffByPositionUseCase $positionUseCase)
    {}

    /**
     * Handle the event.
     */
    public function handle(ReviewHasEdit $event): void
    {
        $staffs = $this->positionUseCase->execute(StaffPosition::customerManager());

        //FIXME Модуль Notification - через RecipientResolverInterface
/*

        foreach ($staffs as $staff) {

            $staff->notify(new StaffMessage(
                'Отзыв на модерацию',
                'Отзыв на товар ' . $event->review->product->name,
                route('admin.feedback.review.show', $event->review),
                'message-square-warning'
            ));

        }
*/
    }
}
