<?php

namespace App\Modules\Hotels\Livewire\Admin;

use App\Modules\Hotels\Models\Hotel;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Hotels')]
#[Layout('components.layouts.admin')]
class HotelTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    #[Url(as: 'stars')]
    public string $stars = '';

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'status', 'stars'], true)) {
            $this->resetPage();
        }
    }

    public function togglePublish(string $id): void
    {
        $this->authorize('hotels.update');
        $row = Hotel::query()->findOrFail($id);
        $row->is_published = ! $row->is_published;
        $row->save();
    }

    public function delete(string $id): void
    {
        $this->authorize('hotels.delete');
        Hotel::query()->whereKey($id)->delete();
    }

    public function render(): View
    {
        $hotels = Hotel::query()
            ->with('destination')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->status === 'published', fn ($q) => $q->where('is_published', true))
            ->when($this->status === 'draft', fn ($q) => $q->where('is_published', false))
            ->when($this->stars, fn ($q) => $q->where('star_rating', (int) $this->stars))
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('hotels::admin.table', compact('hotels'));
    }
}
