<?php

namespace App\Modules\Activities\Livewire\Admin;

use App\Modules\Activities\Models\Activity;
use App\Modules\Destinations\Models\Destination;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Edit activity')]
#[Layout('components.layouts.admin')]
class ActivityForm extends Component
{
    public ?Activity $activity = null;

    public ?string $destination_id = null;

    #[Validate('required|string|max:160')]
    public string $name = '';

    #[Validate('required|string|max:200')]
    public string $slug = '';

    #[Validate('nullable|string|max:60')]
    public ?string $category = null;

    #[Validate('nullable|string|max:500')]
    public ?string $short_description = null;

    #[Validate('nullable|string|max:20000')]
    public ?string $description = null;

    #[Validate('nullable|string|max:255')]
    public ?string $cover_image = null;

    #[Validate('required|numeric|min:0.5|max:24')]
    public float $duration_hours = 2;

    #[Validate('required|numeric|min:0')]
    public float $price = 0;

    #[Validate('required|string|size:3')]
    public string $currency = 'USD';

    #[Validate('required|integer|min:0|max:99')]
    public int $min_age = 0;

    #[Validate('required|integer|min:1|max:500')]
    public int $max_group_size = 20;

    #[Validate('required|string|in:easy,moderate,challenging')]
    public string $difficulty = 'easy';

    public bool $is_published = false;

    public bool $is_featured = false;

    public function mount(?string $id = null): void
    {
        if ($id) {
            $this->authorize('activities.update');
            $this->activity = Activity::query()->findOrFail($id);
            $this->fill($this->activity->only([
                'destination_id', 'name', 'slug', 'category', 'short_description', 'description', 'cover_image',
                'duration_hours', 'price', 'currency', 'min_age', 'max_group_size', 'difficulty',
                'is_published', 'is_featured',
            ]));
        } else {
            $this->authorize('activities.create');
        }
    }

    public function updatedName(string $value): void
    {
        if (! $this->slug || $this->activity === null) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'destination_id' => $this->destination_id ?: null,
            'name' => $this->name,
            'slug' => $this->slug,
            'category' => $this->category,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'cover_image' => $this->cover_image,
            'duration_hours' => $this->duration_hours,
            'price' => $this->price,
            'currency' => strtoupper($this->currency),
            'min_age' => $this->min_age,
            'max_group_size' => $this->max_group_size,
            'difficulty' => $this->difficulty,
            'is_published' => $this->is_published,
            'is_featured' => $this->is_featured,
        ];

        if ($this->activity) {
            $this->activity->update($data);
        } else {
            $this->activity = Activity::create($data);
        }

        session()->flash('status', __('Activity saved.'));
        $this->redirectRoute('admin.activities.index', navigate: true);
    }

    public function render(): View
    {
        return view('activities::admin.form', [
            'destinations' => Schema::hasTable('destinations')
                ? Destination::query()->where('is_published', true)->orderBy('name')->limit(500)->get(['id', 'name'])
                : collect(),
        ]);
    }
}
