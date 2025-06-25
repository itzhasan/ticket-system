<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Profile extends Component
{
   public $name;
    public $email;
    public $new_password;
    public $new_password_confirmation;
    public $showPasswordSection = false;
    public $successMessage = '';
    public $errorMessage = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
            ],
            'new_password' => 'nullable|min:8|confirmed',
        ];
    }

    protected $messages = [
        'new_password.confirmed' => 'The new password confirmation does not match.'
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function updateProfile()
    {
        $this->resetMessages();

        $rules = $this->rules();

        if (!$this->new_password) {
            unset($rules['current_password'], $rules['new_password']);
        }

        $this->validate($rules);

        try {
            $user = Auth::user();

            $updateData = [
                'name' => $this->name,
                'email' => $this->email,
            ];

            if ($this->new_password) {
                $updateData['password'] = bcrypt($this->new_password);
            }
            $user->update($updateData);

            $this->reset(['new_password', 'new_password_confirmation']);
            $this->showPasswordSection = false;

            $this->successMessage = 'Profile updated successfully!';

            $this->emit('profileUpdated');

        } catch (\Exception $e) {
            $this->errorMessage = 'An error occurred while updating your profile. Please try again.';
        }
    }

    public function togglePasswordSection()
    {
        $this->showPasswordSection = !$this->showPasswordSection;

        if (!$this->showPasswordSection) {
            $this->reset(['new_password', 'new_password_confirmation']);
        }
    }

    private function resetMessages()
    {
        $this->successMessage = '';
        $this->errorMessage = '';
    }

    public function render()
    {
        return view('livewire.auth.profile');
    }
}
