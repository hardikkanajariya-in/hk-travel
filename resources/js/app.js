/*
 * Alpine plugins are registered against Livewire 4's bundled Alpine
 * via the `alpine:init` event, so we don't ship a duplicate Alpine instance.
 */
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
