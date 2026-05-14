<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class CmsLayout extends Component
{
    public $departments;
    public $activeDepartment;
    public $users;
    public $selectedUserId;

    /**
     * Create a new component instance.
     */
    public function __construct($departments, $activeDepartment = null, $users = [], $selectedUserId = null)
    {
        $this->departments = $departments;
        $this->activeDepartment = $activeDepartment;
        $this->users = $users;
        $this->selectedUserId = $selectedUserId;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.cms');
    }
}
