<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    // Allow mass assignment for these attributes
    protected $fillable = [
        'name',
        'email',
        'phone',
        'user_id',
        'category_id',
    ];

    /**
     * Define the relationship between Contact and User.
     * A contact belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
