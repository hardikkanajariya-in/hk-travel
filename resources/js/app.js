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

    /**
     * Custom listbox/combobox backing <x-ui.select>.
     *
     * Renders a styled dropdown panel on top of a hidden native <select>.
     * The native element is the source of truth (and preserves wire:model
     * bindings); selecting an option here mirrors the value back and fires
     * a `change` event so Livewire / forms see the update.
     */
    window.Alpine.data('hkSelect', ({ initial = '', options = {}, placeholder = '', searchable = false } = {}) => ({
        open: false,
        value: String(initial ?? ''),
        query: '',
        focusedIndex: -1,
        placeholder: placeholder ?? '',
        searchable: !!searchable,
        options: Object.entries(options || {}).map(([k, v]) => ({ value: String(k), label: String(v) })),

        init() {
            const native = this.$refs.native;
            if (native && native.value !== this.value) {
                this.value = String(native.value ?? '');
            }
            // Keep our state in sync if the value is changed by Livewire / external code.
            if (native) {
                native.addEventListener('change', () => {
                    this.value = String(native.value ?? '');
                });
            }
        },

        currentLabel() {
            const found = this.options.find((o) => o.value === this.value);
            if (found) return found.label;
            return this.placeholder || '';
        },

        filtered() {
            if (!this.query) return this.options;
            const q = this.query.toLowerCase();
            return this.options.filter((o) => o.label.toLowerCase().includes(q));
        },

        toggle() {
            this.open ? this.close() : this.openPanel();
        },

        openPanel() {
            this.open = true;
            const list = this.filtered();
            this.focusedIndex = Math.max(0, list.findIndex((o) => o.value === this.value));
            this.$nextTick(() => {
                if (this.searchable && this.$refs.search) {
                    this.$refs.search.focus();
                }
            });
        },

        close() {
            this.open = false;
            this.query = '';
            this.focusedIndex = -1;
        },

        select(val) {
            this.value = String(val);
            const native = this.$refs.native;
            if (native) {
                native.value = this.value;
                native.dispatchEvent(new Event('input', { bubbles: true }));
                native.dispatchEvent(new Event('change', { bubbles: true }));
            }
            this.close();
            this.$refs.trigger?.focus();
        },

        focusNext() {
            const list = this.filtered();
            if (!list.length) return;
            this.focusedIndex = (this.focusedIndex + 1) % list.length;
        },

        focusPrev() {
            const list = this.filtered();
            if (!list.length) return;
            this.focusedIndex = this.focusedIndex <= 0 ? list.length - 1 : this.focusedIndex - 1;
        },

        selectFocused() {
            const list = this.filtered();
            const opt = list[this.focusedIndex];
            if (opt) this.select(opt.value);
        },
    }));
});

Livewire.start();
