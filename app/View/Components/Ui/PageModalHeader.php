<?php

namespace App\View\Components\Ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PageModalHeader extends Component
{
    public $title;
    public $modalId;
    public $buttonText;
    public $formAction;

    public function __construct($title, $modalId, $buttonText, $formAction)
    {
        $this->title = $title;
        $this->modalId = $modalId;
        $this->buttonText = $buttonText;
        $this->formAction = $formAction;
    }

    public function render()
    {
        return view('components.ui.page-modal-header');
    }
}

