<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Repositories\CustomQueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriberController extends Controller
{
    private Subscriber $model;
    private string|null|array $query;

    public function __construct()
    {
        $this->model = new Subscriber();
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


        return $this->response('Subscribers retrived successfully', 200, $websites, [], true);
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

        $request->validate([
            'website_id' => 'numeric|required|exists:websites,id',
            'user_id' => 'numeric|required|exists:users,id'
        ]);

        $fields = $request->only('website_id', 'user_id');

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
        $this->exists($this->model, 'id', $id, 'Subscriber was not found');

        $website = CustomQueryBuilder::forModel($this->model::class, $this->model)
            ->where('id', $id)
            ->with($this->query['include_q'] ?? [])
            ->select($this->query['select_q'] ?? '*')
            ->first();


        return $this->response('Subscribers retrived successfully', 200, $website);
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
        $this->exists($this->model, 'id', $id, 'Subscriber was not found');

        $request->validate([
            'website_id' => 'numeric|exists:websites,id',
            'user_id' => 'numeric|exists:users,id'
        ]);

        $fields = $request->only('website_id', 'user_id');

        $subscriber = $this->model->firstWhere('id', $id);
        $subscriber->website_id = $fields['website_id'] ?? $subscriber->website_id;
        $subscriber->user_id = $fields['user_id'] ?? $subscriber->user_id;

        $subscriber->save();

        return $this->response('Subscribers updated successfully', 200, $subscriber);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->exists($this->model, 'id', $id, 'Subscriber was not found');
        $result = $this->model->firstWhere('id', $id)->delete();

        if ($result) return $this->response('Subscribers deleted successfully', 200, $result);

        return $this->response('Subscribers was not deleted successfully', 400, $result);
    }
}
