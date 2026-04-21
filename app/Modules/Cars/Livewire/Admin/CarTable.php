<?php

namespace App\Modules\Cars\Livewire\Admin;

use App\Modules\Cars\Models\CarRental;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Car rentals')]
#[Layout('components.layouts.admin')]
class CarTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'class')]
    public string $class = '';

    public function updating(string $name): void
    {
        if (in_array($name, ['search', 'class'], true)) {
            $this->resetPage();
        }
    }

    public function togglePublish(string $id): void
    {
        $this->authorize('cars.update');
        $row = CarRental::query()->findOrFail($id);
        $row->is_published = ! $row->is_published;
        $row->save();
    }

    public function delete(string $id): void
    {
        $this->authorize('cars.delete');
        CarRental::query()->whereKey($id)->delete();
    }

    public function render(): View
    {
        $cars = CarRental::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->class, fn ($q) => $q->where('vehicle_class', $this->class))
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('cars::admin.table', compact('cars'));
    }
}
