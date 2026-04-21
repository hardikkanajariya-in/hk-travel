<?php

namespace App\Modules\Cruises\Livewire\Admin;

use App\Modules\Cruises\Models\Cruise;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Edit cruise')]
#[Layout('components.layouts.admin')]
class CruiseForm extends Component
{
    public ?Cruise $cruise = null;

    #[Validate('required|string|max:160')]
    public string $title = '';

    #[Validate('required|string|max:200')]
    public string $slug = '';

    #[Validate('required|string|max:120')]
    public string $cruise_line = '';

    #[Validate('nullable|string|max:120')]
    public ?string $ship_name = null;

    #[Validate('required|string|max:120')]
    public string $departure_port = '';

    #[Validate('required|string|max:120')]
    public string $arrival_port = '';

    #[Validate('nullable|date')]
    public ?string $departure_date = null;

    #[Validate('nullable|date')]
    public ?string $return_date = null;

    #[Validate('required|integer|min:1|max:60')]
    public int $duration_nights = 7;

    #[Validate('nullable|string|max:20000')]
    public ?string $description = null;

    #[Validate('nullable|string|max:1000')]
    public ?string $highlights = null;

    #[Validate('nullable|string|max:255')]
    public ?string $cover_image = null;

    /** @var array<int, array{name: string, price: float, capacity: int}> */
    public array $cabin_types = [];

    /** @var array<int, array{day: int, port: string, activity: string}> */
    public array $itinerary = [];

    /** @var array<int, string> */
    public array $inclusions = [];

    /** @var array<int, string> */
    public array $exclusions = [];

    #[Validate('required|numeric|min:0')]
    public float $price_from = 0;

    #[Validate('required|string|size:3')]
    public string $currency = 'USD';

    public bool $is_published = false;

    public bool $is_featured = false;

    public function mount(?string $id = null): void
    {
        if ($id) {
            $this->authorize('cruises.update');
            $this->cruise = Cruise::query()->findOrFail($id);
            $this->fill($this->cruise->only([
                'title', 'slug', 'cruise_line', 'ship_name', 'departure_port', 'arrival_port',
                'duration_nights', 'description', 'highlights', 'cover_image',
                'price_from', 'currency', 'is_published', 'is_featured',
            ]));
            $this->departure_date = optional($this->cruise->departure_date)->toDateString();
            $this->return_date = optional($this->cruise->return_date)->toDateString();
            $this->cabin_types = (array) ($this->cruise->cabin_types ?? []);
            $this->itinerary = (array) ($this->cruise->itinerary ?? []);
            $this->inclusions = (array) ($this->cruise->inclusions ?? []);
            $this->exclusions = (array) ($this->cruise->exclusions ?? []);
        } else {
            $this->authorize('cruises.create');
        }
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->slug || $this->cruise === null) {
            $this->slug = Str::slug($value);
        }
    }

    public function addCabin(): void
    {
        $this->cabin_types[] = ['name' => '', 'price' => 0, 'capacity' => 2];
    }

    public function removeCabin(int $i): void
    {
        unset($this->cabin_types[$i]);
        $this->cabin_types = array_values($this->cabin_types);
    }

    public function addDay(): void
    {
        $this->itinerary[] = ['day' => count($this->itinerary) + 1, 'port' => '', 'activity' => ''];
    }

    public function removeDay(int $i): void
    {
        unset($this->itinerary[$i]);
        $this->itinerary = array_values($this->itinerary);
    }

    public function addInclusion(): void
    {
        $this->inclusions[] = '';
    }

    public function removeInclusion(int $i): void
    {
        unset($this->inclusions[$i]);
        $this->inclusions = array_values($this->inclusions);
    }

    public function addExclusion(): void
    {
        $this->exclusions[] = '';
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
            'title' => $this->title, 'slug' => $this->slug, 'cruise_line' => $this->cruise_line,
            'ship_name' => $this->ship_name, 'departure_port' => $this->departure_port, 'arrival_port' => $this->arrival_port,
            'departure_date' => $this->departure_date, 'return_date' => $this->return_date, 'duration_nights' => $this->duration_nights,
            'description' => $this->description, 'highlights' => $this->highlights, 'cover_image' => $this->cover_image,
            'cabin_types' => array_values($this->cabin_types), 'itinerary' => array_values($this->itinerary),
            'inclusions' => array_values(array_filter($this->inclusions)), 'exclusions' => array_values(array_filter($this->exclusions)),
            'price_from' => $this->price_from, 'currency' => strtoupper($this->currency),
            'is_published' => $this->is_published, 'is_featured' => $this->is_featured,
        ];

        if ($this->cruise) {
            $this->cruise->update($data);
        } else {
            $this->cruise = Cruise::create($data);
        }

        session()->flash('status', __('Cruise saved.'));
        $this->redirectRoute('admin.cruises.index', navigate: true);
    }

    public function render(): View
    {
        return view('cruises::admin.form');
    }
}
