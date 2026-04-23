<?php

namespace App\Modules\Comments\Livewire\Public;

use App\Core\Captcha\CaptchaService;
use App\Modules\Comments\Models\Comment;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Threaded comments section embedded on Blog Post and Page views.
 *
 * - Hosting model passes itself as `commentable` and the form is hidden
 *   when `allow_comments` is false.
 * - Replies are nested up to Comment::MAX_DEPTH; deeper replies attach
 *   to the deepest legal ancestor.
 * - CaptchaService is consulted when enabled; honeypot accepts silently.
 */
class CommentSection extends Component
{
    public ?string $commentableType = null;

    public ?string $commentableId = null;

    public bool $allowComments = true;

    public ?string $replyToId = null;

    public ?string $replyToName = null;

    #[Validate('required|string|max:120')]
    public string $authorName = '';

    #[Validate('required|email|max:180')]
    public string $authorEmail = '';

    #[Validate('nullable|url|max:255')]
    public string $authorUrl = '';

    #[Validate('required|string|min:3|max:5000')]
    public string $body = '';

    public ?string $captchaToken = null;

    public string $hp_field = '';

    public bool $sent = false;

    public function mount(Model $commentable): void
    {
        $this->commentableType = $commentable->getMorphClass();
        $this->commentableId = (string) $commentable->getKey();
        $this->allowComments = method_exists($commentable, 'commentsAllowed')
            ? $commentable->commentsAllowed()
            : (bool) ($commentable->allow_comments ?? false);

        if ($user = auth()->user()) {
            $this->authorName = (string) $user->name;
            $this->authorEmail = (string) $user->email;
        }
    }

    public function setReplyTo(?string $id): void
    {
        if ($id === null) {
            $this->replyToId = null;
            $this->replyToName = null;

            return;
        }

        $parent = Comment::query()
            ->where('commentable_type', $this->commentableType)
            ->where('commentable_id', $this->commentableId)
            ->where('status', Comment::STATUS_APPROVED)
            ->find($id);

        if (! $parent) {
            return;
        }

        $this->replyToId = $parent->id;
        $this->replyToName = $parent->authorName();
    }

    public function submit(CaptchaService $captcha): void
    {
        abort_unless($this->allowComments, 403);

        if (filled($this->hp_field)) {
            $this->sent = true;

            return;
        }

        $this->validate();

        if (method_exists($captcha, 'enabled') && $captcha->enabled()) {
            if (! $captcha->verify((string) $this->captchaToken, request()->ip())) {
                $this->addError('captchaToken', __('errors.captcha_failed'));

                return;
            }
        }

        $depth = 0;
        $parentId = null;

        if ($this->replyToId) {
            $parent = Comment::query()->find($this->replyToId);
            if ($parent) {
                $parentDepth = (int) $parent->depth;

                if ($parentDepth >= Comment::MAX_DEPTH) {
                    $depth = Comment::MAX_DEPTH;
                    $parentId = $parent->parent_id;
                } else {
                    $depth = $parentDepth + 1;
                    $parentId = $parent->id;
                }
            }
        }

        $autoApprove = (bool) config('hk.comments.auto_approve', false)
            || (auth()->check() && auth()->user()?->hasAnyRole(['super-admin', 'admin', 'manager', 'editor']));

        Comment::create([
            'commentable_type' => $this->commentableType,
            'commentable_id' => $this->commentableId,
            'parent_id' => $parentId,
            'user_id' => auth()->id(),
            'author_name' => $this->authorName,
            'author_email' => $this->authorEmail,
            'author_url' => $this->authorUrl ?: null,
            'body' => $this->body,
            'depth' => $depth,
            'status' => $autoApprove ? Comment::STATUS_APPROVED : Comment::STATUS_PENDING,
            'locale' => app()->getLocale(),
            'ip' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
            'approved_at' => $autoApprove ? now() : null,
        ]);

        $this->reset(['body', 'captchaToken', 'replyToId', 'replyToName']);
        $this->sent = true;
    }

    public function render(): View
    {
        $tree = collect();

        if ($this->commentableType && $this->commentableId) {
            $rows = Comment::query()
                ->where('commentable_type', $this->commentableType)
                ->where('commentable_id', $this->commentableId)
                ->where('status', Comment::STATUS_APPROVED)
                ->orderByDesc('is_pinned')
                ->orderBy('created_at')
                ->get();

            $byParent = $rows->groupBy(fn ($c) => $c->parent_id ?? 'root');
            $tree = $byParent->get('root', collect())->map(function ($node) use ($byParent) {
                return $this->attachChildren($node, $byParent);
            });
        }

        return view('comments::public.section', [
            'tree' => $tree,
            'count' => $tree->isEmpty() ? 0 : $this->countNodes($tree),
        ]);
    }

    protected function attachChildren(Comment $node, $byParent): Comment
    {
        $children = $byParent->get($node->id, collect())
            ->map(fn ($c) => $this->attachChildren($c, $byParent));
        $node->setRelation('approvedReplies', $children);

        return $node;
    }

    protected function countNodes($nodes): int
    {
        $total = 0;
        foreach ($nodes as $n) {
            $total++;
            $total += $this->countNodes($n->approvedReplies ?? collect());
        }

        return $total;
    }
}
