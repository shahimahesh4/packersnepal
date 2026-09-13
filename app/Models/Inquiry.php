<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    protected $fillable = ['reference', 'submission_token', 'inquiry_type', 'service_id', 'name', 'email', 'phone', 'subject', 'address', 'preferred_date', 'details', 'consented_at', 'status', 'staff_notes'];

    protected function casts(): array
    {
        return ['preferred_date' => 'date', 'consented_at' => 'datetime'];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
