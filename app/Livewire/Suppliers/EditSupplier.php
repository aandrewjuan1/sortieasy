<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Models\Supplier;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Renderless;
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

    #[Renderless]
    public function delete()
    {
        $this->authorize('delete', $this->supplier);

        $this->supplier->delete();

        $this->dispatch('modal-close', name: 'delete-supplier');
        $this->dispatch('modal-close', name: 'edit-supplier');
        $this->dispatch('supplier-deleted');
        $this->dispatch('notify',
            type: 'success',
            message: 'Supplier deleted successfully!'
        );
        Cache::forget('audit-logs:page:1:per_page:10:sort:created_at:dir:DESC:search::user::action::table::from::to:');
        Cache::forget('products:page:1:per_page:10:sort:created_at:dir:DESC:search::category::supplier::stock::status:');
    }

    public function update()
    {
        $validated = $this->validate([
            'name' => 'required|min:3|max:255|unique:suppliers,name,' . $this->supplier->id,
            'contact_email' => 'required|email|max:255|unique:suppliers,contact_email,' . $this->supplier->id,
            'contact_phone' => 'required|string|max:15|unique:suppliers,contact_phone,' . $this->supplier->id,
            'address' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
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

            Cache::forget('audit-logs:page:1:per_page:10:sort:created_at:dir:DESC:search::user::action::table::from::to:');
            Cache::forget('products:page:1:per_page:10:sort:created_at:dir:DESC:search::category::supplier::stock::status:');
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
