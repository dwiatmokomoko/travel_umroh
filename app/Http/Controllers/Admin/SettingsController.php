<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\SiteContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(Request $request, SiteContent $site): View
    {
        $group = $request->query('group', 'business');
        abort_unless(is_string($group) && array_key_exists($group, $this->groups()), 404);

        return view('admin.settings', ['group' => $group, 'groups' => $this->groups(), 'fields' => $this->fields($group), 'values' => $site->all()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $group = $request->query('group', 'business');
        abort_unless(is_string($group) && array_key_exists($group, $this->groups()), 404);
        $rules = [];
        foreach ($this->fields($group) as $key => $field) {
            $rules[$key] = $field['rules'] ?? ['required', 'string', 'max:10000'];
        }
        $data = $request->validate($rules);
        foreach (['logo_image', 'hero_image'] as $key) {
            if ($request->hasFile($key) && $group === 'business') {
                $data[$key] = ContentController::storeImage($request, $key);
            } else {
                unset($data[$key]);
            }
        }
        if ($group === 'business') {
            $data['business_indexable'] = $request->boolean('business_indexable') ? '1' : '0';
        }
        if ($group === 'consultants') {
            foreach (['wisnu', 'ryan', 'dwi'] as $key) {
                $data['consultant_'.$key.'_enabled'] = $request->boolean('consultant_'.$key.'_enabled') ? '1' : '0';
                $data['consultant_'.$key.'_phone'] = $data['consultant_'.$key.'_phone'] ?? '';
            }
        }
        if ($group === 'chatbot') {
            $data['chatbot_enabled'] = $request->boolean('chatbot_enabled') ? '1' : '0';
        }
        DB::transaction(function () use ($data) {
            foreach ($data as $key => $value) {
                SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        });

        return back()->with('status', 'Pengaturan disimpan. Perubahan langsung digunakan situs.');
    }

    private function groups(): array
    {
        $groups = ['business' => 'Identitas, kontak & gambar', 'consultants' => 'Kontak Wisnu, Ryan & Dwi', 'chatbot' => 'Asisten AI', 'seo' => 'SEO halaman utama'];
        foreach (array_keys(config('content', [])) as $key) {
            $prefix = explode('_', $key)[0];
            if (in_array($prefix, ['copy7', 'copy8', 'copy9', 'copy10'])) {
                continue;
            }
            $groups[$prefix] = config('content-groups.'.$prefix, ucfirst($prefix));
        }

        return $groups;
    }

    private function fields(string $group): array
    {
        if ($group === 'consultants') {
            $fields = [];
            foreach (['wisnu' => 'Wisnu', 'ryan' => 'Ryan', 'dwi' => 'Dwi'] as $key => $name) {
                $fields['consultant_'.$key.'_name'] = ['label' => 'Nama '.$name, 'default' => $name, 'rules' => ['required', 'string', 'max:60']];
                $fields['consultant_'.$key.'_phone'] = ['label' => 'WhatsApp '.$name.' (628..., kosongkan jika belum tersedia)', 'default' => config('consultants.'.$key.'.phone'), 'rules' => ['nullable', 'regex:/^[1-9][0-9]{7,14}$/'], 'optional' => true];
                $fields['consultant_'.$key.'_enabled'] = ['label' => 'Aktifkan kontak '.$name, 'type' => 'checkbox', 'default' => '1', 'rules' => ['nullable', 'boolean']];
            }

            return $fields;
        }
        if ($group === 'chatbot') {
            return [
                'chatbot_enabled' => ['label' => 'Tampilkan asisten chat', 'type' => 'checkbox', 'default' => '1', 'rules' => ['nullable', 'boolean']],
                'chatbot_welcome' => ['label' => 'Pesan sambutan', 'default' => 'Assalamu’alaikum! Saya asisten virtual Safir. Tanyakan paket, fasilitas, atau persiapan umrah.', 'rules' => ['required', 'string', 'max:500']],
            ];
        }
        if ($group === 'business') {
            $fields = [];
            foreach (['name' => 'Nama usaha di situs', 'company' => 'Nama badan usaha', 'contact' => 'Nama konsultan', 'phone' => 'Nomor telepon tampilan', 'license' => 'Nomor izin PPIU', 'address' => 'Alamat Bantul'] as $key => $label) {
                $fields['business_'.$key] = ['label' => $label, 'default' => config('safir.'.$key), 'rules' => ['required', 'string', 'max:500']];
            }
            $fields['business_whatsapp'] = ['label' => 'WhatsApp internasional, contoh 6282136042015', 'default' => config('safir.whatsapp'), 'rules' => ['required', 'regex:/^[1-9][0-9]{7,14}$/']];
            $fields['business_maps'] = ['label' => 'Tautan Google Maps', 'default' => config('safir.maps'), 'rules' => ['required', 'url:https', 'max:1000']];
            $fields['business_indexable'] = ['label' => 'Izinkan indeks mesin pencari (aktifkan hanya pada domain produksi)', 'type' => 'checkbox', 'default' => config('safir.indexable') ? '1' : '0', 'rules' => ['nullable', 'boolean']];
            foreach (['logo_image' => 'Logo usaha', 'hero_image' => 'Gambar utama / gambar bawaan paket'] as $key => $label) {
                $fields[$key] = ['label' => $label, 'type' => 'file', 'rules' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:max_width=5000,max_height=5000']];
            }

            return $fields;
        }
        if ($group === 'seo') {
            return collect(config('seo'))->map(fn ($value, $key) => ['label' => str_replace('_', ' ', $key), 'default' => $value, 'rules' => ['required', 'string', 'max:'.(str_ends_with($key, 'title') ? '70' : '170')]])->all();
        }

        return collect(config('content'))->filter(fn ($value, $key) => str_starts_with($key, $group.'_'))->map(fn ($value) => ['label' => $value, 'default' => $value])->all();
    }
}
