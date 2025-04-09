<div class="relative min-h-[500px] min-w-[500px]" wire:loading.class="opacity-50">
    <div wire:loading.flex class="absolute inset-0 items-center justify-center bg-white bg-opacity-80 z-10 dark:bg-zinc-800 dark:bg-opacity-80">
        <flux:icon.loading />
    </div>
    <div>
        <!-- Product Details Section -->
        @if($this->product)
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-lg">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-zinc-100 mb-4">Product Details</h2>

                <div class="mb-4">
                    <span class="text-gray-600 dark:text-zinc-400 font-medium">Name:</span>
                    <p class="text-lg text-gray-900 dark:text-zinc-200">
                        {{ $this->product->name }}
                        @if($this->product->quantity_in_stock == 0)
                            <span class="ml-2 px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                Out of Stock
                            </span>
                        @endif
                    </p>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                        {{ Str::limit($this->product->description, 50) }}
                    </div>
                </div>

                <div class="mb-4">
                    <span class="text-gray-600 dark:text-zinc-400 font-medium">Description:</span>
                    <p class="text-gray-700 dark:text-zinc-300">{{ $this->product->description ?? 'No description available' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <span class="text-gray-600 dark:text-zinc-400 font-medium">Category:</span>
                        <p>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs dark:bg-blue-900 dark:text-blue-200">
                                {{ $this->product->category }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-zinc-400 font-medium">SKU:</span>
                        <p class="font-mono text-gray-900 dark:text-zinc-200">{{ $this->product->sku }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <span class="text-gray-600 dark:text-zinc-400 font-medium">Price:</span>
                        <p class="text-lg font-semibold text-gray-900 dark:text-zinc-200">${{ number_format($this->product->price, 2) }}</p>
                        @if($this->product->cost)
                            <div class="text-xs text-zinc-400">Cost: ${{ number_format($this->product->cost, 2) }}</div>
                        @endif
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-zinc-400 font-medium">Profit Margin:</span>
                        @if($this->product->cost && $this->product->cost > 0)
                            @php
                                $margin = (($this->product->price - $this->product->cost) / $this->product->price) * 100;
                            @endphp
                            <p class="{{ $margin >= 50 ? 'text-green-600 dark:text-green-400' : ($margin >= 30 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }} font-medium">
                                {{ number_format($margin, 1) }}%
                            </p>
                        @else
                            <p class="text-zinc-400">N/A</p>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <span class="text-gray-600 dark:text-zinc-400 font-medium">Quantity in Stock:</span>
                        <div class="flex items-center">
                            <span class="text-gray-900 dark:text-zinc-200 {{ $this->product->quantity_in_stock <= $this->product->safety_stock ? 'text-red-600 dark:text-red-400 font-bold' : ($this->product->quantity_in_stock <= $this->product->reorder_threshold ? 'text-yellow-600 dark:text-yellow-400' : 'text-zinc-500 dark:text-zinc-300') }}">
                                {{ $this->product->quantity_in_stock }}
                            </span>
                            @if($this->product->quantity_in_stock <= $this->product->reorder_threshold)
                                <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $this->product->quantity_in_stock <= $this->product->safety_stock ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' }}">
                                    {{ $this->product->quantity_in_stock <= $this->product->safety_stock ? 'Critical' : 'Low' }}
                                </span>
                            @endif
                        </div>
                        <div class="text-xs text-zinc-400 mt-1">
                            Reorder at: {{ $this->product->reorder_threshold }}
                        </div>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-zinc-400 font-medium">Reorder Threshold:</span>
                        <p class="text-gray-900 dark:text-zinc-200">{{ $this->product->reorder_threshold }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <span class="text-gray-600 dark:text-zinc-400 font-medium">Safety Stock:</span>
                        <p class="text-gray-900 dark:text-zinc-200">{{ $this->product->safety_stock }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-zinc-400 font-medium">Last Restocked:</span>
                        <p class="text-gray-900 dark:text-zinc-200">{{ $this->product->last_restocked ? $this->product->last_restocked->format('F j, Y') : 'Not restocked yet' }}</p>
                    </div>
                </div>

                <div class="mb-4">
                    <span class="text-gray-600 dark:text-zinc-400 font-medium">Supplier:</span>
                    <p class="text-gray-900 dark:text-zinc-200">{{ $this->product->supplier ? $this->product->supplier->name : 'No supplier assigned' }}</p>
                </div>

                @can('edit',$this->product)
                    <div class="mt-6 flex items-center justify-between">
                        <flux:modal.trigger name="edit-product">
                            <flux:button wire:click="$dispatch('edit-product', { productId: {{ $this->product->id }} })" class="bg-blue-500 dark:bg-zinc-700 text-white">Edit Product</flux:button>
                        </flux:modal.trigger>
                        <flux:modal.trigger name="delete-product">
                            <flux:button variant="danger">Delete Product</flux:button>
                        </flux:modal.trigger>
                    </div>
                @endcan
            </div>

            <flux:modal name="delete-product" class="min-w-[22rem]">
                <x-delete-confirm-modal subject="product"/>
            </flux:modal>
        @else
            <div class="bg-white dark:bg-zinc-800 p-6 rounded-lg shadow-md text-center">
                <p class="text-lg text-gray-700 dark:text-zinc-300">Product not found. Please select a valid product.</p>
            </div>
        @endif
    </div>
</div>
