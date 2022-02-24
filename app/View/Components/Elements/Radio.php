<?php

namespace App\View\Components\Elements;

use Illuminate\View\Component;

class Radio extends Component
{
    public $size;

    public $color;

    public $label;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($size, $color, $label)
    {
        $this->size = $size;
        $this->color = $color;
        $this->label = $label;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.elements.radio');
    }
}
