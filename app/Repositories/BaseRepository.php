<?php

namespace App\Repositories;

use Symfony\Component\HttpKernel\Exception\HttpException;

use function GuzzleHttp\Promise\queue;

class BaseRepository
{
    public array|string $select_q;

    public array $include_q;

    public int $per_page;

    function __construct()
    {
        $query = request()->query();
        // TODO: remove all special character except ','
        $this->select_q = explode(',', $query['select'] ?? '*');

        // TODO: remove all special character except [',', '|', ':']   
        $includes_arr = explode('|', $query['include'] ?? null) ?? [];
        
        $this->include_q = array_filter($includes_arr, function ($item) {
            return $item != "";
        });
        
        $this->per_page = $query['per_page'] ?? 15;

        // dd($query, $includes_arr, $this->include_q, $this);
    }

    function exists($class, string $column, $value, string $err = 'Record was not found', bool $throw_error = true)
    {
        $count = $class::where($column, $value)->count();
        
        if ($throw_error && $count < 1) throw new HttpException(404, $err);
        
        return $count;
    }
}
