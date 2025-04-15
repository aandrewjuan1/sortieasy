<?php

namespace App\Livewire\ManageUsers;

use App\Models\User;
use App\Enums\UserRole;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class EditUser extends Component
{
    public string $name = '';

    public string $email = '';

    public ?string $password = null;

    public ?string $password_confirmation = null;

    public string $role = UserRole::Employee->value;

    public ?string $phone = null;

    public bool $is_active = true;
    public ?User $user = null;

    #[On('edit-user')]
    public function editSupplier($userId)
    {
        $this->user = User::where('id', $userId)->first();
        $this->fillInputs($this->user);
        $this->resetValidation();
    }

    public function fillInputs($user)
    {
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role->value;
        $this->phone = $user->phone;
        $this->is_active = $user->is_active;
    }

    public function update()
    {
        $validated = $this->validate([
            'name' => 'required|min:3|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->user?->id),
            ],
            'password' => 'nullable|min:8',
            'password_confirmation' => 'nullable|same:password',
            'role' => 'required|in:'.UserRole::Admin->value.','.UserRole::Employee->value, // Make consistent
            'phone' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {
            if (!$this->user) {
                throw new \Exception('User not found');
            }

            // Add password if present
            if ($this->password) {
                $validated['password'] = bcrypt($this->password);
            } else {
                unset($validated['password']);
            }

            // Remove password_confirmation from the data to save
            unset($validated['password_confirmation']);

            $this->user->update($validated);

            DB::commit();

            $this->reset();

            $this->dispatch('modal-close', name: 'edit-user');
            $this->dispatch('user-updated');
            $this->dispatch('notify',
                type: 'success',
                message: 'User updated successfully!'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User update failed: '.$e->getMessage());
            $this->dispatch('notify',
                type: 'error',
                message: 'Failed on updating the user.'
            );
        }
    }

    public function delete()
    {
        DB::beginTransaction();

        try {
            if (!$this->user) {
                throw new \Exception('User not found');
            }

            $this->authorize('delete', $this->user);

            $this->user->delete();

            DB::commit();

            $this->reset();

            $this->dispatch('modal-close', name: 'delete-user');
            $this->dispatch('modal-close', name: 'edit-user');
            $this->dispatch('user-deleted');
            $this->dispatch('notify',
                type: 'success',
                message: 'User deleted successfully!'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User deletion failed: '.$e->getMessage());
            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to delete the user.'
            );
        }
    }
}
