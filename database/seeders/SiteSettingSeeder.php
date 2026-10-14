<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        // Identity and local contact from A5 DAVID GK_3.pdf and the supplied logo;
        // company name and permit number from SK PPIU PT Safir Caturjaya Wisata.pdf.
        $settings = [
            'chatbot_enabled' => '1',
            'chatbot_welcome' => 'Assalamu’alaikum! Tanyakan paket, fasilitas, atau persiapan umrah. Untuk konsultasi pribadi, pilih Wisnu, Ryan, atau Dwi.',
            'business_name' => 'Safir Caturjaya Wisata Jogja',
            'business_company' => 'PT Safir Caturjaya Wisata',
            'business_whatsapp' => '6282136042015',
            'business_phone' => '0821-3604-2015',
            'business_contact' => 'Wisnu',
            'business_license' => '11032200304330006',
            'business_address' => 'Perum Ndalem Triwidadi Kav. 43, Triwidadi, Pajangan, Bantul, Daerah Istimewa Yogyakarta',
            'business_maps' => 'https://maps.app.goo.gl/19ZCKpJWaN1wcEge9',
        ];

        foreach (config('consultants') as $key => $contact) {
            $settings['consultant_'.$key.'_name'] = $contact['name'];
            $settings['consultant_'.$key.'_phone'] = $contact['phone'];
            $settings['consultant_'.$key.'_enabled'] = '1';
        }

        // Keep editorial defaults editable in the dashboard. Indexing remains
        // an environment/deployment choice, not a consequence of importing data.
        foreach (array_merge(config('content'), config('seo'), $settings) as $key => $value) {
            SiteSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
