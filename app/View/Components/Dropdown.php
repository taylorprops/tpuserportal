<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Dropdown extends Component
{

    public $buttonText;
    public $class;
    public $size;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($buttonText, $class, $size)
    {
        $this -> buttonText = $buttonText;
        $this -> class = $class;
        $this -> size = $size;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dropdown');
    }
}
