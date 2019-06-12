<?php

namespace RichanFongdasen\Varnishable\Tests\Supports\Models;

use RichanFongdasen\Varnishable\Model\Concerns\Varnishable;

class Post extends AbstractModel
{
    use Varnishable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'content'
    ];
}
