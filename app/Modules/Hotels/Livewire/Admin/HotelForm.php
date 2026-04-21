<?php

namespace App\Modules\Hotels\Livewire\Admin;

use App\Modules\Destinations\Models\Destination;
use App\Modules\Hotels\Models\Hotel;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Edit hotel')]
#[Layout('components.layouts.admin')]
class HotelForm extends Component
{
    public ?Hotel $hotel = null;

    public ?string $destination_id = null;

    #[Validate('required|string|max:160')]
    public string $name = '';

    #[Validate('required|string|max:200')]
    public string $slug = '';

    #[Validate('required|integer|min:1|max:5')]
    public int $star_rating = 3;

    #[Validate('nullable|string|max:10000')]
    public ?string $description = null;

    #[Validate('nullable|string|max:255')]
    public ?string $cover_image = null;

    #[Validate('nullable|string|max:255')]
    public ?string $address = null;

    #[Validate('nullable|numeric|between:-90,90')]
    public ?float $lat = null;

    #[Validate('nullable|numeric|between:-180,180')]
    public ?float $lng = null;

    #[Validate('required|string|max:8')]
    public string $check_in = '15:00';

    #[Validate('required|string|max:8')]
    public string $check_out = '11:00';

    #[Validate('required|numeric|min:0')]
    public float $price_from = 0;

    #[Validate('required|string|size:3')]
    public string $currency = 'USD';

    /** @var array<int, string> */
    public array $amenities = [];

    public string $newAmenity = '';

    public bool $is_published = false;

    public bool $is_featured = false;

    public function mount(?string $id = null): void
    {
        if ($id) {
            $this->authorize('hotels.update');
            $this->hotel = Hotel::query()->findOrFail($id);
            $this->fill($this->hotel->only([
                'destination_id', 'name', 'slug', 'star_rating', 'description', 'cover_image',
                'address', 'lat', 'lng', 'check_in', 'check_out', 'price_from', 'currency',
                'is_published', 'is_featured',
            ]));
            $this->amenities = (array) ($this->hotel->amenities ?? []);
        } else {
            $this->authorize('hotels.create');
        }
    }

    public function updatedName(string $value): void
    {
        if (! $this->slug || $this->hotel === null) {
            $this->slug = Str::slug($value);
        }
    }

    public function addAmenity(): void
    {
        if (filled($this->newAmenity)) {
            $this->amenities[] = trim($this->newAmenity);
            $this->newAmenity = '';
        }
    }

    public function removeAmenity(int $i): void
    {
        unset($this->amenities[$i]);
        $this->amenities = array_values($this->amenities);
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'destination_id' => $this->destination_id ?: null,
            'name' => $this->name,
            'slug' => $this->slug,
            'star_rating' => $this->star_rating,
            'description' => $this->description,
            'cover_image' => $this->cover_image,
            'address' => $this->address,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'price_from' => $this->price_from,
            'currency' => strtoupper($this->currency),
            'amenities' => $this->amenities,
            'is_published' => $this->is_published,
            'is_featured' => $this->is_featured,
        ];

        if ($this->hotel) {
            $this->hotel->update($data);
        } else {
            $this->hotel = Hotel::create($data);
        }

        session()->flash('status', __('Hotel saved.'));
        $this->redirectRoute('admin.hotels.index', navigate: true);
    }

    public function render(): View
    {
        return view('hotels::admin.form', [
            'destinations' => Schema::hasTable('destinations')
                ? Destination::query()->where('is_published', true)->orderBy('name')->limit(500)->get(['id', 'name'])
                : collect(),
        ]);
    }
}
