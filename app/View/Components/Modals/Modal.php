<?php

namespace App\View\Components\Modals;

use Illuminate\View\Component;

class Modal extends Component
{


    public $modalWidth;
    public $modalTitle;
    public $buttonId;
    public $buttonText;
    public $buttonClass;
    public $buttonSize;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($modalWidth, $modalTitle, $buttonId, $buttonText, $buttonClass, $buttonSize)
    {
        $this -> modalWidth = $modalWidth;
        $this -> modalTitle = $modalTitle;
        $this -> buttonId = $buttonId;
        $this -> buttonText = $buttonText;
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
        return view('components.modals.modal');
    }
}
