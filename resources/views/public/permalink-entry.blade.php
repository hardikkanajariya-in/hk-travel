<x-layouts.app>
    <livewire:dynamic-component
        :component="$livewireComponent"
        :slug="$slug"
        :key="$livewireComponent.'-'.$slug"
    />
</x-layouts.app>
