<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Profile extends Component
{
   public $name;
    public $email;
    public $current_password;
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
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ];
    }

    protected $messages = [
        'new_password.confirmed' => 'The new password confirmation does not match.',
        'current_password.required_with' => 'Current password is required when setting a new password.',
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
            $user = User::find(Auth::id());
            // Verify current password if changing password
            if ($this->new_password) {
                if (!Hash::check($this->current_password, $user->password)) {
                    $this->addError('current_password', 'The current password is incorrect.');
                    return;
                }
            }

            $updateData = [
                'name' => $this->name,
                'email' => $this->email,
            ];

            if ($this->new_password) {
                $updateData['password'] = Hash::make($this->new_password);
            }

            $user->update($updateData);

            $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
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
            $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
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
