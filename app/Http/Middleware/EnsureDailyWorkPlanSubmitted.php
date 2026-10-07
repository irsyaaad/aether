<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Sdm\Entities\DailyReport;
use Symfony\Component\HttpFoundation\Response;

class EnsureDailyWorkPlanSubmitted
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        if (!$this->isFeatureEnabled()) {
            return $next($request);
        }

        if (!$this->isTargetRole()) {
            return $next($request);
        }

        // if (!$this->hasAccessToDailyCheckMenu()) {
        //     return $next($request);
        // }

        if ($this->isAllowedPath($request)) {
            return $next($request);
        }

        $alreadySubmitted = DailyReport::query()
            ->whereDate('report_date', date('Y-m-d'))
            ->where('user_id', Auth::id())
            ->exists();

        if ($alreadySubmitted) {
            return $next($request);
        }

        return redirect()
            ->route('daily-report.create')
            ->with('error', 'Silakan isi rencana kerja hari ini terlebih dahulu sebelum mengakses menu lain.');
    }

    private function hasAccessToDailyCheckMenu(): bool
    {
        if (!function_exists('check_access')) {
            return true;
        }

        return (bool) check_access('daily-report.create');
    }

    private function isFeatureEnabled(): bool
    {
        return (bool) config('services.persada.daily_work_plan_lock.enabled', false);
    }

    private function isTargetRole(): bool
    {
        $sessionRoleId = (int) (session('role.id_role') ?? 0);
        $targetRoleIds = config('services.persada.daily_work_plan_lock.role_ids', []);

        if (!is_array($targetRoleIds) || empty($targetRoleIds)) {
            return false;
        }

        $normalized = array_map('intval', $targetRoleIds);
        return in_array($sessionRoleId, $normalized, true);
    }

    private function isAllowedPath(Request $request): bool
    {
        return $request->is('daily-report')
            || $request->is('daily-report/*')
            || $request->is('daily-report-summary')
            || $request->is('daily-report-summary/*')
            || $request->is('ajax/get-armada')
            || $request->is('ajax/get-sopir')
            || $request->is('auth/logout')
            || $request->is('auth/logout/*');
    }
}
