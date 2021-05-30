<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Dropdown extends Component
{

    public $buttonText;
    public $buttonClass;
    public $buttonSize;
    public $dropdownClasses;
    public $align;
    public $dropdownWidth;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($buttonText, $buttonClass, $buttonSize, $dropdownClasses, $align, $dropdownWidth)
    {
        $this -> buttonText = $buttonText;
        $this -> buttonClass = $buttonClass;
        $this -> buttonSize = $buttonSize;
        $this -> dropdownClasses = $dropdownClasses;
        $this -> align = $align;
        $this -> dropdownWidth = $dropdownWidth;
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
