<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class IpInfoController extends Controller
{
    /**
     * Get IP geolocation information
     */
    public function getIpInfo(Request $request, $ip = null)
    {
        try {
            $url = $ip 
                ? "https://ipinfo.io/{$ip}/geo"
                : 'https://ipinfo.io/geo';
            
            // Use cache to reduce API calls (cache for 5 minutes)
            $cacheKey = 'ipinfo_' . ($ip ?: 'current');
            
            $data = Cache::remember($cacheKey, 300, function () use ($url) {
                $response = Http::timeout(10)->get($url);
                
                if ($response->successful()) {
                    return $response->json();
                }
                
                throw new \Exception('Failed to fetch IP information');
            });
            
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch IP information. Please try again later.'
            ], 500);
        }
    }
}



