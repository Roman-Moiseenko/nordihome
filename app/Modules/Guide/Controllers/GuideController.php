<?php
declare(strict_types=1);

namespace App\Modules\Guide\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class GuideController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin']);
    }

    public function index(): Response
    {
        $guides = [
            [
                'name' => 'Дополнительные услуги',
                'route' => 'admin.guide.addition.index',
                'comment' => 'Услуги в заказе клиента, такие как доставка, сборка и другие',
                'font_awesome' => 'fa-light fa-bell-concierge',
            ],
            [
                'name' => 'Страны',
                'route' => 'admin.guide.country.index',
                'comment' => 'Список стран используемых в CRM для маркировки товаров',
                'font_awesome' => 'fa-light fa-flag',
            ],
            [
                'name' => 'Маркировка продукции',
                'route' => 'admin.guide.marking-type.index',
                'comment' => 'Маркировка продукции для "Честный знак"',
                'font_awesome' => 'fa-light fa-circle-parking',
            ],
            [
                'name' => 'Единицы измерения',
                'route' => 'admin.guide.measuring.index',
                'comment' => 'Единицы измерения товаров ',
                'font_awesome' => 'fa-light fa-scissors',
            ],
            [
                'name' => 'НДС',
                'route' => 'admin.guide.vat.index',
                'comment' => 'Налог на добавленную стоимость',
                'font_awesome' => 'fa-light fa-cent-sign',
            ],

        ];
        return Inertia::render('Guide/Index', [
            'guides' => $guides,

        ]);
    }
}
