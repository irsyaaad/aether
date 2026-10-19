<?php
namespace App\Http\Middleware;

use App\Models\UserActivity;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya catat request non-AJAX atau request penting (POST/PUT/DELETE)
        // Dan pastikan user sudah terautentikasi (atau bisa juga catat guest)
        if ($request->isMethod('GET') && $request->ajax()) {
            return $response;
        }

        // Jangan catat request ke asset atau file static jika ada
        if ($request->is('assets/*') || $request->is('css/*') || $request->is('js/*')) {
            return $response;
        }

        try {
            $user = Auth::user();

            UserActivity::create([
                'user_id'    => $user ? $user->id_user : null,
                'activity'   => $this->getActivityDescription($request),
                'url'        => $request->fullUrl(),
                'method'     => $request->method(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload'    => $this->getSanitizedPayload($request),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('LogUserActivity error: ' . $e->getMessage());
        }

        return $response;
    }

    private function getActivityDescription(Request $request): string
    {
        $method = $request->method();
        $path   = $request->path();

        if ($method === 'GET') {
            return "Mengakses " . $path;
        }

        return "Melakukan " . $method . " pada " . $path;
    }

    private function getSanitizedPayload(Request $request): ?array
    {
        if ($request->isMethod('GET')) {
            return null;
        }

        $payload = $request->all();

        // Hapus data sensitif
        unset($payload['password']);
        unset($payload['password_confirmation']);
        unset($payload['_token']);

        // Konversi objek file agar aman di-encode ke JSON
        array_walk_recursive($payload, function (&$value) {
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                $value = $value->getClientOriginalName();
            }
        });

        return count($payload) > 0 ? $payload : null;
    }
}
