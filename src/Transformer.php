<?php

namespace Huztw\ModelSupport;

use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transformer
{
    protected $model;
    protected $belongs_to_relation;

    /**
     * Create a new Transform instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string|null  $belongsToRelation
     * @return void
     */
    public function __construct(Model $model, $belongsToRelation = null)
    {
        $this->model               = $model;
        $this->belongs_to_relation = $belongsToRelation;
    }

    /**
     * Get model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get belongs to relation.
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getbelongsToRelation()
    {
        return $this->belongs_to_relation;
    }

    /**
     * Transform the model and its relationships.
     *
     * @param  \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model $model
     * @param  Closure|null $callback
     * @param  array|string $relations
     * @param  array|string $relations
     * @param  array|string $ignores
     * @return mixed
     */
    public function transform(Closure $callback = null, $relations = ['*'], $ignores = [])
    {
        $functionName = __FUNCTION__;

        foreach ($this->model->getRelations() as $relation => $value) {
            if ($value instanceof Collection) {
                $value = $value->map(function ($item) use ($functionName, $callback, $relations, $relation, $ignores) {
                    return (new static($item, $relation))->{$functionName}($callback, $relations, $ignores);
                });

                $this->model->setRelation($relation, $value);
            } elseif ($value instanceof Model) {
                $this->model->setRelation($relation, (new static($value, $relation))->{$functionName}($callback, $relations, $ignores));
            }
        }

        if ($this->belongs_to_relation === null || (Str::is($relations, $this->belongs_to_relation) && !Str::is($ignores, $this->belongs_to_relation))) {
            return $callback($this->model);
        }

        return $this->model;
    }
}
