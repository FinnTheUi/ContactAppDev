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
        'message',     // Assuming 'message' is part of the contact data
        'user_id',
        'category_id', // Link to the category
    ];

    /**
     * Define the relationship between Contact and User.
     * A contact belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship between Contact and Category.
     * A contact belongs to a category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
