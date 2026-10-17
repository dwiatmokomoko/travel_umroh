<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Support\SiteContent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PackageController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
            'days' => ['nullable', 'integer', Rule::in(Package::upcoming()->distinct()->pluck('days')->all())],
            'sort' => ['nullable', 'in:date,price'],
        ], ['month.date_format' => 'Pilih bulan keberangkatan yang valid.', 'days.in' => 'Pilih durasi 9 atau 12 hari.', 'sort.in' => 'Pilihan urutan tidak valid.']);
        $query = Package::upcoming();
        if (! empty($filters['month'])) {
            $start = Carbon::createFromFormat('!Y-m', $filters['month']);
            $query->whereDate('departure_date', '>=', $start->toDateString())
                ->whereDate('departure_date', '<=', $start->copy()->endOfMonth()->toDateString());
        }
        if (! empty($filters['days'])) {
            $query->where('days', $filters['days']);
        }

        $planningStart = now('Asia/Jakarta')->startOfMonth();
        $monthlyCounts = Package::upcoming()->where('departure_date', '<', $planningStart->copy()->addMonths(60))
            ->get(['departure_date'])->countBy(fn ($package) => $package->departure_date->format('Y-m'));
        $planningMonths = collect(range(0, 59))->map(function (int $offset) use ($planningStart, $monthlyCounts): array {
            $date = $planningStart->copy()->addMonths($offset);

            return ['key' => $date->format('Y-m'), 'label' => $date->translatedFormat('F Y'), 'year' => $date->year, 'count' => $monthlyCounts->get($date->format('Y-m'), 0)];
        });

        return view('packages.index', [
            'planningMonths' => $planningMonths,
            'packages' => $query->orderBy(($filters['sort'] ?? 'date') === 'price' ? 'price' : 'departure_date')->orderBy('id')->get(),
            'months' => Package::upcoming()->orderBy('departure_date')->get(['departure_date'])->unique(fn ($package) => $package->departure_date->format('Y-m')),
            'durations' => Package::upcoming()->distinct()->orderBy('days')->pluck('days'),
            'title' => app(SiteContent::class)->text('packages_title', config('seo.packages_title')),
            'description' => app(SiteContent::class)->text('packages_description', config('seo.packages_description')),
            'canonical' => route('packages.index'), 'noindex' => $request->query() !== [],
        ]);
    }

    public function show(Package $package): View
    {
        abort_unless($package->is_published, 404);

        return view('packages.show', [
            'package' => $package,
            'title' => $package->seo_title ?: $package->name.' • '.$package->departure_date->translatedFormat('d F Y').' | Safir Jogja',
            'description' => $package->seo_description ?: $package->name.' bersama '.$package->airline.'. Harga brosur Rp'.number_format($package->price, 0, ',', '.').'. Konsultasi dengan Safir Jogja untuk ketersediaan, hotel, dan persiapan keberangkatan.',
            'socialImage' => $package->image ? route('media', $package->image) : null,
            'canonical' => route('packages.show', $package->slug),
        ]);
    }
}
