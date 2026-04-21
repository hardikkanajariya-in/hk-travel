<?php

namespace App\Modules\Visa\Models;

use App\Concerns\HasAuditLog;
use App\Core\Concerns\ProvidesSeoMeta;
use App\Core\Contracts\HasSeoMeta;
use App\Modules\Visa\Database\Factories\VisaServiceFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class VisaService extends Model implements HasSeoMeta
{
    use HasAuditLog, HasFactory, HasTranslations, HasUlids, ProvidesSeoMeta, SoftDeletes;

    protected $table = 'visa_services';

    protected $guarded = ['id'];

    public $translatable = ['title', 'description', 'eligibility', 'notes'];

    protected function casts(): array
    {
        return [
            'fee' => 'decimal:2',
            'service_fee' => 'decimal:2',
            'processing_days_min' => 'integer',
            'processing_days_max' => 'integer',
            'allowed_stay_days' => 'integer',
            'validity_days' => 'integer',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'requirements' => 'array',
            'documents' => 'array',
            'seo' => 'array',
        ];
    }

    protected static function newFactory(): VisaServiceFactory
    {
        return VisaServiceFactory::new();
    }

    protected function buildSeoSchema(): ?array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'GovernmentService',
            'name' => $this->title,
            'description' => strip_tags((string) $this->description),
            'serviceType' => 'Visa application',
            'areaServed' => $this->country,
        ];
    }
}
