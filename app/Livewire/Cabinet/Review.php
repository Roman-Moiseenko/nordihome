<?php

namespace App\Livewire\Cabinet;

use App\Entity\Photo;
use App\Modules\Product\Service\ReviewService;
use Livewire\Component;
use App\Modules\Product\Entity\Review as ProductReview;
use Livewire\WithFileUploads;

class Review extends Component
{
    use WithFileUploads;

    public ProductReview $review;
    public string $text;
    public int $rating;
    public bool $edit = false;
    public string $caption = 'Изменить';
    private ReviewService $service;
    public $image;

    public function boot(ReviewService $service)
    {
        $this->service = $service;
    }

    public function mount(ProductReview $review)
    {
        $this->review = $review;
        $this->refresh_fields();
    }

    private function refresh_fields()
    {
        $this->rating = $this->review->rating;
        $this->text = $this->review->text;
    }

    public function toggle_button()
    {
        if ($this->edit) {
            $this->caption = 'Изменить';
            $this->edit = false;
            $this->service->update($this->review, $this->text, $this->rating);
            $this->review->photo()->save(Photo::upload($this->image));
        } else {
            $this->edit = true;
            $this->caption = 'Сохранить';
        }
    }

    public function cancel_button()
    {
        $this->edit = false;
        $this->refresh_fields();
        $this->caption = 'Изменить';

    }

    public function render()
    {
        return view('livewire.cabinet.review');
    }


    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка в Уведомлениях', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }

}
