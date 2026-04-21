<div class="space-y-6 max-w-4xl">
    <h1 class="text-2xl font-semibold">{{ $service ? 'Edit visa service' : 'New visa service' }}</h1>

    <form wire:submit="save" class="space-y-6">
        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Country & type</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-ui.input wire:model="country" label="Country" required :error="$errors->first('country')" />
                <x-ui.input wire:model="country_code" label="ISO code" maxlength="2" />
                <x-ui.input wire:model="visa_type" label="Visa type" required :error="$errors->first('visa_type')" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <x-ui.input wire:model.blur="title" label="Title" required :error="$errors->first('title')" />
                <x-ui.input wire:model="slug" label="Slug" required :error="$errors->first('slug')" />
            </div>
            <x-ui.textarea wire:model="description" label="Description" rows="4" class="mt-4" />
            <x-ui.textarea wire:model="eligibility" label="Eligibility" rows="3" class="mt-4" />
            <x-ui.textarea wire:model="notes" label="Notes" rows="3" class="mt-4" />
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Processing & validity</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <x-ui.input type="number" wire:model="processing_days_min" label="Min days" />
                <x-ui.input type="number" wire:model="processing_days_max" label="Max days" />
                <x-ui.input type="number" wire:model="allowed_stay_days" label="Allowed stay" />
                <x-ui.input type="number" wire:model="validity_days" label="Validity (days)" />
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Fees</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-ui.input type="number" step="0.01" wire:model="fee" label="Govt fee" />
                <x-ui.input type="number" step="0.01" wire:model="service_fee" label="Service fee" />
                <x-ui.input wire:model="currency" label="Currency" />
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Requirements</h2>
            <div class="flex gap-2">
                <x-ui.input wire:model="newRequirement" placeholder="e.g. Valid passport" class="flex-1" />
                <x-ui.button type="button" variant="secondary" wire:click="addRequirement">Add</x-ui.button>
            </div>
            <ul class="mt-3 space-y-1">
                @foreach ($requirements as $i => $r)
                    <li class="flex items-center justify-between gap-2 text-sm" wire:key="req-{{ $i }}">
                        <span>• {{ $r }}</span>
                        <button type="button" wire:click="removeRequirement({{ $i }})" class="text-hk-danger text-xs">Remove</button>
                    </li>
                @endforeach
            </ul>
        </x-ui.card>

        <x-ui.card>
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 mb-4">Required documents</h2>
            <div class="flex gap-2">
                <x-ui.input wire:model="newDocument" placeholder="e.g. Passport scan" class="flex-1" />
                <x-ui.button type="button" variant="secondary" wire:click="addDocument">Add</x-ui.button>
            </div>
            <ul class="mt-3 space-y-1">
                @foreach ($documents as $i => $d)
                    <li class="flex items-center justify-between gap-2 text-sm" wire:key="doc-{{ $i }}">
                        <span>• {{ $d }}</span>
                        <button type="button" wire:click="removeDocument({{ $i }})" class="text-hk-danger text-xs">Remove</button>
                    </li>
                @endforeach
            </ul>
        </x-ui.card>

        <x-ui.card>
            <div class="flex items-center gap-6">
                <x-ui.checkbox wire:model="is_published" label="Published" />
                <x-ui.checkbox wire:model="is_featured" label="Featured" />
            </div>
        </x-ui.card>

        <div class="flex items-center gap-3">
            <x-ui.button type="submit" variant="primary">Save</x-ui.button>
            <a href="{{ route('admin.visa.index') }}" wire:navigate><x-ui.button variant="ghost" type="button">Cancel</x-ui.button></a>
        </div>
    </form>
</div>
