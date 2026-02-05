<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthModal extends Component
{
    public bool $isOpen = false;
    public string $activeTab = 'login';

    #[On('openAuthModal')]
    public function handleOpenAuthModal($tab = 'login')
    {
        $this->openModal($tab);
    }

    // Login fields
    public string $loginEmail = '';
    public string $loginPassword = '';
    public bool $loginRemember = false;

    // Register fields
    public string $registerName = '';
    public string $registerEmail = '';
    public string $registerPassword = '';
    public string $registerPasswordConfirm = '';
    public bool $registerTerms = false;

    protected $rules = [
        'loginEmail' => 'required|email',
        'loginPassword' => 'required|min:6',
        'registerName' => 'required|string|max:255',
        'registerEmail' => 'required|string|email|max:255|unique:users,email',
        'registerPassword' => 'required|string|min:8|confirmed',
        'registerPasswordConfirm' => 'required|string|min:8',
        'registerTerms' => 'required|accepted',
    ];

    public function openModal($tab = 'login')
    {
        $this->isOpen = true;
        $this->activeTab = $tab;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetValidation();
    }

    public function resetForm()
    {
        $this->loginEmail = '';
        $this->loginPassword = '';
        $this->loginRemember = false;
        $this->registerName = '';
        $this->registerEmail = '';
        $this->registerPassword = '';
        $this->registerPasswordConfirm = '';
        $this->registerTerms = false;
    }

    public function handleLogin()
    {
        $this->validate([
            'loginEmail' => 'required|email',
            'loginPassword' => 'required|min:6',
        ]);

        // Try to login as User
        if (Auth::guard('web')->attempt(
            ['email' => $this->loginEmail, 'password' => $this->loginPassword],
            $this->loginRemember
        )) {
            request()->session()->regenerate();
            $user = Auth::guard('web')->user();

            // Check user role and redirect accordingly
            if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
                $this->closeModal();
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('officer')) {
                $this->closeModal();
                return redirect()->route('officer.dashboard');
            } else {
                // Regular user - stay on home
                $this->closeModal();
                return redirect()->route('home');
            }
        }

        // Try to login as Officer
        if (Auth::guard('officer')->attempt(
            ['email' => $this->loginEmail, 'password' => $this->loginPassword],
            $this->loginRemember
        )) {
            request()->session()->regenerate();
            $this->closeModal();
            return redirect()->route('officer.dashboard');
        }

        // Try to login as Admin
        if (Auth::guard('admin')->attempt(
            ['email' => $this->loginEmail, 'password' => $this->loginPassword],
            $this->loginRemember
        )) {
            request()->session()->regenerate();
            $this->closeModal();
            return redirect()->route('admin.dashboard');
        }

        // Try to login as Courier
        $courier = User::where('email', $this->loginEmail)->first();
        if ($courier && Auth::guard('courier')->attempt(
            ['email' => $this->loginEmail, 'password' => $this->loginPassword],
            $this->loginRemember
        )) {
            request()->session()->regenerate();
            $this->closeModal();
            return redirect()->route('courier.dashboard');
        }

        $this->addError('loginEmail', 'Email atau password salah.');
    }

    public function handleRegister()
    {
        $this->validate([
            'registerName' => 'required|string|max:255',
            'registerEmail' => 'required|string|email|max:255|unique:users,email',
            'registerPassword' => 'required|string|min:8',
            'registerPasswordConfirm' => 'required|string|min:8|same:registerPassword',
            'registerTerms' => 'required|accepted',
        ]);

        $user = User::create([
            'name' => $this->registerName,
            'email' => $this->registerEmail,
            'password' => Hash::make($this->registerPassword),
        ]);

        Auth::login($user);
        $this->closeModal();
        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.auth-modal');
    }
}
