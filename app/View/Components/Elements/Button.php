<?php

namespace App\View\Components\Elements;

use Illuminate\View\Component;

class Button extends Component
{

    public $buttonClass; // primary, secondary, danger, success
    public $buttonSize; // sm, md, lg

    /**
     * Create a new component instance.
     *
     * @return void
     */

    public function __construct($buttonClass, $buttonSize)
    {
        $this -> buttonClass = $buttonClass;
        $this -> buttonSize = $buttonSize;
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
