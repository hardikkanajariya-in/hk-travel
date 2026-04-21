<?php

namespace App\Modules\Cruises\Livewire\Public;

use App\Modules\Cruises\Models\Cruise;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Cruises')]
#[Layout('components.layouts.public')]
class CruiseIndex extends Component
{
    use WithPagination;

    #[Url(as: 'line')]
    public string $line = '';

    #[Url(as: 'nights')]
    public string $nights = '';

    #[Url(as: 'sort')]
    public string $sort = 'departure_asc';

    public function updating(string $name): void
    {
        if (in_array($name, ['line', 'nights', 'sort'], true)) {
            $this->resetPage();
        }
    }

    public function render(): View
    {
        if (! Schema::hasTable('cruises')) {
            return view('cruises::public.index', [
                'cruises' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 12),
                'lines' => collect(),
            ]);
        }

        $query = Cruise::query()->where('is_published', true)
            ->when($this->line, fn ($q) => $q->where('cruise_line', $this->line))
            ->when($this->nights, function ($q): void {
                match ($this->nights) {
                    'short' => $q->where('duration_nights', '<=', 5),
                    'medium' => $q->whereBetween('duration_nights', [6, 9]),
                    'long' => $q->where('duration_nights', '>=', 10),
                    default => null,
                };
            });

        match ($this->sort) {
            'price_asc' => $query->orderBy('price_from'),
            'price_desc' => $query->orderByDesc('price_from'),
            'duration' => $query->orderBy('duration_nights'),
            default => $query->orderBy('departure_date'),
        };

        $cruises = $query->paginate(12);
        $lines = Cruise::query()->where('is_published', true)->distinct()->pluck('cruise_line');

        return view('cruises::public.index', compact('cruises', 'lines'));
    }
}
