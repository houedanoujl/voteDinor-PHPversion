<?php

namespace App\Livewire;

use Livewire\Component;

class TestModal extends Component
{
    public $showTest = false;

    public function toggleTest()
    {
        $this->showTest = !$this->showTest;
        \Log::info('TestModal toggled: ' . ($this->showTest ? 'true' : 'false'));
    }

    public function render()
    {
        return view('livewire.test-modal');
    }
}