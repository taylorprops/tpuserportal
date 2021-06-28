<?php

namespace App\View\Components\Elements;

use Illuminate\View\Component;

class Toggle extends Component
{

    public $label;
    public $size;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($label, $size)
    {
        $this -> label = $label;
        $this -> size = $size;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.elements.toggle');
    }
}

