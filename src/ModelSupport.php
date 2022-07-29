<?php

namespace Huztw\ModelSupport;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ModelSupport
{
    /**
     * Transform the model and its relationships.
     *
     * @param  \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model $model
     * @param  Closure|null $callback
     * @param  array|string $relations
     * @param  array|string $ignores
     * @return mixed
     */
    public static function transform($model, Closure $callback = null, $relations = ['*'], $ignores = [])
    {
        $functionName = __FUNCTION__;

        if ($model instanceof Collection) {
            return $model->map(fn($item) => static::{$functionName}($item, $callback, $relations, $ignores));
        }

        return (new Transformer($model))->transform($callback, $relations, $ignores);
    }

    /**
     * Create a new paginator instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @return \Huztw\ModelSupport\Paginator
     */
    public static function paginator(Builder $builder)
    {
        return (new Paginator($builder));
    }
}
