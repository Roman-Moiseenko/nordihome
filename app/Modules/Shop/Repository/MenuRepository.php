<?php

namespace App\Modules\Shop\Repository;

use App\Modules\Page\Entity\Contact;
use App\Modules\Page\Entity\Menu;
use App\Modules\Page\Repository\MenuRepository as AdminMenuRepository;

class MenuRepository
{
    private AdminMenuRepository $repository;

    public function __construct(AdminMenuRepository $repository)
    {

        $this->repository = $repository;
    }
    public function contacts(): array
    {
        $contacts = Contact::where('published', true)->orderBy('sort')->getModels();

        $result = [];
        foreach ($contacts as $index => $contact) {
            $key = empty($contact->slug) ? $index : $contact->slug;
            $result[$key] = [
                'name' => $contact->name,
                'icon' => $contact->icon,
                'color' => $contact->color,
                'url' => $contact->url,
                'data-type' => $contact->type,
            ];
        }
        return $result;

    }


    public function menus(): array
    {
        $result = [];
        foreach (Menu::getModels() as $menu) {
            $result[$menu->slug] = array_merge([
                'title' => $menu->name],
                $this->repository->getItems($menu));
        }
        return $result;
    }

}
