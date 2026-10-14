<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'city', 'body', 'rating', 'travel_date', 'image', 'is_published', 'consent_confirmed'])]
class Testimonial extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'consent_confirmed' => 'boolean', 'travel_date' => 'date'];
    }
}
