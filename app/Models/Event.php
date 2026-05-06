<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'start_date', 'end_date', 'place',
        'price', 'is_free', 'capacity', 'image',
        'category_id', 'created_by', 'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
        'is_free'    => 'boolean',
        'price'      => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function registeredUsers()
    {
        return $this->belongsToMany(User::class, 'registrations')->withTimestamps();
    }

    public function getAvailableSpotsAttribute(): int
    {
        return $this->capacity - $this->registrations()->count();
    }
}
