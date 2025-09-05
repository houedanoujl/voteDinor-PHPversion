<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RobustMobileUpload extends Component
{
    public string $wireModel;
    public int $maxSize;
    public bool $required;

    /**
     * Create a new component instance.
     */
    public function __construct(string $wireModel = 'photo', int $maxSize = 5, bool $required = true)
    {
        $this->wireModel = $wireModel;
        $this->maxSize = $maxSize;
        $this->required = $required;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.robust-mobile-upload');
    }
}
