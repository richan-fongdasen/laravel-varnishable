<?php

namespace RichanFongdasen\Varnishable\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class ModelHasUpdated
{
    use SerializesModels;

    /**
     * Eloquent model object.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Event constructor.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Model accessor.
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function model()
    {
        return $this->model;
    }
}
