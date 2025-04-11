<?php

namespace App\Livewire\Inventory;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use Illuminate\Support\Facades\Cache;

class ShowProduct extends Component
{
    #[Locked]
    public ?int $productId = null;

    #[Computed]
    public function product(): ?Product
    {
        if ($this->productId) {
            return Product::findOrFail($this->productId);
        }
        return null;
    }
    #[On('show-product')]
    public function setMediaId(int $productId)
    {
        $this->productId = $productId;
    }

    #[Renderless]
    public function delete()
    {
        $this->authorize('delete', $this->product);

        $this->product->delete();

        $this->dispatch('modal-close', name: 'delete-product');
        $this->dispatch('modal-close', name: 'show-product');
        $this->dispatch('product-deleted');
        $this->dispatch('notify',
            type: 'success',
            message: 'Product deleted successfully!'
        );

        Cache::forget('suppliers:page:1:per_page:10:sort:created_at:dir:DESC:search::product:');
        Cache::forget('transactions:page:1:per_page:10:sort:created_at:dir:DESC:search::type::date:');
    }


    #[On('product-updated')]
    public function render()
    {
        return view('livewire.inventory.show-product');
    }
}
