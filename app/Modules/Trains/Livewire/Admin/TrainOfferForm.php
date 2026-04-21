<?php

namespace App\Modules\Trains\Livewire\Admin;

use App\Modules\Trains\Models\TrainOffer;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Edit train offer')]
#[Layout('components.layouts.admin')]
class TrainOfferForm extends Component
{
    public ?TrainOffer $offer = null;

    #[Validate('required|string|max:120')]
    public string $operator = '';

    #[Validate('required|string|max:8')]
    public string $operator_code = '';

    #[Validate('required|string|max:16')]
    public string $train_number = '';

    #[Validate('required|string|max:8')]
    public string $origin = '';

    #[Validate('required|string|max:8')]
    public string $destination = '';

    #[Validate('required|string|max:50')]
    public string $depart_time = '';

    #[Validate('required|string|max:50')]
    public string $arrive_time = '';

    #[Validate('required|integer|min:0|max:2880')]
    public int $duration_minutes = 0;

    #[Validate('required|integer|min:0|max:5')]
    public int $changes = 0;

    #[Validate('required|string|in:standard,first,business,sleeper')]
    public string $class = 'standard';

    #[Validate('nullable|string|max:32')]
    public ?string $fare_type = null;

    public bool $refundable = false;

    #[Validate('required|numeric|min:0')]
    public float $price = 0;

    #[Validate('required|string|size:3')]
    public string $currency = 'USD';

    public bool $is_published = true;

    public bool $is_featured = false;

    public function mount(?string $id = null): void
    {
        if ($id) {
            $this->authorize('trains.update');
            $this->offer = TrainOffer::query()->findOrFail($id);
            $this->fill($this->offer->only([
                'operator', 'operator_code', 'train_number', 'origin', 'destination',
                'depart_time', 'arrive_time', 'duration_minutes', 'changes', 'class',
                'fare_type', 'refundable', 'price', 'currency', 'is_published', 'is_featured',
            ]));
        } else {
            $this->authorize('trains.create');
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'operator' => $this->operator,
            'operator_code' => strtoupper($this->operator_code),
            'train_number' => strtoupper($this->train_number),
            'origin' => strtoupper($this->origin),
            'destination' => strtoupper($this->destination),
            'depart_time' => $this->depart_time,
            'arrive_time' => $this->arrive_time,
            'duration_minutes' => $this->duration_minutes,
            'changes' => $this->changes,
            'class' => $this->class,
            'fare_type' => $this->fare_type,
            'refundable' => $this->refundable,
            'price' => $this->price,
            'currency' => strtoupper($this->currency),
            'is_published' => $this->is_published,
            'is_featured' => $this->is_featured,
        ];

        if ($this->offer) {
            $this->offer->update($data);
        } else {
            $this->offer = TrainOffer::create($data);
        }

        session()->flash('status', __('Train offer saved.'));
        $this->redirectRoute('admin.trains.index', navigate: true);
    }

    public function render(): View
    {
        return view('trains::admin.form');
    }
}
