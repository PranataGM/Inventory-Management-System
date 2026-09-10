<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use App\Models\AuditLog;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void { }

    public function boot(): void
    {
        Event::listen(function (Login $event) {
            AuditLog::create([
                'user_id' => $event->user->id,
                'action' => 'login',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        Event::listen(function (Logout $event) {
            AuditLog::create([
                'user_id' => $event->user?->id,
                'action' => 'logout',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        Event::listen(function (Failed $event) {
            AuditLog::create([
                'user_id' => $event->user?->id,
                'action' => 'failed_login',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'details' => ['email' => $event->credentials['email'] ?? null],
            ]);
        });
    }
}
