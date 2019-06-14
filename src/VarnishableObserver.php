<?php

namespace RichanFongdasen\Varnishable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use RichanFongdasen\Varnishable\Events\ModelHasUpdated;

class VarnishableObserver
{
    /**
     * Varnishable Service Object.
     *
     * @var \RichanFongdasen\Varnishable\VarnishableService
     */
    protected $varnishable;

    /**
     * Varnishable Observer constructor.
     */
    public function __construct()
    {
        $this->varnishable = app(VarnishableService::class);
    }

    /**
     * Listening to any saved events.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function deleted(Model $model) :void
    {
        $this->handleModelUpdates($model);
    }

    /**
     * Handle any retrieved and wakeup events on
     * the observed models.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @throws Exception
     *
     * @return void
     */
    protected function handleModelInitialization(Model $model) :void
    {
        $updatedAt = $model->getAttribute('updated_at');

        if ($updatedAt !== null) {
            $this->varnishable->setLastModifiedHeader($updatedAt);
        }
    }

    /**
     * Handle any update events on the observed models.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    protected function handleModelUpdates(Model $model) :void
    {
        event(new ModelHasUpdated($model));
    }

    /**
     * Listening to any saved events.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function restored(Model $model) :void
    {
        $this->handleModelUpdates($model);
    }

    /**
     * Listening to any retrieved events.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function retrieved(Model $model) :void
    {
        $this->handleModelInitialization($model);
    }

    /**
     * Listening to any saved events.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function saved(Model $model) :void
    {
        $this->handleModelUpdates($model);
    }

    /**
     * Listening to any wakeup events.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function wakeup(Model $model) :void
    {
        $this->handleModelInitialization($model);
    }
}
