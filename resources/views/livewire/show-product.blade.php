<flux:modal name="show-product" maxWidth="2xl">
    <div class="relative min-h-[500px] min-w-[500px]"> <!-- Added min-height for loading state -->
        <!-- Loading Spinner (shown while product is loading) -->
        <div wire:loading.flex class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 z-10 dark:bg-zinc-800 dark:bg-opacity-80">
            <svg class="animate-spin h-12 w-12 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <div wire:loading.remove>
            <!-- Product Details Section -->
            @if($this->product)
                <div class="bg-white dark:bg-zinc-800 p-6 rounded-lg">
                    <h2 class="text-2xl font-semibold text-gray-800 dark:text-zinc-100 mb-4">Product Details</h2>

                    <div class="mb-4">
                        <span class="text-gray-600 dark:text-zinc-400 font-medium">Name:</span>
                        <p class="text-lg text-gray-900 dark:text-zinc-200">{{ $this->product->name }}</p>
                    </div>

                    <div class="mb-4">
                        <span class="text-gray-600 dark:text-zinc-400 font-medium">Description:</span>
                        <p class="text-gray-700 dark:text-zinc-300">{{ $this->product->description ?? 'No description available' }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <span class="text-gray-600 dark:text-zinc-400 font-medium">Category:</span>
                            <p class="text-gray-900 dark:text-zinc-200">{{ $this->product->category }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-zinc-400 font-medium">SKU:</span>
                            <p class="text-gray-900 dark:text-zinc-200">{{ $this->product->sku }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <span class="text-gray-600 dark:text-zinc-400 font-medium">Price:</span>
                            <p class="text-lg font-semibold text-gray-900 dark:text-zinc-200">${{ number_format($this->product->price, 2) }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-zinc-400 font-medium">Cost:</span>
                            <p class="text-gray-900 dark:text-zinc-200">${{ number_format($this->product->cost ?? 0, 2) }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <span class="text-gray-600 dark:text-zinc-400 font-medium">Quantity in Stock:</span>
                            <p class="text-gray-900 dark:text-zinc-200">{{ $this->product->quantity_in_stock }}</p>
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

                    <div class="mt-6 flex items-center justify-between">
                        <flux:button class="bg-blue-500 dark:bg-zinc-700 text-white">
                            Edit Product
                        </flux:button>
                        <flux:modal.trigger name="delete-product">
                            <flux:button variant="danger">Delete Product</flux:button>
                        </flux:modal.trigger>
                    </div>
                </div>

                <flux:modal name="delete-product" class="min-w-[22rem]">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg" class="text-gray-900 dark:text-zinc-100">Delete product?</flux:heading>
                            <flux:text class="mt-2 text-gray-700 dark:text-zinc-300">
                                <p>You're about to delete this product.</p>
                                <p>This action cannot be reversed.</p>
                            </flux:text>
                        </div>
                        <div class="flex gap-2">
                            <flux:spacer />
                            <flux:modal.close>
                                <flux:button variant="ghost">Cancel</flux:button>
                            </flux:modal.close>
                            <flux:button type="submit" wire:click="delete()" variant="danger">Delete</flux:button>
                        </div>
                    </div>
                </flux:modal>
            @else
                <div class="bg-white dark:bg-zinc-800 p-6 rounded-lg shadow-md text-center">
                    <p class="text-lg text-gray-700 dark:text-zinc-300">Product not found. Please select a valid product.</p>
                </div>
            @endif
        </div>
    </div>
</flux:modal>
