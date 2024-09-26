<?php
declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;

class InfoBlock  extends Component
{
    public string $title;
    public string $id;
    public string $route;
    public bool $show;

    public function __construct(string $title, string $id = '', string $route = '', bool $show = true)
    {
        $this->title = $title;
        $this->id = $id;
        $this->route = $route;
        $this->show = $show;
    }

    public function render()
    {
        return view('components.info-block');
    }
}
