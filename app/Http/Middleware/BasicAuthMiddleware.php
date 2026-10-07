<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\UserApi;
use Illuminate\Support\Facades\Hash;

class BasicAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the Authorization header is set and contains Basic Auth
        if ($request->hasHeader('Authorization')) {
            $authorizationHeader = $request->header('Authorization');
            if (strpos($authorizationHeader, 'Basic ') === 0) {

                $base64Credentials = substr($authorizationHeader, 6);
                $credentials = base64_decode($base64Credentials);
                
                list($username, $password) = explode(':', $credentials, 2);
                
                $CekUser = UserApi::where('username', $username)->first();
                if ($CekUser && Hash::check($password, $CekUser->password)) {
                    if(!$CekUser->status){
                        return response()->json(['message' => 'Akun Anda Telah Di Non Aktifkan, Hubungi Admin Untuk Aktivasi Kembali'], 401);
                    }
                    $request->attributes->set('user_id', $CekUser->id);
                    $request->attributes->set('id_perush',$CekUser->id_perush);
                    return $next($request);
                } else {
                    return response()->json(['message' => 'Invalid username or password'], 401);
                }
            }
        }

        return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED)->header('WWW-Authenticate', 'Basic realm="My Realm"');
    }
}
