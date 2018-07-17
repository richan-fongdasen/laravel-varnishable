<?php

namespace RichanFongdasen\Varnishable;

use Illuminate\Database\Eloquent\Model;
use RichanFongdasen\Varnishable\Events\ModelHasUpdated;

class VarnishableObserver
{
    /**
     * Listening to any saved events.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function deleted(Model $model)
    {
        $this->handleModelUpdates($model);
    }

    /**
     * Handle any retrieved and wakeup events on
     * the observed models.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    protected function handleModelInitialization(Model $model)
    {
        if ($updatedAt = $model->getAttribute('updated_at')) {
            \Varnishable::setLastModifiedHeader($updatedAt);
        }
    }

    /**
     * Handle any update events on the observed models.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    protected function handleModelUpdates(Model $model)
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
    public function restored(Model $model)
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
    public function retrieved(Model $model)
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
    public function saved(Model $model)
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
    public function wakeup(Model $model)
    {
        $this->handleModelInitialization($model);
    }
}
