<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait SoftDeletesWithUser
{
    use \Illuminate\Database\Eloquent\SoftDeletes {
        delete as protected traitDelete;
        restore as protected traitRestore;
    }

    public static function bootSoftDeletesWithUser()
    {
        static::deleting(function ($model) {
            if ($model->usesSoftDeletes() && Auth::check()) {
                $model->deleted_by = Auth::id();
                $model->saveQuietly();
            }
        });

        static::restoring(function ($model) {
            if ($model->usesSoftDeletes()) {
                $model->deleted_by = null;
            }
        });
    }

    public function delete()
    {
        if ($this->usesSoftDeletes() && Auth::check()) {
            $this->deleted_by = Auth::id();
            $this->saveQuietly();
        }

        return $this->traitDelete();
    }

    public function restore()
    {
        if ($this->usesSoftDeletes()) {
            $this->deleted_by = null;
        }

        return $this->traitRestore();
    }

    public function deletedByUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by', 'id_user');
    }
}
