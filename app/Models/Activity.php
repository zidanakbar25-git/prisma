<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'activity_date',
        'start_time',
        'end_time',
        'location',
        'description',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
        ];
    }

    /**
     * Pengguna yang membuat kegiatan.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Daftar pengguna yang menjadi PIC kegiatan.
     */
    public function pics(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'activity_pics'
        )->withTimestamps();
    }
}