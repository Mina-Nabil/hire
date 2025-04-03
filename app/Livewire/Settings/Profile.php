<?php

namespace App\Livewire\Settings;

use App\Exceptions\AppException;
use App\Models\Users\User;
use App\Traits\AlertFrontEnd;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads, AlertFrontEnd;

    public $user;
    public $userImage;
    public $userImageUrl;
    public $username;
    public $name;
    public $type;

    public $changes = false;

    // Change password modal properties
    public $changePasswordModal = false;
    public $newPassword;
    public $newPassword_confirmation;

    public function mount()
    {
        $this->user = Auth::user();
        $this->username = $this->user->username;
        $this->name = $this->user->name;
        $this->type = $this->user->type;
    }

    public function clearImage()
    {
        $this->userImage = null;
        $this->userImageUrl = null;
    }

    public function updatingUsername()
    {
        $this->changes = true;
    }

    public function updatingName()
    {
        $this->changes = true;
    }

    public function updatingUserImage()
    {
        $this->changes = true;
    }

    // Open change password modal
    public function openChangePass()
    {
        $this->changePasswordModal = true;
        $this->resetPasswordFields();
    }

    // Close change password modal
    public function closeChangePasswordModal()
    {
        $this->changePasswordModal = false;
        $this->resetPasswordFields();
    }

    // Reset password fields
    private function resetPasswordFields()
    {
        $this->reset(['newPassword', 'newPassword_confirmation']);
    }

    // Change password
    public function changeUserPassword()
    {
        $this->validate([
            'newPassword' => 'required|string|min:8|confirmed',
        ], [
            'newPassword.required' => 'Password is required',
            'newPassword.min' => 'Password must be at least 8 characters',
            'newPassword.confirmed' => 'Passwords do not match',
        ]);

        try {
            // Get the database model for the user
            $user = User::find(Auth::id());
            if (!$user) {
                throw new AppException('User not found');
            }
            
            // Use the changePassword method from the User model
            $user->changePassword($this->newPassword);
            
            $this->closeChangePasswordModal();
            $this->alertSuccess('Password changed successfully');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Internal server error');
        }
    }

    public function saveInfo()
    {
        try {
            // Get the database model for the user
            /** @var User $user */
            $user = Auth::user();
            
            // Handle image upload if needed
            $imageUrl = null;
            if ($this->userImage && !is_string($this->userImage)) {
                // Store the image and get the path
                $imageUrl = $this->userImage->store('users', 's3');
            } else {
                $imageUrl = $this->user->image_url;
            }
            
            // Use the editInfo method from the User model
            $user->editInfo(
                $this->name,
                $this->username, 
                $this->user->type, // Use existing type 
                $imageUrl
            );
            
            $this->changes = false;
            $this->alertSuccess('Profile updated successfully');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Failed to update profile: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.settings.profile');
    }
}
