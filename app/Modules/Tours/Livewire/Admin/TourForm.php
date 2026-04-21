<?php

namespace App\Modules\Tours\Livewire\Admin;

use App\Modules\Destinations\Models\Destination;
use App\Modules\Tours\Models\Tour;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Edit tour')]
#[Layout('components.layouts.admin')]
class TourForm extends Component
{
    public ?Tour $tour = null;

    public ?string $destination_id = null;

    #[Validate('required|string|max:160')]
    public string $name = '';

    #[Validate('required|string|max:200')]
    public string $slug = '';

    #[Validate('nullable|string|max:10000')]
    public ?string $description = null;

    #[Validate('nullable|string|max:255')]
    public ?string $cover_image = null;

    #[Validate('required|numeric|min:0')]
    public float $price = 0;

    #[Validate('nullable|numeric|min:0')]
    public ?float $discount_price = null;

    #[Validate('required|string|size:3')]
    public string $currency = 'USD';

    #[Validate('required|integer|min:1|max:365')]
    public int $duration_days = 1;

    #[Validate('required|integer|min:1|max:200')]
    public int $max_group_size = 10;

    #[Validate('required|in:easy,moderate,challenging,extreme')]
    public string $difficulty = 'easy';

    #[Validate('required|string|max:8')]
    public string $language = 'en';

    /** @var array<int, string> */
    public array $inclusions = [];

    /** @var array<int, string> */
    public array $exclusions = [];

    public string $newInclusion = '';

    public string $newExclusion = '';

    public bool $is_published = false;

    public bool $is_featured = false;

    public function mount(?string $id = null): void
    {
        if ($id) {
            $this->authorize('tours.update');
            $this->tour = Tour::query()->findOrFail($id);
            $this->fill($this->tour->only([
                'destination_id', 'name', 'slug', 'description', 'cover_image',
                'price', 'discount_price', 'currency', 'duration_days', 'max_group_size',
                'difficulty', 'language', 'is_published', 'is_featured',
            ]));
            $this->inclusions = (array) ($this->tour->inclusions ?? []);
            $this->exclusions = (array) ($this->tour->exclusions ?? []);
        } else {
            $this->authorize('tours.create');
        }
    }

    public function updatedName(string $value): void
    {
        if (! $this->slug || $this->tour === null) {
            $this->slug = Str::slug($value);
        }
    }

    public function addInclusion(): void
    {
        if (filled($this->newInclusion)) {
            $this->inclusions[] = trim($this->newInclusion);
            $this->newInclusion = '';
        }
    }

    public function removeInclusion(int $i): void
    {
        unset($this->inclusions[$i]);
        $this->inclusions = array_values($this->inclusions);
    }

    public function addExclusion(): void
    {
        if (filled($this->newExclusion)) {
            $this->exclusions[] = trim($this->newExclusion);
            $this->newExclusion = '';
        }
    }

    public function removeExclusion(int $i): void
    {
        unset($this->exclusions[$i]);
        $this->exclusions = array_values($this->exclusions);
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'destination_id' => $this->destination_id ?: null,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'cover_image' => $this->cover_image,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'currency' => strtoupper($this->currency),
            'duration_days' => $this->duration_days,
            'max_group_size' => $this->max_group_size,
            'difficulty' => $this->difficulty,
            'language' => $this->language,
            'inclusions' => $this->inclusions,
            'exclusions' => $this->exclusions,
            'is_published' => $this->is_published,
            'is_featured' => $this->is_featured,
            'published_at' => $this->is_published && ! ($this->tour?->published_at) ? now() : ($this->tour?->published_at),
        ];

        if ($this->tour) {
            $this->tour->update($data);
        } else {
            $this->tour = Tour::create($data);
        }

        session()->flash('status', __('Tour saved.'));
        $this->redirectRoute('admin.tours.index', navigate: true);
    }

    public function render(): View
    {
        return view('tours::admin.form', [
            'destinations' => Schema::hasTable('destinations')
                ? Destination::query()->where('is_published', true)->orderBy('name')->limit(500)->get(['id', 'name'])
                : collect(),
        ]);
    }
}
