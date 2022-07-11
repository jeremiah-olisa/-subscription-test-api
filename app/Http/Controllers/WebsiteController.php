<?php

namespace App\Http\Controllers;

use App\Models\Website;
use App\Repositories\CustomQueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebsiteController extends Controller
{
    private Website $model;
    private string|null|array $query;

    public function __construct()
    {
        $this->model = new Website();
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


        return $this->response('Websites retrived successfully', 200, $websites, [], true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //
        $request['owner_id'] ??= intval($request['owner_id'] ?? Auth::id() ?? '0');

        $request->validate([
            'name' => 'string|required|min:5|max:25',
            'url' => 'string|required|min:5|max:50|url|unique:websites,url',
            'owner_id' => 'numeric|required|exists:users,id'
        ]);

        $fields = $request->only('name', 'url', 'owner_id');

        $website = $this->model->create($fields);

        return $this->response('Websites created successfully', 201, $website);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $this->exists($this->model, 'id', $id, 'Website was not found');

        $website = CustomQueryBuilder::forModel($this->model::class, $this->model)
            ->where('id', $id)
            ->with($this->query['include_q'] ?? [])
            ->select($this->query['select_q'] ?? '*')
            ->first();


        return $this->response('Websites retrived successfully', 200, $website);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $this->exists($this->model, 'id', $id, 'Website was not found');

        $request->validate([
            'name' => 'string|min:5|max:25',
            'url' => 'string|min:5|max:50|url|unique:websites,url',
            'owner_id' => 'numeric|exists:users,id'
        ]);

        $fields = $request->only('name', 'url', 'owner_id');


        $website = $this->model->firstWhere('id', $id);
        $website->name = $fields['name'] ?? $website->name;
        $website->url = $fields['url'] ?? $website->url;
        $website->owner_id = $fields['owner_id'] ?? $website->owner_id;

        $website->save();

        return $this->response('Websites updated successfully', 200, $website);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->exists($this->model, 'id', $id, 'Website was not found');
        $result = $this->model->firstWhere('id', $id)->delete();

        if ($result) return $this->response('Websites deleted successfully', 200, $result);

        return $this->response('Websites was not deleted successfully', 400, $result);
    }
}
