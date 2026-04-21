<?php

namespace App\Modules\Buses\Livewire\Admin;

use App\Modules\Buses\Models\BusRoute;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Bus routes')]
#[Layout('components.layouts.admin')]
class BusRouteTable extends Component
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
        $this->authorize('buses.update');
        $row = BusRoute::query()->findOrFail($id);
        $row->is_published = ! $row->is_published;
        $row->save();
    }

    public function delete(string $id): void
    {
        $this->authorize('buses.delete');
        BusRoute::query()->whereKey($id)->delete();
    }

    public function render(): View
    {
        $routes = BusRoute::query()
            ->when($this->search, fn ($q) => $q->where(fn ($qq) => $qq->where('title', 'like', "%{$this->search}%")
                ->orWhere('origin', 'like', "%{$this->search}%")->orWhere('destination', 'like', "%{$this->search}%")))
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('buses::admin.table', compact('routes'));
    }
}
