<?php

namespace App\Modules\Taxi\Livewire\Admin;

use App\Modules\Taxi\Models\TaxiService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Edit taxi service')]
#[Layout('components.layouts.admin')]
class TaxiForm extends Component
{
    public ?TaxiService $service = null;

    #[Validate('required|string|max:160')]
    public string $title = '';

    #[Validate('required|string|max:200')]
    public string $slug = '';

    #[Validate('required|string|in:airport_transfer,hourly,point_to_point')]
    public string $service_type = 'airport_transfer';

    #[Validate('required|string|max:60')]
    public string $vehicle_type = 'Sedan';

    #[Validate('nullable|string|max:20000')]
    public ?string $description = null;

    #[Validate('nullable|string|max:255')]
    public ?string $cover_image = null;

    #[Validate('required|integer|min:1|max:30')]
    public int $capacity = 4;

    #[Validate('required|integer|min:0|max:20')]
    public int $luggage = 2;

    #[Validate('required|numeric|min:0')]
    public float $base_fare = 0;

    #[Validate('required|numeric|min:0')]
    public float $per_km_rate = 0;

    #[Validate('required|numeric|min:0')]
    public float $per_hour_rate = 0;

    #[Validate('required|numeric|min:0')]
    public float $flat_rate = 0;

    #[Validate('required|string|size:3')]
    public string $currency = 'USD';

    public bool $is_published = false;

    public bool $is_featured = false;

    public function mount(?string $id = null): void
    {
        if ($id) {
            $this->authorize('taxi.update');
            $this->service = TaxiService::query()->findOrFail($id);
            $this->fill($this->service->only([
                'title', 'slug', 'service_type', 'vehicle_type', 'description', 'cover_image',
                'capacity', 'luggage', 'base_fare', 'per_km_rate', 'per_hour_rate', 'flat_rate',
                'currency', 'is_published', 'is_featured',
            ]));
        } else {
            $this->authorize('taxi.create');
        }
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->slug || $this->service === null) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'title' => $this->title, 'slug' => $this->slug, 'service_type' => $this->service_type,
            'vehicle_type' => $this->vehicle_type, 'description' => $this->description, 'cover_image' => $this->cover_image,
            'capacity' => $this->capacity, 'luggage' => $this->luggage,
            'base_fare' => $this->base_fare, 'per_km_rate' => $this->per_km_rate,
            'per_hour_rate' => $this->per_hour_rate, 'flat_rate' => $this->flat_rate,
            'currency' => strtoupper($this->currency), 'is_published' => $this->is_published, 'is_featured' => $this->is_featured,
        ];

        if ($this->service) {
            $this->service->update($data);
        } else {
            $this->service = TaxiService::create($data);
        }

        session()->flash('status', __('Taxi service saved.'));
        $this->redirectRoute('admin.taxi.index', navigate: true);
    }

    public function render(): View
    {
        return view('taxi::admin.form');
    }
}
