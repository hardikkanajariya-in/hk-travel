<?php

namespace App\Modules\Taxi\Livewire\Admin;

use App\Modules\Taxi\Models\TaxiService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Taxi & transfers')]
#[Layout('components.layouts.admin')]
class TaxiTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'type')]
    public string $type = '';

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'type'], true)) {
            $this->resetPage();
        }
    }

    public function togglePublish(string $id): void
    {
        $this->authorize('taxi.update');
        $row = TaxiService::query()->findOrFail($id);
        $row->is_published = ! $row->is_published;
        $row->save();
    }

    public function delete(string $id): void
    {
        $this->authorize('taxi.delete');
        TaxiService::query()->whereKey($id)->delete();
    }

    public function render(): View
    {
        $services = TaxiService::query()
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->type, fn ($q) => $q->where('service_type', $this->type))
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('taxi::admin.table', compact('services'));
    }
}
