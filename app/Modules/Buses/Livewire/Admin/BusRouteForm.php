<?php

namespace App\Modules\Buses\Livewire\Admin;

use App\Modules\Buses\Models\BusRoute;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Edit bus route')]
#[Layout('components.layouts.admin')]
class BusRouteForm extends Component
{
    public ?BusRoute $route = null;

    #[Validate('required|string|max:160')]
    public string $title = '';

    #[Validate('required|string|max:200')]
    public string $slug = '';

    #[Validate('required|string|max:120')]
    public string $operator = '';

    #[Validate('required|string|in:standard,ac,sleeper,luxury')]
    public string $bus_type = 'standard';

    #[Validate('required|string|max:120')]
    public string $origin = '';

    #[Validate('required|string|max:120')]
    public string $destination = '';

    #[Validate('required|string|max:8')]
    public string $departure_time = '08:00';

    #[Validate('required|string|max:8')]
    public string $arrival_time = '14:00';

    #[Validate('required|integer|min:1|max:2880')]
    public int $duration_minutes = 360;

    #[Validate('required|integer|min:0')]
    public int $distance_km = 0;

    /** @var array<int, string> */
    public array $schedule_days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

    #[Validate('nullable|string|max:5000')]
    public ?string $description = null;

    #[Validate('required|numeric|min:0')]
    public float $fare = 0;

    #[Validate('required|string|size:3')]
    public string $currency = 'USD';

    public bool $is_published = false;

    public bool $is_featured = false;

    public function mount(?string $id = null): void
    {
        if ($id) {
            $this->authorize('buses.update');
            $this->route = BusRoute::query()->findOrFail($id);
            $this->fill($this->route->only([
                'title', 'slug', 'operator', 'bus_type', 'origin', 'destination',
                'departure_time', 'arrival_time', 'duration_minutes', 'distance_km',
                'description', 'fare', 'currency', 'is_published', 'is_featured',
            ]));
            $this->schedule_days = (array) ($this->route->schedule_days ?? []);
        } else {
            $this->authorize('buses.create');
        }
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->slug || $this->route === null) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'title' => $this->title, 'slug' => $this->slug, 'operator' => $this->operator,
            'bus_type' => $this->bus_type, 'origin' => $this->origin, 'destination' => $this->destination,
            'departure_time' => $this->departure_time, 'arrival_time' => $this->arrival_time,
            'duration_minutes' => $this->duration_minutes, 'distance_km' => $this->distance_km,
            'schedule_days' => array_values($this->schedule_days), 'description' => $this->description,
            'fare' => $this->fare, 'currency' => strtoupper($this->currency),
            'is_published' => $this->is_published, 'is_featured' => $this->is_featured,
        ];

        if ($this->route) {
            $this->route->update($data);
        } else {
            $this->route = BusRoute::create($data);
        }

        session()->flash('status', __('Bus route saved.'));
        $this->redirectRoute('admin.buses.index', navigate: true);
    }

    public function render(): View
    {
        return view('buses::admin.form');
    }
}
