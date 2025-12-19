<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StatCardComponent extends Component
{
    /**
     * Create a new component instance.
     */
    public $title;
    public $value;
    public $desc;
    public $icon;
    public $color;

    /**
     * Create a new component instance.
     */
    public function __construct($title, $value, $desc, $icon, $color = 'blue')
    {
        $this->title = $title;
        $this->value = $value;
        $this->desc = $desc;
        $this->icon = $icon;
        $this->color = $color;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.stat-card-component');
    }
}
