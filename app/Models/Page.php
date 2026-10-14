<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['slug', 'title', 'intro', 'body', 'seo_title', 'seo_description', 'is_published'])]
class Page extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }
}
