<?php

namespace App\Modules\Blog\Livewire\Admin;

use App\Modules\Blog\Models\BlogTag;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Edit tag')]
#[Layout('components.layouts.admin')]
class BlogTagForm extends Component
{
    public ?BlogTag $tag = null;

    #[Validate('required|string|max:120')]
    public string $name = '';

    #[Validate('required|string|max:140')]
    public string $slug = '';

    public function mount(?string $id = null): void
    {
        $this->authorize('blog.taxonomy.manage');

        if ($id) {
            $this->tag = BlogTag::query()->findOrFail($id);
            $this->fill($this->tag->only(['name', 'slug']));
        }
    }

    public function updatedName(string $value): void
    {
        if (! $this->slug || $this->tag === null) {
            $this->slug = Str::slug($value);
        }
    }

    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'max:140', Rule::unique('blog_tags', 'slug')->ignore($this->tag?->id)],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $data = ['name' => $this->name, 'slug' => $this->slug];

        if ($this->tag) {
            $this->tag->update($data);
        } else {
            $this->tag = BlogTag::create($data);
        }

        session()->flash('status', __('Tag saved.'));
        $this->redirectRoute('admin.blog.tags.index', navigate: true);
    }

    public function render(): View
    {
        return view('blog::admin.tags.form');
    }
}
