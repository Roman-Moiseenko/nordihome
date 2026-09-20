<?php

namespace App\Modules\Output\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Output\Application\Queries\Feed\GetFeedForGoogleQuery;
use App\Modules\Output\Application\Queries\Feed\GetFeedForYandexQuery;
use App\Modules\Output\Domain\Interfaces\FeedRepositoryInterface;
use App\Modules\Output\Infrastructure\Models\Feed;
use Illuminate\Http\Response;

class FeedXMLController extends Controller
{
    public function __construct(
        private readonly FeedRepositoryInterface $feedRepository,
        private readonly GetFeedForGoogleQuery $googleQuery,
        private readonly GetFeedForYandexQuery $yandexQuery,
    ) {}

    public function google(Feed $feed): Response
    {
        if (!$feed->active) {
            abort(404);
        }

        $data = $this->googleQuery->execute($this->feedRepository->getById($feed->id));
        $date = now()->addDays(14)->format('Y-m-d\TH:i+0200');

        $content = view('shop.unload.feed-google', compact('data', 'date'))->render();
        ob_end_clean();

        return response($content)->header('Content-Type', 'text/xml');
    }

    public function yandex(Feed $feed): Response
    {
        if (!$feed->active) {
            abort(404);
        }

        $data = $this->yandexQuery->execute($this->feedRepository->getById($feed->id));
        $date = now()->format('Y-m-d\TH:i');

        $content = view('shop.unload.feed-yandex', compact('data', 'date'))->render();
        ob_end_clean();

        return response($content)->header('Content-Type', 'text/xml');
    }
}
