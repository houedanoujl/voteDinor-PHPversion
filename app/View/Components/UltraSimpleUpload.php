<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UltraSimpleUpload extends Component
{
    public string $wireModel;

    /**
     * Create a new component instance.
     */
    public function __construct(string $wireModel = 'photo')
    {
        $this->wireModel = $wireModel;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ultra-simple-upload');
    }
}
