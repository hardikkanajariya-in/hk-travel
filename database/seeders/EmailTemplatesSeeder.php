<?php

namespace Database\Seeders;

use App\Core\Email\EmailTemplateRegistry;
use App\Models\EmailTemplate;
use App\Models\EmailTemplateTranslation;
use Illuminate\Database\Seeder;

class EmailTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $registry = app(EmailTemplateRegistry::class);
        $defaults = $this->defaults();

        foreach ($registry->all() as $key => $meta) {
            $template = EmailTemplate::firstOrCreate(
                ['key' => $key],
                [
                    'label' => $meta['label'],
                    'description' => $meta['description'] ?? null,
                    'variables' => $meta['variables'] ?? [],
                    'is_active' => true,
                ],
            );

            $content = $defaults[$key] ?? ['subject' => $meta['label'], 'body_html' => '<p>{{ '.($meta['variables'][0] ?? 'name').' }}</p>', 'body_text' => null];

            EmailTemplateTranslation::updateOrCreate(
                ['email_template_id' => $template->id, 'locale' => 'en'],
                $content,
            );
        }
    }

    /** @return array<string, array{subject: string, body_html: string, body_text: ?string}> */
    protected function defaults(): array
    {
        $name = config('hk.general.site_name', config('app.name'));

        return [
            'fortify.verify_email' => [
                'subject' => 'Verify your email address',
                'body_html' => '<p>Hi {{ user.name }},</p><p>Please confirm your email by clicking the button below:</p><p><a href="{{ url }}">Verify email</a></p><p>Thanks,<br>'.e($name).'</p>',
                'body_text' => "Hi {{ user.name }},\n\nVerify your email: {{ url }}\n\nThanks,\n".$name,
            ],
            'fortify.reset_password' => [
                'subject' => 'Reset your password',
                'body_html' => '<p>Hi {{ user.name }},</p><p>You are receiving this email because we received a password reset request for your account.</p><p><a href="{{ url }}">Reset password</a></p><p>If you did not request a password reset, no further action is required.</p>',
                'body_text' => "Hi {{ user.name }},\n\nReset your password: {{ url }}",
            ],
            'fortify.welcome' => [
                'subject' => 'Welcome to '.$name,
                'body_html' => '<p>Hi {{ user.name }},</p><p>Welcome aboard! We\'re glad to have you.</p>',
                'body_text' => 'Hi {{ user.name }}, welcome!',
            ],
            'account.password_changed' => [
                'subject' => 'Your password was changed',
                'body_html' => '<p>Hi {{ user.name }},</p><p>This is a confirmation that your password was changed. If you did not make this change please contact us immediately.</p>',
                'body_text' => 'Hi {{ user.name }}, your password was just changed.',
            ],
            'account.profile_deleted' => [
                'subject' => 'Account deletion confirmed',
                'body_html' => '<p>Hi {{ user.name }},</p><p>Your account and personal data have been deleted in accordance with our privacy policy.</p>',
                'body_text' => 'Your account has been deleted.',
            ],
            'contact.received' => [
                'subject' => 'New contact form submission from {{ name }}',
                'body_html' => '<p><strong>From:</strong> {{ name }} &lt;{{ email }}&gt;</p><p>{{ message }}</p>',
                'body_text' => "From: {{ name }} <{{ email }}>\n\n{{ message }}",
            ],
            'booking.confirmed' => [
                'subject' => 'Booking confirmed: {{ booking.code }}',
                'body_html' => '<p>Hi {{ user.name }},</p><p>Your booking <strong>{{ booking.code }}</strong> has been confirmed. Total: {{ booking.total }}.</p>',
                'body_text' => 'Booking {{ booking.code }} confirmed. Total: {{ booking.total }}.',
            ],
            'booking.cancelled' => [
                'subject' => 'Booking cancelled: {{ booking.code }}',
                'body_html' => '<p>Hi {{ user.name }},</p><p>Your booking <strong>{{ booking.code }}</strong> has been cancelled.</p>',
                'body_text' => 'Booking {{ booking.code }} cancelled.',
            ],
        ];
    }
}
