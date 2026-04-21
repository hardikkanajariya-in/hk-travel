@props([
    'name' => null,
    'label' => null,
    'hint' => null,
    'error' => null,
    'value' => null,
    'folder' => 'media',
    'aspect' => 'aspect-video', // tailwind class for the preview frame
])

@php
    $modelKey = $attributes->wire('model')->value();
    $errorKey = $name ?? $modelKey;
    if (blank($error) && filled($errorKey) && isset($errors) && $errors->has($errorKey)) {
        $error = $errors->first($errorKey);
    }
    $hasError = filled($error);
    $pickerId = 'pick-'.\Illuminate\Support\Str::random(6);
@endphp

<div
    class="space-y-1.5"
    x-data="hkImagePicker({
        modelKey: @js($modelKey),
        initial: @js((string) ($value ?? '')),
        folder: @js($folder),
        uploadUrl: @js(route('admin.media.upload-image')),
        csrf: @js(csrf_token()),
    })"
>
    @if ($label)
        <label for="{{ $pickerId }}" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $label }}</label>
    @endif

    <div
        @click.prevent="$refs.input.click()"
        @dragover.prevent="dragging = true"
        @dragleave.prevent="dragging = false"
        @drop.prevent="dropFiles($event)"
        :class="dragging
            ? 'border-hk-primary-500 bg-hk-primary-50 dark:bg-hk-primary-950/30'
            : '{{ $hasError ? 'border-hk-danger' : 'border-zinc-300 dark:border-zinc-700' }}'"
        class="group relative flex cursor-pointer flex-col gap-3 rounded-xl border-2 border-dashed bg-white p-3 transition hover:border-hk-primary-400 dark:bg-zinc-900 @container"
    >
        {{-- Preview frame: full-width on top so it stays usable inside narrow sidebars/columns. --}}
        <div class="{{ $aspect }} flex w-full items-center justify-center overflow-hidden rounded-lg bg-zinc-100 dark:bg-zinc-800">
            <template x-if="url">
                <img :src="url" alt="" class="size-full object-cover" />
            </template>
            <template x-if="! url && ! uploading">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.4" stroke="currentColor" class="size-10 text-zinc-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                </svg>
            </template>
            <template x-if="uploading">
                <div class="flex flex-col items-center gap-1 text-xs text-zinc-500">
                    <svg class="size-5 animate-spin text-hk-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"></circle>
                        <path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
                    </svg>
                    <span x-text="progress + '%'"></span>
                </div>
            </template>
        </div>

        {{-- Info + actions: stacks on narrow widths, sits side-by-side once we have room. --}}
        <div class="flex flex-col gap-2 @sm:flex-row @sm:items-center @sm:justify-between">
            <div class="min-w-0 flex-1 text-sm">
                <p class="font-medium text-zinc-700 dark:text-zinc-200">
                    <span x-show="! url">Click or drop an image here</span>
                    <span x-show="url" x-cloak>Image uploaded</span>
                </p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">JPG, PNG, GIF, WebP or SVG. Up to 5 MB.</p>
                <p x-show="url" x-cloak class="mt-1 truncate font-mono text-[11px] text-zinc-400" x-text="url"></p>
            </div>

            <div class="flex shrink-0 gap-2" @click.stop>
                <button
                    type="button"
                    @click.prevent="$refs.input.click()"
                    class="rounded-md bg-hk-primary-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-hk-primary-700"
                >
                    <span x-show="! url">Choose</span>
                    <span x-show="url" x-cloak>Replace</span>
                </button>
                <button
                    type="button"
                    x-show="url"
                    x-cloak
                    @click.prevent="clear()"
                    class="rounded-md border border-zinc-200 bg-white px-3 py-1.5 text-xs font-medium text-zinc-700 hover:border-hk-danger hover:text-hk-danger dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200"
                >
                    Remove
                </button>
            </div>
        </div>

        <input
            x-ref="input"
            id="{{ $pickerId }}"
            type="file"
            class="sr-only"
            accept="image/*"
            @change="upload($event.target.files)"
        />
    </div>

    @if ($hasError)
        <p class="text-xs text-hk-danger">{{ $error }}</p>
    @elseif (filled($hint))
        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $hint }}</p>
    @endif

    <p x-show="message" x-cloak x-text="message" class="text-xs text-hk-danger"></p>
</div>

@once
    @push('scripts')
        <script>
            window.hkImagePicker = function ({ modelKey, initial, folder, uploadUrl, csrf }) {
                return {
                    url: initial || (modelKey ? (this.$wire?.get(modelKey) ?? '') : ''),
                    dragging: false,
                    uploading: false,
                    progress: 0,
                    message: '',

                    init() {
                        if (modelKey && this.$wire) {
                            const current = this.$wire.get(modelKey);
                            if (current) this.url = current;
                            this.$watch('url', (v) => {
                                if (this.$wire.get(modelKey) !== v) {
                                    this.$wire.set(modelKey, v ?? '');
                                }
                            });
                        }
                    },

                    dropFiles(e) {
                        this.dragging = false;
                        this.upload(e.dataTransfer.files);
                    },

                    async upload(files) {
                        this.message = '';
                        if (! files || ! files.length) return;
                        const file = files[0];
                        if (! file.type.startsWith('image/')) {
                            this.message = 'Please pick an image file.';
                            return;
                        }
                        if (file.size > 5 * 1024 * 1024) {
                            this.message = 'That image is larger than 5 MB. Please pick a smaller one.';
                            return;
                        }

                        const data = new FormData();
                        data.append('file', file);
                        data.append('folder', folder);

                        this.uploading = true;
                        this.progress = 0;
                        try {
                            const res = await new Promise((resolve, reject) => {
                                const xhr = new XMLHttpRequest();
                                xhr.open('POST', uploadUrl);
                                xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                                xhr.setRequestHeader('Accept', 'application/json');
                                xhr.upload.onprogress = (ev) => {
                                    if (ev.lengthComputable) this.progress = Math.round((ev.loaded / ev.total) * 100);
                                };
                                xhr.onload = () => {
                                    let payload = {};
                                    try { payload = JSON.parse(xhr.responseText); } catch (_) {}
                                    if (xhr.status >= 200 && xhr.status < 300) resolve(payload);
                                    else reject(payload?.message || 'Upload failed. Please try again.');
                                };
                                xhr.onerror = () => reject('Network error — please try again.');
                                xhr.send(data);
                            });
                            this.url = res.url;
                        } catch (err) {
                            this.message = typeof err === 'string' ? err : 'Upload failed. Please try again.';
                        } finally {
                            this.uploading = false;
                            this.progress = 0;
                        }
                    },

                    clear() {
                        this.url = '';
                    },
                };
            };
        </script>
    @endpush
@endonce
