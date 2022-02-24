<?php

namespace App\View\Components\Elements;

use Illuminate\View\Component;

class InputFile extends Component
{
    public $size;

    public $buttonClass;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($size, $buttonClass)
    {
        $this->size = $size;
        $this->buttonClass = $buttonClass;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.elements.input-file');
    }
}
