<?php

namespace App\Modules\Visa\Livewire\Admin;

use App\Modules\Visa\Models\VisaService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Visa services')]
#[Layout('components.layouts.admin')]
class VisaServiceTable extends Component
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
        $this->authorize('visa.update');
        $row = VisaService::query()->findOrFail($id);
        $row->is_published = ! $row->is_published;
        $row->save();
    }

    public function delete(string $id): void
    {
        $this->authorize('visa.delete');
        VisaService::query()->whereKey($id)->delete();
    }

    public function render(): View
    {
        $services = VisaService::query()
            ->when($this->search, fn ($q) => $q->where(fn ($qq) => $qq->where('title', 'like', "%{$this->search}%")->orWhere('country', 'like', "%{$this->search}%")))
            ->when($this->status === 'published', fn ($q) => $q->where('is_published', true))
            ->when($this->status === 'draft', fn ($q) => $q->where('is_published', false))
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('visa::admin.table', compact('services'));
    }
}
