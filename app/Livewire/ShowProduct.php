<?php

namespace App\Livewire;

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
        if (!$this->product) {
            session()->flash('error', 'Product not found.');
            return;
        }

        $this->authorize('delete', $this->product);

        $this->product->delete();

        $this->dispatch('notify',
            type: 'success',
            message: 'Product deleted successfully!'
        );
        $this->dispatch('modal-close', name: 'show-product');
        $this->dispatch('modal-close', name: 'delete-product');
        $this->dispatch('product-deleted');
    }

    public function render()
    {
        return view('livewire.show-product');
    }
}
