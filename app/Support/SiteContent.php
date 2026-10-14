<?php

namespace App\Support;

use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Support\Collection;

class SiteContent
{
    private ?array $values = null;

    private ?Collection $pages = null;

    public function consultants(): array
    {
        $contacts = [];
        foreach (config('consultants') as $key => $defaults) {
            $name = $defaults['name'];
            $phone = $this->text('consultant_'.$key.'_phone', $defaults['phone']);
            $contacts[] = [
                'name' => $this->text('consultant_'.$key.'_name', $name),
                'phone' => $phone,
                'available' => $this->text('consultant_'.$key.'_enabled', '1') === '1' && preg_match('/^[1-9][0-9]{7,14}$/', $phone),
            ];
        }

        return $contacts;
    }

    public function pages(): Collection
    {
        return $this->pages ??= Page::all(['slug', 'title', 'is_published']);
    }

    public function pageVisible(string $slug): bool
    {
        $page = $this->pages()->firstWhere('slug', $slug);

        return $page ? $page->is_published : true;
    }

    public function navigationVisible(string $route): bool
    {
        $slugs = ['about' => 'tentang-kami', 'local' => 'umrah-jogja', 'contact' => 'kontak', 'guide' => 'panduan-umrah', 'privacy' => 'privasi'];

        return ! isset($slugs[$route]) || $this->pageVisible($slugs[$route]);
    }

    public function all(): array
    {
        return $this->values ??= SiteSetting::pluck('value', 'key')->all();
    }

    public function text(string $key, string $fallback = ''): string
    {
        return $this->all()[$key] ?? config('content.'.$key, $fallback);
    }

    public function image(string $key, string $fallback): string
    {
        $path = $this->text($key);

        return $path ? route('media', basename($path)) : asset($fallback);
    }

    public function resolve(string $text): string
    {
        foreach (['name', 'company', 'contact', 'phone', 'license', 'address'] as $key) {
            $text = str_replace('{'.$key.'}', (string) config('safir.'.$key), $text);
        }

        return $text;
    }
}
