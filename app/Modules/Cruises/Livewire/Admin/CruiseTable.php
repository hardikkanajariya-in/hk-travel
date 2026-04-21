<?php

namespace App\Modules\Cruises\Livewire\Admin;

use App\Modules\Cruises\Models\Cruise;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Cruises')]
#[Layout('components.layouts.admin')]
class CruiseTable extends Component
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
        $this->authorize('cruises.update');
        $row = Cruise::query()->findOrFail($id);
        $row->is_published = ! $row->is_published;
        $row->save();
    }

    public function delete(string $id): void
    {
        $this->authorize('cruises.delete');
        Cruise::query()->whereKey($id)->delete();
    }

    public function render(): View
    {
        $cruises = Cruise::query()
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('cruises::admin.table', compact('cruises'));
    }
}
