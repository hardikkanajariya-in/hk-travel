/*
 * Manually bundled Livewire + Alpine entry point.
 *
 * The app's layouts use @livewireScriptConfig (not @livewireScripts),
 * which only injects runtime config — the actual Livewire/Alpine
 * runtime must be imported here. Alpine plugins register on the
 * `alpine:init` event so they bind to Livewire's bundled Alpine.
 */
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import intersect from '@alpinejs/intersect';
import sort from '@alpinejs/sort';

document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(collapse);
    window.Alpine.plugin(focus);
    window.Alpine.plugin(intersect);
    window.Alpine.plugin(sort);
});

Livewire.start();
