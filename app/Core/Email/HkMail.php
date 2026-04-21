<?php

namespace App\Core\Email;

use App\Models\EmailTemplate;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

/**
 * Application-wide mail wrapper.
 *
 * Resolves a DB-backed `EmailTemplate` by key, renders subject + body
 * for the given locale (with fallback), and dispatches via the default
 * mailer. Falls back gracefully if the template is missing — emits a
 * minimal default body so the system never silently drops a notice.
 */
class HkMail
{
    public function __construct(
        protected EmailTemplateRegistry $registry,
        protected TemplateRenderer $renderer,
    ) {}

    /**
     * @param  array<string, mixed>  $vars
     */
    public function sendTo(string $to, string $key, array $vars = [], ?string $locale = null): void
    {
        $template = EmailTemplate::with('translations')->where('key', $key)->where('is_active', true)->first();
        $locale = $locale ?? app()->getLocale();

        if (! $template) {
            $registered = $this->registry->get($key);
            $subject = $registered['label'] ?? $key;
            $bodyHtml = '<p>'.e($registered['description'] ?? $key).'</p>';
            $bodyText = $registered['description'] ?? $key;
        } else {
            $tr = $template->translation($locale);
            $subject = $tr ? $this->renderer->renderText($tr->subject, $vars) : $template->label;
            $bodyHtml = $tr ? $this->renderer->render($tr->body_html, $vars) : '';
            $bodyText = $tr && $tr->body_text
                ? $this->renderer->renderText($tr->body_text, $vars)
                : trim(strip_tags($bodyHtml));
        }

        Mail::raw($bodyText, function ($message) use ($to, $subject, $bodyHtml): void {
            $message->to($to)->subject($subject)->html($bodyHtml);
        });
    }

    /** Send a Laravel Mailable, ignoring DB templates (e.g. dev-only). */
    public function sendMailable(string $to, Mailable $mailable): void
    {
        Mail::to($to)->send($mailable);
    }
}
