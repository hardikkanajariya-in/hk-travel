<?php

namespace App\Modules\Comments;

use App\Core\Modules\Module;
use App\Modules\Comments\Livewire\Admin\CommentModerationTable;
use App\Modules\Comments\Livewire\Public\CommentSection;

/**
 * Comments module manifest.
 *
 * Threaded, polymorphic comments with Cloudflare Turnstile gating and
 * an admin moderation queue. Host models (Blog Post, Page) opt in by
 * applying App\Modules\Comments\Concerns\HasComments and exposing an
 * `allow_comments` boolean attribute that the form respects.
 *
 * Registered in config/hk-modules.php under key `comments`.
 */
class CommentModule extends Module
{
    public function key(): string
    {
        return 'comments';
    }

    public function name(): string
    {
        return 'Comments';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    public function permissions(): array
    {
        return [
            'comments.view',
            'comments.update',
            'comments.delete',
            'comments.moderate',
        ];
    }

    public function adminMenu(): array
    {
        return [[
            'label' => 'Comments',
            'route' => 'admin.comments.index',
            'icon' => 'message-square',
            'permission' => 'comments.moderate',
            'group' => 'Engagement',
        ]];
    }

    public function livewireComponents(): array
    {
        return [
            'comments-public.comment-section' => CommentSection::class,
            'comments-admin.comment-moderation-table' => CommentModerationTable::class,
        ];
    }
}
