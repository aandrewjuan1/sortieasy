<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Models\Supplier;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;

class AddSupplier extends Component
{
    #[Validate('required|min:3|max:255|unique:suppliers,name')]
    public string $name = '';

    #[Validate('required|email|max:255|unique:suppliers,contact_email')]
    public string $contact_email = '';

    #[Validate('required|string|max:15|unique:suppliers,contact_phone')]
    public string $contact_phone = '';

    #[Validate('required|string|max:1000')]
    public string $address = '';

    public function save()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $this->authorize('create', Supplier::class);
            Supplier::create([
                'name' => $this->name,
                'contact_email' => $this->contact_email,
                'contact_phone' => $this->contact_phone,
                'address' => $this->address
            ]);

            DB::commit();

            $this->reset();

            $this->dispatch('modal-close', name: 'add-supplier');
            $this->dispatch('supplier-added');
            $this->dispatch('notify',
                type: 'success',
                message: 'Supplier created successfully!'
            );

        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to create supplier: ' . $e->getMessage()
            );

            // Handle specific error cases
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                if (str_contains($e->getMessage(), 'suppliers_name_unique')) {
                    $this->addError('name', 'This supplier name already exists');
                } elseif (str_contains($e->getMessage(), 'suppliers_contact_email_unique')) {
                    $this->addError('contact_email', 'This email is already registered');
                } elseif (str_contains($e->getMessage(), 'suppliers_contact_phone_unique')) {
                    $this->addError('contact_phone', 'This phone number is already registered');
                }
            }
        }
    }
}
