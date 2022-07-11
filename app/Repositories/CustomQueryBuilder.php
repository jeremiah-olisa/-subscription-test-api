<?php

namespace App\Repositories;

use App\Interfaces\IQueryBuilderFields;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Model;

class CustomQueryBuilder extends QueryBuilder
{

    /**
        function __construct(IQueryBuilderFields $class): self
        {
            $query_builder = $this::for($class)
            // ->defaultSort($class->default_sort)
            ->defaultSorts($class->default_sorts)
            ->allowedIncludes($class->allowed_includes)
            ->allowedFilters($class->allowed_filters)
            ->allowedSorts($class->allowed_sorts);
            
            return $query_builder;
        }
     */

    public static function forModel($subject, $class, ?Request $request = null): QueryBuilder
    {
        if (is_subclass_of($subject, Model::class)) {
            $subject = $subject::query();
        }

        $query_builder = new static($subject, $request);

        $query = request()->query();


        return $query_builder
            ->allowedFilters($class::AllowedFilters ?? $class::AllQueryFields ?? [])
            ->defaultSorts($class::DefaultSorts ?? $class::AllQueryFields ?? [])
            ->allowedIncludes($class::AllowedIncludes ?? $class::AllQueryFields ?? [])
            ->allowedSorts($class::AllowedSorts ?? $subject::AllQueryFields ?? [])
            ->select(explode(',', $query['select'] ?? '*'));
    }
}
