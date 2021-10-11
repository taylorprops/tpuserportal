<?php

namespace App\View\Components\Nav;

use Illuminate\View\Component;

class BreadCrumbs extends Component
{

    public $breadcrumbs;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($breadcrumbs)
    {
        $this -> breadcrumbs = $breadcrumbs;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.nav.bread-crumbs');
    }
}
