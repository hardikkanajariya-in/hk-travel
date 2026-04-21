<?php

namespace App\Modules\Tours\Livewire\Admin;

use App\Modules\Tours\Models\Tour;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Tours')]
#[Layout('components.layouts.admin')]
class TourTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'difficulty')]
    public string $difficulty = '';

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'status', 'difficulty'], true)) {
            $this->resetPage();
        }
    }

    public function togglePublish(string $id): void
    {
        $this->authorize('tours.update');
        $row = Tour::query()->findOrFail($id);
        $row->is_published = ! $row->is_published;
        if ($row->is_published && ! $row->published_at) {
            $row->published_at = now();
        }
        $row->save();
    }

    public function delete(string $id): void
    {
        $this->authorize('tours.delete');
        Tour::query()->whereKey($id)->delete();
    }

    public function render(): View
    {
        $tours = Tour::query()
            ->with('destination')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->status === 'published', fn ($q) => $q->where('is_published', true))
            ->when($this->status === 'draft', fn ($q) => $q->where('is_published', false))
            ->when($this->difficulty, fn ($q) => $q->where('difficulty', $this->difficulty))
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('tours::admin.table', compact('tours'));
    }
}
