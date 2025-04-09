<div class="space-y-6">
    <div>
        <flux:heading size="lg">Delete {{ $subject }}?</flux:heading>
        <flux:text class="mt-2">
            <p>You're about to delete this {{ $subject }}.</p>
            <p>This action cannot be reversed.</p>
        </flux:text>
    </div>
    <div class="flex gap-2">
        <flux:spacer />
        <flux:modal.close>
            <flux:button variant="ghost">Cancel</flux:button>
        </flux:modal.close>
        <flux:button type="submit" wire:click="delete" variant="danger">Delete {{ $subject }}</flux:button>
    </div>
</div>
