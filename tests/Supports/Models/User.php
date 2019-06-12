<?php

namespace RichanFongdasen\Varnishable\Tests\Supports\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use RichanFongdasen\Varnishable\Model\Concerns\Varnishable;

class User extends AbstractModel
{
    use SoftDeletes;
    use Varnishable;

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
}
