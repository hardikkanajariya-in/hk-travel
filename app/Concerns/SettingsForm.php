<?php

namespace App\Concerns;

use App\Core\Settings\SettingsRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * Shared Livewire trait for "settings form" admin pages.
 *
 * The host component declares:
 *   - `array $state` — flat key/value bag bound to wire:model
 *   - `settingsKeys(): array<string,string>` — map of state-key => settings dotted path
 *   - `settingsRules(): array` — Laravel validation rules keyed on state keys
 *
 * The trait then handles loading defaults from SettingsRepository,
 * validating, persisting and flashing a success message.
 */
trait SettingsForm
{
    /** @var array<string, mixed> */
    public array $state = [];

    public function loadSettings(): void
    {
        $repo = app(SettingsRepository::class);
        foreach ($this->settingsKeys() as $stateKey => $settingsKey) {
            Arr::set($this->state, $stateKey, $repo->get($settingsKey, Arr::get($this->state, $stateKey)));
        }
    }

    public function saveSettings(): void
    {
        $this->validate($this->settingsRules());

        $repo = app(SettingsRepository::class);
        $changed = [];
        foreach ($this->settingsKeys() as $stateKey => $settingsKey) {
            $new = Arr::get($this->state, $stateKey);
            $old = $repo->get($settingsKey);
            if ($new !== $old) {
                $changed[$settingsKey] = ['from' => $old, 'to' => $new];
            }
            $repo->set($settingsKey, $new);
        }

        if ($changed !== []) {
            activity('settings')
                ->causedBy(Auth::user())
                ->withProperties(['changed' => $changed, 'component' => static::class])
                ->event('updated')
                ->log('settings.updated');
        }

        session()->flash('settings.saved', __('Settings updated.'));
    }

    /** @return array<string, string> */
    abstract protected function settingsKeys(): array;

    /** @return array<string, mixed> */
    abstract protected function settingsRules(): array;
}
