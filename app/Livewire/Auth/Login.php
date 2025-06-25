<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Title;
use Illuminate\Validation\ValidationException;

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
        $key = "login_" . request()->ip();

        // Check if the user has hit the rate limit
        if (RateLimiter::tooManyAttempts($key, maxAttempts: 5)) {
            throw ValidationException::withMessages([
                'email' => ['Too many login attempts. Please try again later.']
            ]);
        } else {
            RateLimiter::hit($key, 60);
            $this->validate();

            if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
                session()->regenerate();
                return redirect()->intended(route('dashboard'));
            }

            $this->addError('email', 'The provided credentials do not match our records.');
        }
    }

    #[Title('Login')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}
