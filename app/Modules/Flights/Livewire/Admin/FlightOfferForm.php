<?php

namespace App\Modules\Flights\Livewire\Admin;

use App\Modules\Flights\Models\FlightOffer;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Edit flight offer')]
#[Layout('components.layouts.admin')]
class FlightOfferForm extends Component
{
    public ?FlightOffer $offer = null;

    #[Validate('required|string|max:120')]
    public string $airline = '';

    #[Validate('required|string|max:4')]
    public string $airline_code = '';

    #[Validate('required|string|max:12')]
    public string $flight_number = '';

    #[Validate('required|string|size:3')]
    public string $origin = '';

    #[Validate('required|string|size:3')]
    public string $destination = '';

    #[Validate('required|string|max:50')]
    public string $depart_time = '';

    #[Validate('required|string|max:50')]
    public string $arrive_time = '';

    #[Validate('required|integer|min:0|max:2880')]
    public int $duration_minutes = 0;

    #[Validate('required|integer|min:0|max:5')]
    public int $stops = 0;

    #[Validate('required|string|in:economy,premium_economy,business,first')]
    public string $cabin = 'economy';

    #[Validate('required|numeric|min:0')]
    public float $price = 0;

    #[Validate('required|string|size:3')]
    public string $currency = 'USD';

    public bool $is_published = true;

    public bool $is_featured = false;

    public function mount(?string $id = null): void
    {
        if ($id) {
            $this->authorize('flights.update');
            $this->offer = FlightOffer::query()->findOrFail($id);
            $this->fill($this->offer->only([
                'airline', 'airline_code', 'flight_number', 'origin', 'destination',
                'depart_time', 'arrive_time', 'duration_minutes', 'stops', 'cabin',
                'price', 'currency', 'is_published', 'is_featured',
            ]));
        } else {
            $this->authorize('flights.create');
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'airline' => $this->airline, 'airline_code' => strtoupper($this->airline_code), 'flight_number' => strtoupper($this->flight_number),
            'origin' => strtoupper($this->origin), 'destination' => strtoupper($this->destination),
            'depart_time' => $this->depart_time, 'arrive_time' => $this->arrive_time,
            'duration_minutes' => $this->duration_minutes, 'stops' => $this->stops, 'cabin' => $this->cabin,
            'price' => $this->price, 'currency' => strtoupper($this->currency),
            'is_published' => $this->is_published, 'is_featured' => $this->is_featured,
        ];

        if ($this->offer) {
            $this->offer->update($data);
        } else {
            $this->offer = FlightOffer::create($data);
        }

        session()->flash('status', __('Flight offer saved.'));
        $this->redirectRoute('admin.flights.index', navigate: true);
    }

    public function render(): View
    {
        return view('flights::admin.form');
    }
}
