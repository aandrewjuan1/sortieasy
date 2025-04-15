<?php

namespace App\Livewire\ManageUsers;

use App\Models\User;
use App\Enums\UserRole;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Hash;

class AddUser extends Component
{
    #[Validate('required|min:3|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255|unique:users,email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('required|same:password')]
    public string $password_confirmation = '';

    #[Validate('required|in:'.UserRole::Admin->value.','.UserRole::Employee->value)]
    public string $role = UserRole::Employee->value;

    #[Validate('nullable|string|max:20')]
    public ?string $phone = null;

    public bool $is_active = true;

    public function updatedRole($value)
    {
        if ($value === UserRole::Admin->value) {
            $this->authorize('changeRole', User::class);
        }
    }

    public function save()
    {
        $this->validate();

        $this->authorize('create', User::class);

        try {
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $this->role,
                'phone' => $this->phone,
                'is_active' => $this->is_active,
            ]);

            $this->reset();
            $this->dispatch('modal-close', name: 'add-user');
            $this->dispatch('user-added');
            $this->dispatch('notify',
                type: 'success',
                message: 'User account created successfully!'
            );

        } catch (\Exception $e) {
            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to create user.'
            );
        }
    }
}
