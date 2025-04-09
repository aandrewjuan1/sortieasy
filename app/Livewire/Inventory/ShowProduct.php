<?php

namespace App\Livewire\Inventory;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;

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
    }


    #[On('product-updated')]
    public function render()
    {
        return view('livewire.inventory.show-product');
    }
}
