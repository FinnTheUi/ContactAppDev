<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // Allow mass assignment
    protected $fillable = [
        'name',
        'type',
        'user_id',
    ];

    /**
     * A category belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A category can have many contacts.
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Scope to filter categories by user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the category's type in a human-readable format.
     */
    public function getTypeAttribute($value)
    {
        return ucfirst($value); // Capitalizes the type (e.g., 'business' => 'Business')
    }
}
