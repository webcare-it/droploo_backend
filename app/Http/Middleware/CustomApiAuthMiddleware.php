<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomApiAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('appKey') && $request->header('appSecret') && $request->header('username')) {

            $appKey = $request->header('appKey');
            $appSecret = $request->header('appSecret');
            $username = $request->header('username');
            
            if ($this->isValidCredentials($appKey, $appSecret, $username)) {
                return $next($request);
            } 
            else {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } 
        else {
            return response()->json(['error' => 'Missing headers'], 400);
        }
    }

    private function isValidCredentials($appKey, $appSecret, $username)
    {
        $validCredentials = DB::table('dropshippers')
            ->where('app_key', $appKey)
            ->where('app_secret', $appSecret)
            ->where('user_name', $username)
            ->exists();

        return $validCredentials;
    }
}
