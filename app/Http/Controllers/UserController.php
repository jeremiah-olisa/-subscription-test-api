<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\CustomQueryBuilder;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private User $model;
    private string|null|array $query;

    public function __construct()
    {
        $this->model = new User();
        $this->query = request()->query();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        //
        $websites = CustomQueryBuilder::forModel($this->model::class, $this->model)
            ->simplePaginate($this->query['per_page'] ?? 15);


        return $this->response('Users retrived successfully', 200, $websites, [], true);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $this->exists($this->model, 'id', $id, 'User was not found');

        $website = CustomQueryBuilder::forModel($this->model::class, $this->model)
            ->where('id', $id)
            ->with($this->query['include_q'] ?? [])
            ->select($this->query['select_q'] ?? '*')
            ->first();


        return $this->response('Users retrived successfully', 200, $website);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->exists($this->model, 'id', $id, 'User was not found');
        $result = $this->model->firstWhere('id', $id)->delete();

        if ($result) return $this->response('Users deleted successfully', 200, $result);

        return $this->response('Users was not deleted successfully', 400, $result);
    }
}
