<?php

namespace App\Modules\Visa\Livewire\Admin;

use App\Modules\Visa\Models\VisaService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Edit visa service')]
#[Layout('components.layouts.admin')]
class VisaServiceForm extends Component
{
    public ?VisaService $service = null;

    #[Validate('required|string|max:120')]
    public string $country = '';

    #[Validate('nullable|string|size:2')]
    public ?string $country_code = null;

    #[Validate('required|string|max:60')]
    public string $visa_type = 'Tourist';

    #[Validate('required|string|max:160')]
    public string $title = '';

    #[Validate('required|string|max:200')]
    public string $slug = '';

    #[Validate('nullable|string|max:20000')]
    public ?string $description = null;

    #[Validate('nullable|string|max:5000')]
    public ?string $eligibility = null;

    #[Validate('nullable|string|max:5000')]
    public ?string $notes = null;

    /** @var array<int, string> */
    public array $requirements = [];

    public string $newRequirement = '';

    /** @var array<int, string> */
    public array $documents = [];

    public string $newDocument = '';

    #[Validate('required|integer|min:0|max:365')]
    public int $processing_days_min = 7;

    #[Validate('required|integer|min:0|max:365')]
    public int $processing_days_max = 21;

    #[Validate('required|integer|min:1|max:3650')]
    public int $allowed_stay_days = 30;

    #[Validate('required|integer|min:1|max:3650')]
    public int $validity_days = 180;

    #[Validate('required|numeric|min:0')]
    public float $fee = 0;

    #[Validate('required|numeric|min:0')]
    public float $service_fee = 0;

    #[Validate('required|string|size:3')]
    public string $currency = 'USD';

    public bool $is_published = false;

    public bool $is_featured = false;

    public function mount(?string $id = null): void
    {
        if ($id) {
            $this->authorize('visa.update');
            $this->service = VisaService::query()->findOrFail($id);
            $this->fill($this->service->only([
                'country', 'country_code', 'visa_type', 'title', 'slug', 'description', 'eligibility', 'notes',
                'processing_days_min', 'processing_days_max', 'allowed_stay_days', 'validity_days',
                'fee', 'service_fee', 'currency', 'is_published', 'is_featured',
            ]));
            $this->requirements = (array) ($this->service->requirements ?? []);
            $this->documents = (array) ($this->service->documents ?? []);
        } else {
            $this->authorize('visa.create');
        }
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->slug || $this->service === null) {
            $this->slug = Str::slug($value);
        }
    }

    public function addRequirement(): void
    {
        if (filled($this->newRequirement)) {
            $this->requirements[] = trim($this->newRequirement);
            $this->newRequirement = '';
        }
    }

    public function removeRequirement(int $i): void
    {
        unset($this->requirements[$i]);
        $this->requirements = array_values($this->requirements);
    }

    public function addDocument(): void
    {
        if (filled($this->newDocument)) {
            $this->documents[] = trim($this->newDocument);
            $this->newDocument = '';
        }
    }

    public function removeDocument(int $i): void
    {
        unset($this->documents[$i]);
        $this->documents = array_values($this->documents);
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'country' => $this->country,
            'country_code' => $this->country_code ? strtoupper($this->country_code) : null,
            'visa_type' => $this->visa_type,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'eligibility' => $this->eligibility,
            'notes' => $this->notes,
            'requirements' => $this->requirements,
            'documents' => $this->documents,
            'processing_days_min' => $this->processing_days_min,
            'processing_days_max' => $this->processing_days_max,
            'allowed_stay_days' => $this->allowed_stay_days,
            'validity_days' => $this->validity_days,
            'fee' => $this->fee,
            'service_fee' => $this->service_fee,
            'currency' => strtoupper($this->currency),
            'is_published' => $this->is_published,
            'is_featured' => $this->is_featured,
        ];

        if ($this->service) {
            $this->service->update($data);
        } else {
            $this->service = VisaService::create($data);
        }

        session()->flash('status', __('Visa service saved.'));
        $this->redirectRoute('admin.visa.index', navigate: true);
    }

    public function render(): View
    {
        return view('visa::admin.form');
    }
}
