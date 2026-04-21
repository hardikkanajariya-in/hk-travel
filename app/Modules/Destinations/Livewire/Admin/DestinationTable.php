<?php

namespace App\Modules\Destinations\Livewire\Admin;

use App\Modules\Destinations\Models\Destination;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Destinations')]
#[Layout('components.layouts.admin')]
class DestinationTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'type')]
    public string $type = '';

    #[Url(as: 'status')]
    public string $status = '';

    public function updating(string $name, mixed $value): void
    {
        if (in_array($name, ['search', 'type', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function togglePublish(string $id): void
    {
        $this->authorize('destinations.update');
        $row = Destination::query()->findOrFail($id);
        $row->is_published = ! $row->is_published;
        $row->save();
    }

    public function delete(string $id): void
    {
        $this->authorize('destinations.delete');
        Destination::query()->whereKey($id)->delete();
    }

    public function render(): View
    {
        $destinations = Destination::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->when($this->status === 'published', fn ($q) => $q->where('is_published', true))
            ->when($this->status === 'draft', fn ($q) => $q->where('is_published', false))
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('destinations::admin.table', compact('destinations'));
    }
}
