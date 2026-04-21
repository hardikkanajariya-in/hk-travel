<?php

namespace App\Modules\Trains\Livewire\Admin;

use App\Modules\Trains\Models\TrainOffer;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Train offers')]
#[Layout('components.layouts.admin')]
class TrainOfferTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public function updating(string $name): void
    {
        if ($name === 'search') {
            $this->resetPage();
        }
    }

    public function togglePublish(string $id): void
    {
        $this->authorize('trains.update');
        $row = TrainOffer::query()->findOrFail($id);
        $row->is_published = ! $row->is_published;
        $row->save();
    }

    public function delete(string $id): void
    {
        $this->authorize('trains.delete');
        TrainOffer::query()->whereKey($id)->delete();
    }

    public function render(): View
    {
        $offers = TrainOffer::query()
            ->when($this->search, fn ($q) => $q->where(fn ($qq) => $qq->where('origin', 'like', "%{$this->search}%")
                ->orWhere('destination', 'like', "%{$this->search}%")
                ->orWhere('operator', 'like', "%{$this->search}%")))
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('trains::admin.table', compact('offers'));
    }
}
