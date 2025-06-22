<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;

class Login extends Component
{
     public $email = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        $this->addError('email', 'The provided credentials do not match our records.');
    }

    #[Title('Login')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}
