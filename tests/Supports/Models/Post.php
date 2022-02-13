<?php

namespace RichanFongdasen\Varnishable\Tests\Supports\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use RichanFongdasen\Varnishable\Contracts\VarnishableModel;
use RichanFongdasen\Varnishable\Model\Concerns\Varnishable;

class Post extends AbstractModel implements VarnishableModel
{
    use HasFactory;
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
