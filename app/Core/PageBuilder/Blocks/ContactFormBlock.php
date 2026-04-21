<?php

namespace App\Core\PageBuilder\Blocks;

use App\Core\PageBuilder\Block;
use App\Models\ContactForm;

class ContactFormBlock extends Block
{
    public function key(): string
    {
        return 'contact_form';
    }

    public function name(): string
    {
        return 'Contact form';
    }

    public function icon(): string
    {
        return 'envelope';
    }

    public function category(): string
    {
        return 'Forms';
    }

    public function defaultData(): array
    {
        return [
            'slug' => '',
            'heading' => 'Get in touch',
            'intro' => null,
        ];
    }

    public function fields(): array
    {
        return [
            [
                'key' => 'slug',
                'label' => 'Form',
                'type' => 'select',
                'options' => ContactForm::query()->where('is_active', true)->pluck('name', 'slug')->all(),
            ],
            ['key' => 'heading', 'label' => 'Heading', 'type' => 'text'],
            ['key' => 'intro', 'label' => 'Intro', 'type' => 'richtext'],
        ];
    }

    public function view(): string
    {
        return 'page-builder::blocks.contact-form';
    }
}
