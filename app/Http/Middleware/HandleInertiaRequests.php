<?php

namespace App\Http\Middleware;

use App\Models\KioskDevice;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Spatie\Permission\Models\Permission;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'week_time' => fn () => $request->user()
                ? $request->user()->getWeekTime()
                : null,
            'auth.user.permissions' => function () use ($request) {
                if ($request->user()) {
                    if ($request->user()->hasRole('Super admin')) {
                        return Permission::whereNotIn('id', [102, 104])->get()->pluck('name');
                    } else {
                        return $request->user()->getAllPermissions()->pluck('name');
                    }
                }
                return [];
            },
            'auth.user.has_pendent_production' => function () use ($request) {
                if ($request->user()) {
                    return $request->user()->getPendentProductions();
                }

                return null;
            },
            'auth.user.experience' => function () use ($request) {
                if ($request->user()) {
                    return $request->user()->calculateExperience();
                }

                return null;
            },
            'auth.user.nextAttendance' => function () use ($request) {
                if ($request->user()) {
                    return $request->user()->getNextAttendance();
                }

                return null;
            },
            'auth.user.pauseStatus' => function () use ($request) {
                if ($request->user()) {
                    return $request->user()->getPauseStatus();
                }

                return null;
            },
            'isKiosk' => function () use ($request) {
                $token = $_COOKIE['kioskToken'] ?? 'noToken';
                $kiosk = KioskDevice::where('token', $token)
                    ->first();

                return !is_null($kiosk);
            },
            'auth.user.notifications' => function () use ($request) {
                if ($request->user()) {
                    return $request->user()->unreadNotifications;
                }

                return null;
            },
        ]);
    }
}
