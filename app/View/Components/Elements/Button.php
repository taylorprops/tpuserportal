<?php

namespace App\View\Components\Elements;

use Illuminate\View\Component;

class Button extends Component
{

    public $colorClass; // primary, secondary, danger, success
    public $size; // sm, md, lg

    /**
     * Create a new component instance.
     *
     * @return void
     */

    public function __construct($colorClass, $size)
    {
        $this -> colorClass = $colorClass;
        $this -> size = $size;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */

    public function render()
    {
        return view('components.elements.button');
    }
}
