<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Appearance settings')] #[Layout('components.layouts.admin')] class extends Component {
};

?>

<div>
    <x-settings.shell :heading="__('Appearance')" :subheading="__('Update the appearance settings for your account')">
        <div
            x-data="{
                theme: localStorage.getItem('hk-theme') || 'system',
                apply() {
                    const root = document.documentElement;
                    const dark = this.theme === 'dark' || (this.theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                    root.classList.toggle('dark', dark);
                    localStorage.setItem('hk-theme', this.theme);
                },
            }"
            x-init="apply()"
            class="inline-flex rounded-md border border-zinc-200 dark:border-zinc-700 p-1 bg-zinc-50 dark:bg-zinc-900"
        >
            @foreach (['light' => __('Light'), 'dark' => __('Dark'), 'system' => __('System')] as $value => $label)
                <label class="cursor-pointer">
                    <input type="radio" x-model="theme" @change="apply()" value="{{ $value }}" class="sr-only peer">
                    <span class="px-3 py-1.5 text-sm rounded peer-checked:bg-white dark:peer-checked:bg-zinc-700 peer-checked:shadow-sm peer-checked:font-medium block">
                        {{ $label }}
                    </span>
                </label>
            @endforeach
        </div>
    </x-settings.shell>
</div>
