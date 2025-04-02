<?php

namespace App\Livewire\Settings;

use App\Exceptions\AppException;
use App\Models\Users\User;
use App\Traits\AlertFrontEnd;
use Exception;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class UsersIndex extends Component
{
    use WithPagination, AlertFrontEnd, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $search;

    public $setUserSec;
    public $loadedUser;
    public $username;
    public $userImage;
    public $userImageUrl;
    public $name;
    public $type;
    public $password;
    public $password_confirmation;
    public $user;

    // Change password modal properties
    public $changePasswordModal = false;
    public $selectedUserId;
    public $newPassword;
    public $newPassword_confirmation;

    public function clearImage()
    {
        $this->userImage = null;
        $this->userImageUrl = null;
    }

    public function updateThisUser($id)
    {
        $this->setUserSec = $id;
        $this->loadedUser = User::find($id);
        $this->user = $this->loadedUser;
        $this->username = $this->loadedUser->username;
        $this->name = $this->loadedUser->name;
        $this->type = $this->loadedUser->type;
        $this->userImageUrl = $this->loadedUser->image_url;
        if ($this->loadedUser->image_url) {
            $this->userImage = $this->loadedUser->full_image_url;
        }
    }

    public function toggleUserStatus($id)
    {
        try {
            User::find($id)->toggleStatus();
            $this->alertSuccess('User status updated successfully');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            $this->alertError('Internal server error');
        }
    }

    public function clearUserImage()
    {
        $this->userImage = null;
        $this->userImageUrl = null;
    }

    public function closeSetUserSec()
    {
        $this->reset(['setUserSec', 'username', 'name', 'type', 'userImage', 'password', 'password_confirmation']);
    }

    // Open change password modal
    public function openChangePasswordModal($id)
    {
        $this->selectedUserId = $id;
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
            $user = User::find($this->selectedUserId);
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

    public function EditUser()
    {
        $currentUserId = $this->setUserSec;
        $this->validate([
            'username' => [
                'required',
                'max:255',
                'unique:users,username,' . $currentUserId,
            ],
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', User::TYPES),
        ]);

        if (!is_url($this->userImage)) {
            $this->validate([
                'userImage' => 'nullable|image|max:5024',
            ]);
            if ($this->userImage) {
                $imageUrl = $this->userImage->store('users', 's3');
            }
        } else {
            $imageUrl = $this->userImageUrl;
        }



        try {
            User::find($currentUserId)->editInfo($this->username, $this->name, $this->type, $imageUrl);
            $this->closeSetUserSec();
            $this->alertSuccess('User updated successfuly!');
        } catch (AppException $e) {
            $this->alertError($e->getMessage());
        } catch (Exception $e) {
            report($e);
            $this->alertError('Internal server error');
        }
    }

    public function openNewUserSec()
    {
        $this->setUserSec = true;
    }


    public function addNewUser()
    {
        $validatedData = $this->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', User::TYPES),
            'password' => 'required|string|min:8|confirmed',
            'userImage' => 'nullable|image|max:5024',
        ]);
        $imageUrl = null;
        if ($this->userImage) {
            $imageUrl = $this->userImage->store('users', 's3');
        }

        $res = User::createUser($this->username, $this->name, $this->password, $this->type, $imageUrl ?? $this->userImageUrl);

        if ($res) {
            $this->closeSetUserSec();
            $this->alertSuccess('User added successfuly!');
        } else {
            $this->alertError('Server error');
        }
    }


    public function render()
    {
        $users = User::search($this->search)->paginate(10);

        return view('livewire.settings.users-index', [
            'users' => $users,
            'TYPES' => User::TYPES,
            'usersIndex' => 'active'
        ])->layout('components.layouts.app', [
            'title' => 'Users',
            'usersIndex' => 'active'
        ]);
    }
}
