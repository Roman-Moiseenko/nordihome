<?php
declare(strict_types=1);

use App\Modules\Product\Entity\Review;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

//REVIEW
Breadcrumbs::for('admin.feedback.review.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Отзывы на товары', route('admin.feedback.review.index'));
});
Breadcrumbs::for('admin.feedback.review.show', function (BreadcrumbTrail $trail, Review $review) {
    $trail->parent('admin.feedback.review.index');
    $trail->push($review->product->name . ': ' . $review->user->fullname->firstname, route('admin.feedback.review.show', $review));
});
