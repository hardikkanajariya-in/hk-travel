<?php

namespace App\Modules\Reviews\Livewire\Public;

use App\Core\Captcha\CaptchaService;
use App\Modules\Reviews\Models\Review;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Public review submission form embedded on host module Show pages.
 *
 * Hosting view passes the reviewable model. The form posts a pending
 * review (moderation defaults to ON via config('hk.reviews.auto_approve'))
 * and emits a success state. CaptchaService is consulted when enabled.
 */
class ReviewForm extends Component
{
    public ?string $reviewableType = null;

    public ?string $reviewableId = null;

    /** @var array<int, string> */
    public array $criteriaKeys = [];

    /** @var array<string, float> */
    public array $criteria = [];

    #[Validate('required|integer|min:1|max:5')]
    public int $rating = 5;

    #[Validate('required|string|max:120')]
    public string $authorName = '';

    #[Validate('required|email|max:180')]
    public string $authorEmail = '';

    #[Validate('nullable|string|max:200')]
    public string $title = '';

    #[Validate('required|string|min:20|max:5000')]
    public string $body = '';

    public ?string $captchaToken = null;

    public string $hp_field = '';

    public bool $sent = false;

    public function mount(Model $reviewable, array $criteriaKeys = []): void
    {
        $this->reviewableType = $reviewable->getMorphClass();
        $this->reviewableId = (string) $reviewable->getKey();
        $this->criteriaKeys = $criteriaKeys ?: (method_exists($reviewable, 'reviewCriteria')
            ? $reviewable->reviewCriteria()
            : (array) config('hk.reviews.default_criteria', ['value', 'service', 'quality']));

        foreach ($this->criteriaKeys as $key) {
            $this->criteria[$key] = 5;
        }

        if ($user = auth()->user()) {
            $this->authorName = (string) $user->name;
            $this->authorEmail = (string) $user->email;
        }
    }

    public function submit(CaptchaService $captcha): void
    {
        if (filled($this->hp_field)) {
            $this->sent = true;

            return;
        }

        $rules = [];
        foreach ($this->criteriaKeys as $key) {
            $rules['criteria.'.$key] = 'nullable|numeric|min:1|max:5';
        }
        $this->validate($rules);
        $this->validate();

        if (method_exists($captcha, 'enabled') && $captcha->enabled()) {
            if (! $captcha->verify((string) $this->captchaToken, request()->ip())) {
                $this->addError('captchaToken', __('errors.captcha_failed'));

                return;
            }
        }

        $rating = $this->rating;
        if (! empty($this->criteria)) {
            $values = array_filter(array_map('floatval', $this->criteria), fn ($v) => $v > 0);
            if ($values !== []) {
                $rating = round(array_sum($values) / count($values), 2);
            }
        }

        $autoApprove = (bool) config('hk.reviews.auto_approve', false);

        Review::create([
            'reviewable_type' => $this->reviewableType,
            'reviewable_id' => $this->reviewableId,
            'user_id' => auth()->id(),
            'author_name' => $this->authorName,
            'author_email' => $this->authorEmail,
            'title' => $this->title ?: null,
            'body' => $this->body,
            'rating' => $rating,
            'criteria' => $this->criteria,
            'status' => $autoApprove ? Review::STATUS_APPROVED : Review::STATUS_PENDING,
            'is_verified' => auth()->check(),
            'locale' => app()->getLocale(),
            'ip' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
            'approved_at' => $autoApprove ? now() : null,
        ]);

        $this->reset(['rating', 'title', 'body', 'captchaToken']);
        $this->rating = 5;
        $this->sent = true;

        $this->dispatch('review-submitted');
    }

    public function render(): View
    {
        return view('reviews::public.form');
    }
}
