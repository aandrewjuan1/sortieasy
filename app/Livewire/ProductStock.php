<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Models\Supplier;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class ProductStock extends Component
{
    use WithPagination, WithFileUploads;

    #[Url]
    public $search = '';

    public $perPage = 10;
    public $isModalOpen = false;
    public $editingProduct = null;
    public $name, $description, $category, $sku, $price, $cost,
           $quantity_in_stock, $reorder_threshold, $safety_stock,
           $last_restocked, $supplier_id, $image;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'category' => 'required|string|max:255',
        'sku' => 'required|unique:products,sku',
        'price' => 'required|numeric|min:0',
        'cost' => 'nullable|numeric|min:0',
        'quantity_in_stock' => 'required|integer|min:0',
        'reorder_threshold' => 'required|integer|min:0',
        'safety_stock' => 'required|integer|min:0',
        'last_restocked' => 'nullable|date',
        'supplier_id' => 'required|exists:suppliers,id',
        'image' => 'nullable|image|max:2048',
    ];

    public function render()
    {
        return view('livewire.product-stock', [
            'products' => Product::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('sku', 'like', "%{$this->search}%")
                    ->orWhere('category', 'like', "%{$this->search}%"))
                ->with('supplier')
                ->orderBy('name')
                ->paginate($this->perPage),
            'suppliers' => Supplier::orderBy('name')->get(),
            'categories' => Product::distinct('category')->pluck('category')
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
        $this->editingProduct = null;
    }

    public function edit(Product $product)
    {
        $this->editingProduct = $product;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->category = $product->category;
        $this->sku = $product->sku;
        $this->price = $product->price;
        $this->cost = $product->cost;
        $this->quantity_in_stock = $product->quantity_in_stock;
        $this->reorder_threshold = $product->reorder_threshold;
        $this->safety_stock = $product->safety_stock;
        $this->last_restocked = $product->last_restocked?->format('Y-m-d');
        $this->supplier_id = $product->supplier_id;
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->rules['sku'] = 'required|unique:products,sku,' . ($this->editingProduct?->id ?? '');

        $validated = $this->validate();

        if ($this->image) {
            $validated['image'] = $this->image->store('products', 'public');
        }

        if ($this->editingProduct) {
            $this->editingProduct->update($validated);
            session()->flash('message', 'Product updated successfully!');
        } else {
            Product::create($validated);
            session()->flash('message', 'Product created successfully!');
        }

        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function delete(Product $product)
    {
        $product->delete();
        session()->flash('message', 'Product deleted successfully!');
    }

    private function resetForm()
    {
        $this->reset([
            'name', 'description', 'category', 'sku', 'price', 'cost',
            'quantity_in_stock', 'reorder_threshold', 'safety_stock',
            'last_restocked', 'supplier_id', 'image', 'editingProduct'
        ]);
        $this->resetErrorBag();
    }
}
