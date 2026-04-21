<?php

namespace App\Core\Livewire;

use App\Core\Captcha\CaptchaService;
use App\Core\Events\LeadCaptured;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Public-facing enquiry form re-used by every travel module.
 *
 * Hosting components pass `source` (e.g. "tour", "hotel"), an optional
 * `leadable` model, and `extraFields` to render module-specific
 * questions. On submit the component:
 *
 *   - validates input
 *   - validates captcha when CaptchaService is configured
 *   - dispatches App\Core\Events\LeadCaptured (CRM listens)
 *   - shows a success state — no DB writes here
 */
class EnquiryForm extends Component
{
    public string $source = 'generic';

    public ?string $leadableType = null;

    public ?string $leadableId = null;

    public string $heading = 'Send an enquiry';

    /** @var array<int, array{key:string, label:string, type?:string, required?:bool, options?:array<int,string>}> */
    public array $extraFields = [];

    #[Validate('required|string|max:120')]
    public string $name = '';

    #[Validate('required|email|max:180')]
    public string $email = '';

    #[Validate('nullable|string|max:40')]
    public string $phone = '';

    #[Validate('nullable|string|max:5000')]
    public string $message = '';

    /** @var array<string, mixed> */
    public array $extra = [];

    public ?string $captchaToken = null;

    public string $hp_field = '';

    public bool $sent = false;

    /**
     * @param  array<int, array{key:string, label:string, type?:string, required?:bool, options?:array<int,string>}>  $extraFields
     */
    public function mount(
        string $source = 'generic',
        ?string $leadableType = null,
        ?string $leadableId = null,
        string $heading = 'Send an enquiry',
        array $extraFields = [],
    ): void {
        $this->source = $source;
        $this->leadableType = $leadableType;
        $this->leadableId = $leadableId;
        $this->heading = $heading;
        $this->extraFields = $extraFields;
    }

    public function submit(CaptchaService $captcha): void
    {
        if (filled($this->hp_field)) {
            // Honeypot tripped — silently succeed.
            $this->sent = true;

            return;
        }

        $rules = [];
        foreach ($this->extraFields as $f) {
            $rules['extra.'.$f['key']] = ($f['required'] ?? false)
                ? 'required|string|max:500'
                : 'nullable|string|max:500';
        }
        $this->validate($rules);
        $this->validate();

        if (method_exists($captcha, 'enabled') && $captcha->enabled('enquiry')) {
            if (! $captcha->verify($this->captchaToken)) {
                $this->addError('captchaToken', __('errors.captcha_failed'));

                return;
            }
        }

        $leadable = null;
        if ($this->leadableType && $this->leadableId && class_exists($this->leadableType)) {
            $leadable = $this->leadableType::query()->find($this->leadableId);
        }

        LeadCaptured::dispatch(
            $this->source,
            $this->name,
            $this->email,
            $this->phone ?: null,
            $this->message ?: null,
            ['extra' => $this->extra],
            [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'referer' => request()->header('referer'),
                'locale' => app()->getLocale(),
            ],
            $leadable instanceof Model ? $leadable : null,
        );

        $this->reset(['name', 'email', 'phone', 'message', 'extra', 'captchaToken']);
        $this->sent = true;
    }

    public function render(): View
    {
        return view('hk-core::livewire.enquiry-form');
    }
}
