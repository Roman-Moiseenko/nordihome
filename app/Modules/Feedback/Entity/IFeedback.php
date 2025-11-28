<?php

namespace App\Modules\Feedback\Entity;

use App\Modules\Feedback\Classes\DataFieldFeedback;
use Carbon\Carbon;

interface IFeedback
{
    public function date(): Carbon;

    /**
     * @return DataFieldFeedback[]
     */
    public function data(): array;
}
