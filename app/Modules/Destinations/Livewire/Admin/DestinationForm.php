<?php

namespace App\Modules\Destinations\Livewire\Admin;

use App\Modules\Destinations\Models\Destination;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Edit destination')]
#[Layout('components.layouts.admin')]
class DestinationForm extends Component
{
    public ?Destination $destination = null;

    public ?string $parent_id = null;

    #[Validate('required|in:country,region,city,area,poi')]
    public string $type = 'city';

    #[Validate('required|string|max:160')]
    public string $name = '';

    #[Validate('required|string|max:200')]
    public string $slug = '';

    #[Validate('nullable|string|size:2')]
    public ?string $country_code = null;

    #[Validate('nullable|string|max:5000')]
    public ?string $description = null;

    #[Validate('nullable|string|max:2000')]
    public ?string $highlights = null;

    #[Validate('nullable|string|max:255')]
    public ?string $cover_image = null;

    #[Validate('nullable|numeric|between:-90,90')]
    public ?float $lat = null;

    #[Validate('nullable|numeric|between:-180,180')]
    public ?float $lng = null;

    public bool $is_featured = false;

    public bool $is_published = false;

    public function mount(?string $id = null): void
    {
        if ($id) {
            $this->authorize('destinations.update');
            $this->destination = Destination::query()->findOrFail($id);
            $this->fill($this->destination->only([
                'parent_id', 'type', 'name', 'slug', 'country_code', 'description',
                'highlights', 'cover_image', 'lat', 'lng', 'is_featured', 'is_published',
            ]));
        } else {
            $this->authorize('destinations.create');
        }
    }

    public function updatedName(string $value): void
    {
        if (! $this->slug || ($this->destination === null)) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'parent_id' => $this->parent_id ?: null,
            'type' => $this->type,
            'name' => $this->name,
            'slug' => $this->slug,
            'country_code' => $this->country_code ? strtoupper($this->country_code) : null,
            'description' => $this->description,
            'highlights' => $this->highlights,
            'cover_image' => $this->cover_image,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'is_featured' => $this->is_featured,
            'is_published' => $this->is_published,
        ];

        if ($this->destination) {
            $this->destination->update($data);
        } else {
            $this->destination = Destination::create($data);
        }

        session()->flash('status', __('Destination saved.'));
        $this->redirectRoute('admin.destinations.index', navigate: true);
    }

    public function render(): View
    {
        return view('destinations::admin.form', [
            'parents' => Destination::query()
                ->when($this->destination, fn ($q) => $q->whereKeyNot($this->destination->id))
                ->orderBy('name')
                ->limit(500)
                ->get(['id', 'name', 'type']),
        ]);
    }
}
