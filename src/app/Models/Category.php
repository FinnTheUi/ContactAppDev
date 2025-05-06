<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'user_id',
    ];

    /**
     * Get the user that owns the category.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all contacts under this category.
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * Scope a query to only include categories for a given user.
     *
     * @param  Builder  $query
     * @param  int  $userId
     * @return Builder
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the category's type with the first letter capitalized.
     *
     * @param  string  $value
     * @return string
     */
    public function getTypeAttribute($value): string
    {
        return ucfirst($value);
    }
}
