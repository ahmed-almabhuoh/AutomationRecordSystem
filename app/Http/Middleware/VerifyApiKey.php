<?php

namespace App\Http\Middleware;

use App\Models\APIKEY;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VerifyApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('X-API-Key');
        $secret = $request->header('X-API-Secret');
        $uuid = $request->header('X-API-Uuid');

        $api = APIKEY::where([
            ['key', '=', $key],
            ['id', '=', $uuid],
        ])->first();

        if (is_null($api)) {
            return response()->json([
                'message' => 'Invalid API key!',
            ], 401);
        } else {
            if (!Hash::check($secret, $api->secret)) {
                return response()->json([
                    'message' => 'Access denied!',
                ], 403);
            }else {
                if ($api->status != 'active' || $api->deleted_at) {
                    return response()->json([
                        'message' => 'Access is specified!',
                    ], 401);
                }
            }
        }

        return $next($request);
    }
}
