<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FixedDropzone extends Component
{
    public string $wireModel;
    public int $maxSize;

    /**
     * Create a new component instance.
     */
    public function __construct(string $wireModel = 'photo', int $maxSize = 5)
    {
        $this->wireModel = $wireModel;
        $this->maxSize = $maxSize;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.fixed-dropzone');
    }
}
