<?php

namespace Core\Traits;

use Core\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * This trait is used to keep track of the user who created and updated the model.
 * 
 * @package Core\Traits
 * @author Saiful Alam<hello@msar.me>
 */
trait HasUserFootprint
{
    protected static $defaultUser = 'system';

    /**
     * Boot the HasUserFootprint trait for a model.
     *
     * @return void
     */
    public static function bootHasUserFootprint()
    {
        static::creating(function (Model $model) {
            $model->created_by = Auth::user()?->email ?? self::$defaultUser;
        });

        static::saving(function (Model $model) {
            $model->updated_by = Auth::user()?->email ?? self::$defaultUser;
        });

        static::updating(function (Model $model) {
            $model->updated_by = Auth::user()?->email ?? self::$defaultUser;
        });
    }

    /**
     * Get the user that created the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'email');
    }

    /**
     * Get the user that updated the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'email');
    }
}
