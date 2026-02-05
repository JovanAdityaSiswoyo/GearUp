<?php

namespace App\Http\Livewire\Home;

use Livewire\Component;

class LandingWithAuth extends Component
{
    public bool $showLoginModal = false;
    public string $loginTab = 'login';

    public function openAuthModal($tab = 'login')
    {
        $this->showLoginModal = true;
        $this->loginTab = $tab;
    }

    public function closeAuthModal()
    {
        $this->showLoginModal = false;
    }

    public function render()
    {
        return view('livewire.home.landing-with-auth');
    }
}
