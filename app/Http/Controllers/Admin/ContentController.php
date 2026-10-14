<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Package;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', ['counts' => [
            'Paket umrah' => Package::count(), 'Berita terbit' => Article::published()->count(),
            'Berita draf / terjadwal' => Article::count() - Article::published()->count(),
            'Testimoni tampil' => Testimonial::where('is_published', true)->where('consent_confirmed', true)->count(),
        ], 'recentArticles' => Article::latest('updated_at')->limit(5)->get()]);
    }

    private function schema(string $section): array
    {
        abort_unless(array_key_exists($section, config('admin')), 404);

        return config('admin.'.$section);
    }

    public function index(Request $request, string $section): View
    {
        $schema = $this->schema($section);
        $request->validate(['q' => ['nullable', 'string', 'max:150']]);
        $query = $schema['model']::query();
        if ($request->filled('q')) {
            $query->where($schema['title'], 'like', '%'.$request->string('q')->toString().'%');
        }

        return view('admin.index', ['section' => $section, 'schema' => $schema, 'records' => $query->latest('updated_at')->paginate(15)->withQueryString()]);
    }

    public function create(string $section): View
    {
        $schema = $this->schema($section);

        return view('admin.edit', ['section' => $section, 'schema' => $schema, 'record' => new $schema['model']]);
    }

    public function edit(string $section, int $record): View
    {
        $schema = $this->schema($section);

        return view('admin.edit', ['section' => $section, 'schema' => $schema, 'record' => $schema['model']::findOrFail($record)]);
    }

    public function store(Request $request, string $section): RedirectResponse
    {
        return $this->save($request, $section);
    }

    public function update(Request $request, string $section, int $record): RedirectResponse
    {
        return $this->save($request, $section, $record);
    }

    private function save(Request $request, string $section, ?int $id = null): RedirectResponse
    {
        $schema = $this->schema($section);
        $record = $id ? $schema['model']::findOrFail($id) : new $schema['model'];
        $rules = [];
        foreach ($schema['fields'] as $field => [$label, $type, $validation]) {
            $rules[$field] = explode('|', $validation);
            if ($type === 'checkbox') {
                $request->merge([$field => $request->boolean($field) ? 1 : 0]);
            }
        }
        if (isset($rules['slug'])) {
            $rules['slug'][] = Rule::unique($record->getTable(), 'slug')->ignore($record->getKey());
            if ($record->exists) {
                $rules['slug'][] = Rule::in([$record->slug]);
            }
            if ($section === 'pages') {
                $rules['slug'][] = Rule::notIn(['admin', 'berita', 'testimoni', 'paket-umrah', 'sitemap', 'robots', 'media', 'up', 'storage', 'build']);
            }
        }
        $rules['image_upload'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:max_width=5000,max_height=5000'];
        $rules['remove_image'] = ['nullable', 'boolean'];
        $data = $request->validate($rules);
        unset($data['image_upload'], $data['remove_image']);
        if ($section !== 'pages') {
            if ($request->boolean('remove_image')) {
                $data['image'] = null;
            }
            if ($request->hasFile('image_upload')) {
                $data['image'] = self::storeImage($request);
            }
        }
        $record->fill($data)->save();

        return redirect()->route('admin.content.edit', [$section, $record->id])->with('status', 'Perubahan berhasil disimpan.');
    }

    public static function storeImage(Request $request, string $field = 'image_upload'): string
    {
        $image = imagecreatefromstring($request->file($field)->get());
        abort_if($image === false, 422, 'Gambar tidak dapat dibaca.');
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
        ob_start();
        $encoded = imagewebp($image, null, 82);
        $bytes = ob_get_clean();
        abort_unless($encoded && strlen($bytes) > 0, 422, 'Gambar gagal dikonversi.');
        $filename = Str::uuid().'.webp';
        abort_unless(Storage::disk('local')->put('media/'.$filename, $bytes), 500, 'Gambar gagal disimpan.');

        return $filename;
    }

    public function destroy(string $section, int $record): RedirectResponse
    {
        $schema = $this->schema($section);
        $entry = $schema['model']::findOrFail($record);
        abort_if($section === 'pages' && in_array($entry->slug, ['tentang-kami', 'umrah-jogja', 'kontak', 'panduan-umrah', 'privasi']), 422, 'Halaman utama dapat diedit atau disembunyikan, bukan dihapus.');
        $entry->delete();

        return redirect()->route('admin.content.index', $section)->with('status', 'Konten berhasil dihapus.');
    }
}
