<?php

namespace App\Modules\Destinations\Livewire\Public;

use App\Modules\Destinations\Models\Destination;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Destinations')]
#[Layout('components.layouts.public')]
class DestinationIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'type')]
    public string $type = '';

    #[Url(as: 'country')]
    public string $country = '';

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'type', 'country'], true)) {
            $this->resetPage();
        }
    }

    public function render(): View
    {
        $destinations = Destination::query()
            ->where('is_published', true)
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->when($this->country, fn ($q) => $q->where('country_code', strtoupper($this->country)))
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->paginate(12);

        return view('destinations::public.index', compact('destinations'));
    }
}
