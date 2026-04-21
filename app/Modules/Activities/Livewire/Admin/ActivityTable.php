<?php

namespace App\Modules\Activities\Livewire\Admin;

use App\Modules\Activities\Models\Activity;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Activities')]
#[Layout('components.layouts.admin')]
class ActivityTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $status = '';

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function togglePublish(string $id): void
    {
        $this->authorize('activities.update');
        $row = Activity::query()->findOrFail($id);
        $row->is_published = ! $row->is_published;
        $row->save();
    }

    public function delete(string $id): void
    {
        $this->authorize('activities.delete');
        Activity::query()->whereKey($id)->delete();
    }

    public function render(): View
    {
        $activities = Activity::query()
            ->with('destination')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->status === 'published', fn ($q) => $q->where('is_published', true))
            ->when($this->status === 'draft', fn ($q) => $q->where('is_published', false))
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('activities::admin.table', compact('activities'));
    }
}
