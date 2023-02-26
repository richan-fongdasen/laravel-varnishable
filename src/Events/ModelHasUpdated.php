<?php

namespace RichanFongdasen\Varnishable\Events;

use Exception;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use RichanFongdasen\Varnishable\Contracts\VarnishableModel;

class ModelHasUpdated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Eloquent model's data
     * after getting serialized into array.
     *
     * @var array
     */
    protected array $data;

    /**
     * Eloquent model object.
     *
     * @var VarnishableModel|null
     */
    protected ?VarnishableModel $model = null;

    /**
     * Eloquent model class name.
     *
     * @var string
     */
    protected string $modelClass;

    /**
     * Event constructor.
     *
     * @param VarnishableModel $model
     */
    public function __construct(VarnishableModel $model)
    {
        $this->data = $model->toArray();
        $this->modelClass = get_class($model);
    }

    /**
     * Create dirty eloquent model object
     * based on the last saved model data.
     *
     * @throws Exception
     *
     * @return VarnishableModel
     */
    protected function createDirtyModel(): VarnishableModel
    {
        $this->model = $this->newModel();

        $this->model->fill($this->data);

        $key = $this->model->getKeyName();
        $this->model->setAttribute($key, data_get($this->data, $key));

        return $this->model;
    }

    /**
     * Get eloquent query builder for
     * the given eloquent model.
     *
     * @param VarnishableModel $model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getQuery(VarnishableModel $model): Builder
    {
        $query = $model->newQuery();

        $traits = class_uses($model);

        if (in_array(SoftDeletes::class, $traits, true)) {
            $query->withTrashed();
        }

        return $query;
    }

    /**
     * Model accessor.
     *
     * @throws Exception
     *
     * @return VarnishableModel
     */
    public function model(): VarnishableModel
    {
        return $this->retrieveModel() ?? $this->createDirtyModel();
    }

    /**
     * Initialize a new VarnishableModel object.
     *
     * @throws Exception
     *
     * @return VarnishableModel
     */
    protected function newModel(): VarnishableModel
    {
        $model = app($this->modelClass);

        if (!($model instanceof VarnishableModel)) {
            throw new Exception('Failed to initialize new VarnishableModel.');
        }

        return $model;
    }

    /**
     * Retrieve fresh eloquent model from
     * run-time cache or the database.
     *
     * @throws Exception
     *
     * @return VarnishableModel|null
     */
    protected function retrieveModel(): ?VarnishableModel
    {
        if ($this->model instanceof VarnishableModel) {
            return $this->model;
        }

        $model = $this->newModel();

        $loadedModel = $this->getQuery($model)->where($model->getKeyName(), data_get($this->data, $model->getKeyName()))->first();

        $this->model = ($loadedModel instanceof VarnishableModel) ? $loadedModel : null;

        return $this->model;
    }
}
