@extends('admin.layout', ['heading' => ($record->exists ? 'Edit ' : 'Tambah ').$schema['label']])
@section('actions')<a class="button secondary" href="{{ route('admin.content.index', $section) }}">← Kembali</a>@endsection
@section('content')
<form class="admin-panel editor" method="post" enctype="multipart/form-data" action="{{ $record->exists ? route('admin.content.update', [$section, $record->id]) : route('admin.content.store', $section) }}" data-editor>@csrf @if($record->exists) @method('PUT') @endif
<div class="field-grid">@foreach($schema['fields'] as $key => [$label, $type, $rules])
@php
    $value = old($key, $record->$key);
    if ($value instanceof \Carbon\CarbonInterface) { $value = $value->format($type === 'date' ? 'Y-m-d' : 'Y-m-d\TH:i'); }
@endphp
<div @class(['field', 'wide' => in_array($type, ['textarea', 'checkbox'])])>
@if($type === 'checkbox')<label class="check"><input type="checkbox" name="{{ $key }}" value="1" @checked($value)>{{ $label }}</label>
@else<label for="{{ $key }}">{{ $label }}</label>@if($type === 'textarea')<textarea id="{{ $key }}" name="{{ $key }}" rows="{{ $key === 'body' ? 16 : 4 }}" @if(str_starts_with($rules, 'required|')) required @endif>{{ $value }}</textarea>@else<input id="{{ $key }}" type="{{ $type }}" name="{{ $key }}" value="{{ $value }}" @if(str_starts_with($rules, 'required|')) required @endif @readonly($key === 'slug' && $record->exists)>@endif @endif
@if($key === 'slug')<small>URL tetap setelah disimpan agar tautan yang telah dibagikan tidak rusak. Gunakan huruf kecil dan tanda hubung.</small>@endif
@if(in_array($key, ['body', 'description', 'facilities']))<small>Markdown: ## untuk judul bagian, **teks tebal**, - untuk daftar. HTML tidak dijalankan.</small>@endif
@if($key === 'published_at')<small>Jika waktu di masa depan, berita baru tampil setelah waktunya tiba (Asia/Jakarta).</small>@endif
@error($key)<p class="field-error">{{ $message }}</p>@enderror</div>@endforeach</div>
@if($section !== 'pages')<div class="image-field"><label for="image_upload">Gambar (JPG, PNG, WebP · maksimal 4 MB / 5000 × 5000 piksel)</label><input id="image_upload" name="image_upload" type="file" accept="image/jpeg,image/png,image/webp" data-image-input><img class="upload-preview" data-image-preview @if($record->image) src="{{ route('media', $record->image) }}" @else hidden @endif alt="Pratinjau gambar">@if($record->image)<label class="check"><input type="checkbox" name="remove_image" value="1">Hapus gambar dari konten ini</label>@endif</div>@endif
<div class="editor-actions"><button class="button" type="submit">Simpan perubahan</button>@if($record->exists && $record->is_published)@php($publicUrl = match($section) { 'articles' => route('news.show', $record->slug), 'packages' => route('packages.show', $record->slug), 'pages' => url('/'.$record->slug), default => route('testimonials') })<a class="button secondary" href="{{ $publicUrl }}" target="_blank" rel="noopener">Lihat di situs ↗</a>@endif</div></form>
@if($record->exists && !($section === 'pages' && in_array($record->slug, ['tentang-kami','umrah-jogja','kontak','panduan-umrah','privasi'])))<form class="delete-form" method="post" action="{{ route('admin.content.destroy', [$section, $record->id]) }}" data-confirm="Hapus konten ini secara permanen? Tindakan ini tidak dapat dibatalkan.">@csrf @method('DELETE')<button class="danger" type="submit">Hapus konten</button><span>Gunakan status draf bila hanya ingin menyembunyikan sementara.</span></form>@endif
@endsection
