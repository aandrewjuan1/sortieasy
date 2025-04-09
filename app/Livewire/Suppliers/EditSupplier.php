<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Models\Supplier;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class EditSupplier extends Component
{
    public string $name = '';
    public string $contact_email = '';
    public string $contact_phone = '';
    public string $address = '';
    public ?Supplier $supplier = null;

    public function fillInputs($supplier)
    {
        $this->name = $supplier->name;
        $this->contact_email = $supplier->contact_email;
        $this->contact_phone = $supplier->contact_phone;
        $this->address = $supplier->address;
    }

    #[On('edit-supplier')]
    public function editSupplier($supplierId)
    {
        $this->supplier = Supplier::where('id', $supplierId)->first();
        $this->fillInputs($this->supplier);
        $this->resetValidation();
    }

    public function update()
    {
        $validated = $this->validate([
            'name' => 'required|min:3|max:255|unique:suppliers,name,' . $this->supplier->id,
            'contact_email' => 'required|email|max:255|unique:suppliers,contact_email,' . $this->supplier->id,
            'contact_phone' => 'required|string|max:15|unique:suppliers,contact_phone,' . $this->supplier->id,
            'address' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $this->authorize('edit', $this->supplier);
            $this->supplier->update($validated);

            DB::commit();

            $this->reset();

            // Dispatch events after successful update
            $this->dispatch('modal-close', name: 'edit-supplier');
            $this->dispatch('supplier-updated');
            $this->dispatch('notify',
                type: 'success',
                message: 'Supplier updated successfully!'
            );

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Supplier update failed: ' . $e->getMessage());
            $this->dispatch('notify',
                type: 'error',
                message: 'Failed to update supplier.'
            );
        }
    }
}
