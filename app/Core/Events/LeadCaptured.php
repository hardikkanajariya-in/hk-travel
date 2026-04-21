<?php

namespace App\Core\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired when any public-facing form captures a lead — booking enquiry,
 * contact form, package request, etc. The CRM module (Track A) listens
 * for this event and persists the lead into its pipeline.
 */
class LeadCaptured
{
    use Dispatchable, SerializesModels;

    /**
     * @param  array<string, mixed>  $payload  raw form payload
     * @param  array<string, mixed>  $meta  ip, user-agent, referer, locale
     */
    public function __construct(
        public string $source,
        public string $name,
        public string $email,
        public ?string $phone = null,
        public ?string $message = null,
        public array $payload = [],
        public array $meta = [],
        public ?Model $leadable = null,
    ) {}
}
