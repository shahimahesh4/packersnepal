<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = ['title', 'description', 'assigned_to', 'page_id', 'created_by', 'status', 'priority', 'due_date'];

    protected function casts(): array
    {
        return ['due_date' => 'date'];
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): void
    {
        if (! $user->permits('tasks.manage')) {
            $query->where('assigned_to', $user->id);
        }
    }
}
