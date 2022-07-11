<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function user(): User|Authenticatable|null
    {
        return Auth::user();
    }

    public function toJson($data)
    {
        return json_decode(json_encode($data), true);
    }


    function exists($class, string $column, $value, string $err = 'Record was not found', bool $throw_error = true)
    {
        $count = $class::where($column, $value)->count();
        
        if ($throw_error && $count < 1) throw new HttpException(404, $err);
        
        return $count;
    }
    
    public function response(string $message, int $statusCode, $data = null, ?array $meta  = [], ?bool $paginated = false)
    {

        $status = 'fail';
        $meta = $meta ?? [];
        if (substr(strval($statusCode), 0, 1) == '2') $status = 'success';
        if (substr(strval($statusCode), 0, 1) == '4') $status = 'error';

        if ($paginated == true)
            return response()->json([
                'status' => $status,
                'message' => $message,
                ...$this->toJson($data),
                ...$meta,
            ], $statusCode);


        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
            ...$meta
        ], $statusCode);
    }
}
