<?php

namespace App\Traits;

use App\Models\AuditLog;

trait HasAuditLog
{
    public static function bootHasAuditLog()
    {
        static::created(function ($model) {
            self::logAction($model, 'create', $model->getAttributes());
        });

        static::updated(function ($model) {
            self::logAction($model, 'update', $model->getChanges());
        });

        static::deleted(function ($model) {
            self::logAction($model, 'delete', $model->getAttributes());
        });
    }

    protected static function logAction($model, $action, $details)
    {
        if (auth()->check()) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'details' => $details,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}
