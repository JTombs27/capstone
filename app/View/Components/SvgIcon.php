<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SvgIcon extends Component
{
    public $icon;

    public function __construct($icon)
    {
        $this->icon = $icon;
    }

    public function render()
    {
        $path = resource_path("svg/{$this->icon}.svg");
        return file_exists($path) ? file_get_contents($path) : '';
    }
}
