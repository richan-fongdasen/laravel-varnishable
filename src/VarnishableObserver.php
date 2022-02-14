<?php

namespace RichanFongdasen\Varnishable;

use Exception;
use RichanFongdasen\Varnishable\Contracts\VarnishableModel;
use RichanFongdasen\Varnishable\Events\ModelHasUpdated;

class VarnishableObserver
{
    /**
     * Varnishable Service Object.
     *
     * @var VarnishableService
     */
    protected VarnishableService $varnishable;

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
     * @param VarnishableModel $model
     *
     * @return void
     */
    public function deleted(VarnishableModel $model): void
    {
        $this->handleModelUpdates($model);
    }

    /**
     * Handle any retrieved and wakeup events on
     * the observed models.
     *
     * @param VarnishableModel $model
     *
     * @throws Exception
     *
     * @return void
     */
    protected function handleModelInitialization(VarnishableModel $model): void
    {
        $updatedAt = $model->getAttribute('updated_at');

        if ($updatedAt !== null) {
            $this->varnishable->setLastModifiedHeader($updatedAt);
        }
    }

    /**
     * Handle any update events on the observed models.
     *
     * @param VarnishableModel $model
     *
     * @return void
     */
    protected function handleModelUpdates(VarnishableModel $model): void
    {
        ModelHasUpdated::dispatch($model);
    }

    /**
     * Listening to any saved events.
     *
     * @param VarnishableModel $model
     *
     * @return void
     */
    public function restored(VarnishableModel $model): void
    {
        $this->handleModelUpdates($model);
    }

    /**
     * Listening to any retrieved events.
     *
     * @param VarnishableModel $model
     *
     * @throws Exception
     *
     * @return void
     */
    public function retrieved(VarnishableModel $model): void
    {
        $this->handleModelInitialization($model);
    }

    /**
     * Listening to any saved events.
     *
     * @param VarnishableModel $model
     *
     * @return void
     */
    public function saved(VarnishableModel $model): void
    {
        $this->handleModelUpdates($model);
    }

    /**
     * Listening to any wakeup events.
     *
     * @param VarnishableModel $model
     *
     * @throws Exception
     *
     * @return void
     */
    public function wakeup(VarnishableModel $model): void
    {
        $this->handleModelInitialization($model);
    }
}
