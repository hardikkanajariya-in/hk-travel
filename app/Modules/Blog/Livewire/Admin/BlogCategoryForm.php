<?php

namespace App\Modules\Blog\Livewire\Admin;

use App\Modules\Blog\Models\BlogCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Edit category')]
#[Layout('components.layouts.admin')]
class BlogCategoryForm extends Component
{
    public ?BlogCategory $category = null;

    public ?string $parent_id = null;

    #[Validate('required|string|max:120')]
    public string $name = '';

    #[Validate('required|string|max:140')]
    public string $slug = '';

    #[Validate('nullable|string|max:1000')]
    public ?string $description = null;

    #[Validate('nullable|string|max:255')]
    public ?string $cover_image = null;

    public int $position = 0;

    public function mount(?string $id = null): void
    {
        $this->authorize('blog.taxonomy.manage');

        if ($id) {
            $this->category = BlogCategory::query()->findOrFail($id);
            $this->fill($this->category->only(['parent_id', 'name', 'slug', 'description', 'cover_image', 'position']));
        }
    }

    public function updatedName(string $value): void
    {
        if (! $this->slug || $this->category === null) {
            $this->slug = Str::slug($value);
        }
    }

    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'max:140', Rule::unique('blog_categories', 'slug')->ignore($this->category?->id)],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'parent_id' => $this->parent_id ?: null,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'cover_image' => $this->cover_image,
            'position' => $this->position,
        ];

        if ($this->category) {
            $this->category->update($data);
        } else {
            $this->category = BlogCategory::create($data);
        }

        session()->flash('status', __('Category saved.'));
        $this->redirectRoute('admin.blog.categories.index', navigate: true);
    }

    public function render(): View
    {
        return view('blog::admin.categories.form', [
            'parents' => BlogCategory::query()
                ->when($this->category, fn ($q) => $q->whereKeyNot($this->category->id))
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }
}
