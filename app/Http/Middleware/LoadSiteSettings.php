<?php

namespace App\Http\Middleware;

use App\Support\SiteContent;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoadSiteSettings
{
    public function handle(Request $request, Closure $next): Response
    {
        $original = config('safir');
        $site = new SiteContent;
        app()->instance(SiteContent::class, $site);
        foreach ($original as $key => $value) {
            if (array_key_exists('business_'.$key, $site->all())) {
                config(['safir.'.$key => $key === 'indexable' ? $site->text('business_'.$key) === '1' : $site->text('business_'.$key)]);
            }
        }
        view()->share('site', $site);
        try {
            $response = $next($request);
            if ($request->is('admin', 'admin/*')) {
                $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
                $response->headers->set('Cache-Control', 'no-store, private');
            }

            return $response;
        } finally {
            config(['safir' => $original]);
        }
    }
}
