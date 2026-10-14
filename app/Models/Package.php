<?php

namespace App\Models;

use Database\Factories\PackageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['slug', 'name', 'departure_date', 'days', 'includes_thaif', 'price', 'airline', 'madinah_hotel', 'madinah_stars', 'makkah_hotel', 'makkah_stars', 'is_published', 'description', 'facilities', 'image', 'seo_title', 'seo_description'])]
class Package extends Model
{
    /** @use HasFactory<PackageFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['departure_date' => 'date', 'includes_thaif' => 'boolean', 'is_published' => 'boolean', 'price' => 'integer', 'days' => 'integer'];
    }

    #[Scope]
    protected function upcoming(Builder $query): Builder
    {
        return $query->where('is_published', true)->whereDate('departure_date', '>=', today());
    }

    public function whatsappUrl(): string
    {
        return route('contact', ['paket' => $this->name.' keberangkatan '.$this->departure_date->translatedFormat('d F Y')]).'#konsultan';
    }
}
