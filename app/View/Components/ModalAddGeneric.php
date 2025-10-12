<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModalAddGeneric extends Component
{
    public string $id;
    public string $title;
    public string $route;
    public array $fields;

    public function __construct(string $id, string $title, string $route, array $fields)
    {
        $this->id = $id;
        $this->title = $title;
        $this->route = $route;
        $this->fields = $fields;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modal-add-generic');
    }
}
