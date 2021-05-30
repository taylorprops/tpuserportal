<?php

namespace App\View\Components\Nav;

use Illuminate\View\Component;

class Menu extends Component
{

    public $level;
    public $title;
    public $link;
    public $icon;
    public $level2;
    public $level3;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($level, $title, $icon, $link = null, $level2 = null, $level3 = null)
    {
        $this -> level = $level;
        $this -> title = $title;
        $this -> link = $link;
        $this -> icon = $icon;
        $this -> level2 = $level2;
        $this -> level3 = $level3;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.nav.menu');
    }
}
