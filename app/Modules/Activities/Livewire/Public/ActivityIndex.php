<?php

namespace App\Modules\Activities\Livewire\Public;

use App\Modules\Activities\Models\Activity;
use App\Modules\Destinations\Models\Destination;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Activities')]
#[Layout('components.layouts.public')]
class ActivityIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'destination')]
    public string $destinationId = '';

    #[Url(as: 'category')]
    public string $category = '';

    #[Url(as: 'sort')]
    public string $sort = 'featured';

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'destinationId', 'category', 'sort'], true)) {
            $this->resetPage();
        }
    }

    public function render(): View
    {
        $activities = Schema::hasTable('activities')
            ? Activity::query()
                ->with('destination')
                ->where('is_published', true)
                ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->when($this->destinationId, fn ($q) => $q->where('destination_id', $this->destinationId))
                ->when($this->category, fn ($q) => $q->where('category', $this->category))
                ->when($this->sort === 'price_asc', fn ($q) => $q->orderBy('price'))
                ->when($this->sort === 'price_desc', fn ($q) => $q->orderByDesc('price'))
                ->when($this->sort === 'duration', fn ($q) => $q->orderBy('duration_hours'))
                ->when($this->sort === 'featured', fn ($q) => $q->orderByDesc('is_featured')->orderByDesc('rating_avg'))
                ->paginate(12)
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 12);

        return view('activities::public.index', [
            'activities' => $activities,
            'destinations' => Schema::hasTable('destinations')
                ? Destination::query()->where('is_published', true)->orderBy('name')->limit(200)->get(['id', 'name'])
                : collect(),
            'categories' => ['adventure', 'culture', 'food', 'nature', 'wellness'],
        ]);
    }
}
