<?php

namespace App\Models;

use App\Concerns\HasAuditLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Lead extends Model
{
    use HasAuditLog;
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'value' => 'decimal:2',
        'data' => 'array',
        'tags' => 'array',
        'sort_order' => 'int',
        'won_at' => 'datetime',
        'lost_at' => 'datetime',
        'last_contacted_at' => 'datetime',
    ];

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->latest('created_at');
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(LeadReminder::class)->orderBy('due_at');
    }

    public function logActivity(string $type, ?string $subject = null, ?string $body = null, array $meta = []): LeadActivity
    {
        return $this->activities()->create([
            'user_id' => Auth::id(),
            'type' => $type,
            'subject' => $subject,
            'body' => $body,
            'meta' => $meta,
            'happened_at' => now(),
        ]);
    }

    public function moveToStage(string $stage): void
    {
        if ($this->stage === $stage) {
            return;
        }
        $previous = $this->stage;
        $this->stage = $stage;
        $this->save();
        $this->logActivity('status_change', "Moved from {$previous} to {$stage}");
    }
}
