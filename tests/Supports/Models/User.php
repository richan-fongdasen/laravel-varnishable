<?php

namespace RichanFongdasen\Varnishable\Tests\Supports\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Application;
use RichanFongdasen\Varnishable\Model\Concerns\Varnishable;

class User extends Model
{
    use SoftDeletes;
    use Varnishable;
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    /**
     * User exposed observable events.
     *
     * These are extra user-defined events observers may subscribe to.
     *
     * @var array
     */
    protected $observables = ['retrieved', 'wakeup'];

    /**
     * Determine if the current eloquen model have
     * retrieved event.
     *
     * @return boolean
     */
    protected function hasRetrievedEvent()
    {
        $version = explode('.', Application::VERSION);

        return (((int)$version[0] >= 5) && ((int)$version[1] >= 5));
    }

    /**
     * Create a new model instance that is existing.
     *
     * @param  array  $attributes
     * @param  string|null  $connection
     * @return static
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $model = parent::newFromBuilder($attributes, $connection);

        if (!$this->hasRetrievedEvent()) {
            event('eloquent.retrieved: ' . get_class($model), $model);
        }

        return $model;
    }
}
