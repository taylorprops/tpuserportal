<?php

namespace App\View\Components\Modals;

use Illuminate\View\Component;

class Modal extends Component
{
    public $modalWidth;

    public $modalTitle;

    public $modalId;

    public $clickOutside;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($modalWidth, $modalTitle, $modalId, $clickOutside = '')
    {
        $this->modalWidth = $modalWidth;
        $this->modalTitle = $modalTitle;
        $this->modalId = $modalId;
        $this->clickOutside = $clickOutside;
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
