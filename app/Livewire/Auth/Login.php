<?php

namespace App\Livewire\Auth;

use App\Exceptions\AppException;
use App\Models\Users\User;
use App\Traits\AlertFrontEnd;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Login extends Component
{
    use AlertFrontEnd;

    public $username;
    public $password;

    public function checkUser()
    {
        $this->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        try {
            User::login($this->username, $this->password);
            $this->alertSuccess('Login successful');
            return redirect('/');
        } catch(AppException $e) {
            $this->alertError($e->getMessage());
        } 
        catch (\Exception $e) {
            $this->alertError('An error occurred while logging in');
        }
    }


    public function render()
    {
        return view('livewire.auth.login')
        ->layout('components.layouts.base');
    }
}
