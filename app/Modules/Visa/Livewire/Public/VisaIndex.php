<?php

namespace App\Modules\Visa\Livewire\Public;

use App\Modules\Visa\Models\VisaService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Visa services')]
#[Layout('components.layouts.public')]
class VisaIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'country')]
    public string $country = '';

    #[Url(as: 'type')]
    public string $type = '';

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'country', 'type'], true)) {
            $this->resetPage();
        }
    }

    public function render(): View
    {
        if (! Schema::hasTable('visa_services')) {
            return view('visa::public.index', [
                'services' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20),
                'countries' => collect(),
                'types' => collect(),
            ]);
        }

        $services = VisaService::query()
            ->where('is_published', true)
            ->when($this->search, fn ($q) => $q->where(fn ($qq) => $qq->where('title', 'like', "%{$this->search}%")->orWhere('country', 'like', "%{$this->search}%")))
            ->when($this->country, fn ($q) => $q->where('country', $this->country))
            ->when($this->type, fn ($q) => $q->where('visa_type', $this->type))
            ->orderBy('country')
            ->paginate(20);

        $countries = VisaService::query()->where('is_published', true)->distinct()->orderBy('country')->pluck('country');
        $types = VisaService::query()->where('is_published', true)->distinct()->orderBy('visa_type')->pluck('visa_type');

        return view('visa::public.index', compact('services', 'countries', 'types'));
    }
}
