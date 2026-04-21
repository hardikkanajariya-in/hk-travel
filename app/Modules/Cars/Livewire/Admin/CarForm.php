<?php

namespace App\Modules\Cars\Livewire\Admin;

use App\Modules\Cars\Models\CarRental;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Edit car rental')]
#[Layout('components.layouts.admin')]
class CarForm extends Component
{
    public ?CarRental $car = null;

    #[Validate('required|string|max:160')]
    public string $name = '';

    #[Validate('required|string|max:200')]
    public string $slug = '';

    #[Validate('required|string|max:60')]
    public string $vehicle_class = 'economy';

    #[Validate('nullable|string|max:60')]
    public ?string $make = null;

    #[Validate('nullable|string|max:60')]
    public ?string $model = null;

    #[Validate('nullable|string|max:20000')]
    public ?string $description = null;

    #[Validate('nullable|string|max:255')]
    public ?string $cover_image = null;

    #[Validate('required|integer|min:1|max:30')]
    public int $seats = 5;

    #[Validate('required|integer|min:1|max:10')]
    public int $doors = 4;

    #[Validate('required|integer|min:0|max:20')]
    public int $luggage = 2;

    #[Validate('required|string|in:automatic,manual')]
    public string $transmission = 'automatic';

    #[Validate('required|string|in:petrol,diesel,hybrid,electric')]
    public string $fuel_type = 'petrol';

    public bool $has_ac = true;

    #[Validate('required|numeric|min:0')]
    public float $daily_rate = 0;

    #[Validate('required|numeric|min:0')]
    public float $weekly_rate = 0;

    #[Validate('required|string|size:3')]
    public string $currency = 'USD';

    public bool $is_published = false;

    public bool $is_featured = false;

    public function mount(?string $id = null): void
    {
        if ($id) {
            $this->authorize('cars.update');
            $this->car = CarRental::query()->findOrFail($id);
            $this->fill($this->car->only([
                'name', 'slug', 'vehicle_class', 'make', 'model', 'description', 'cover_image',
                'seats', 'doors', 'luggage', 'transmission', 'fuel_type', 'has_ac',
                'daily_rate', 'weekly_rate', 'currency', 'is_published', 'is_featured',
            ]));
        } else {
            $this->authorize('cars.create');
        }
    }

    public function updatedName(string $value): void
    {
        if (! $this->slug || $this->car === null) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name, 'slug' => $this->slug, 'vehicle_class' => $this->vehicle_class,
            'make' => $this->make, 'model' => $this->model, 'description' => $this->description,
            'cover_image' => $this->cover_image, 'seats' => $this->seats, 'doors' => $this->doors,
            'luggage' => $this->luggage, 'transmission' => $this->transmission, 'fuel_type' => $this->fuel_type,
            'has_ac' => $this->has_ac, 'daily_rate' => $this->daily_rate, 'weekly_rate' => $this->weekly_rate,
            'currency' => strtoupper($this->currency), 'is_published' => $this->is_published, 'is_featured' => $this->is_featured,
        ];

        if ($this->car) {
            $this->car->update($data);
        } else {
            $this->car = CarRental::create($data);
        }

        session()->flash('status', __('Car rental saved.'));
        $this->redirectRoute('admin.cars.index', navigate: true);
    }

    public function render(): View
    {
        return view('cars::admin.form');
    }
}
