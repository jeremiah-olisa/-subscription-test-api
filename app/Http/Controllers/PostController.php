<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Repositories\CustomQueryBuilder;
use Illuminate\Http\Request;

class PostController extends Controller
{
    private Post $model;
    private string|null|array $query;

    public function __construct()
    {
        $this->model = new Post();
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
        $posts = CustomQueryBuilder::forModel($this->model::class, $this->model)
            ->simplePaginate($this->query['per_page'] ?? 15);


        return $this->response('Posts retrived successfully', 200, $posts, [], true);
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
            'title' => 'string|required|min:3|max:50',
            'description' => 'string|required|min:20|max:200',
            'content' => 'string|required|min:20',
            'website_id' => 'numeric|required|exists:websites,id',
        ]);

        $fields = $request->only('website_id', 'title', 'content', 'description');

        $post = $this->model->create($fields);

        return $this->response('Post created successfully', 201, $post);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $this->exists($this->model, 'id', $id, 'Post was not found');

        $website = CustomQueryBuilder::forModel($this->model::class, $this->model)
            ->where('id', $id)
            ->with($this->query['include_q'] ?? [])
            ->select($this->query['select_q'] ?? '*')
            ->first();


        return $this->response('Posts retrived successfully', 200, $website);
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
        $this->exists($this->model, 'id', $id, 'Post was not found');

        $request->validate([
            'website_id' => 'numeric|exists:websites,id',
            'user_id' => 'numeric|exists:users,id'
        ]);

        $fields = $request->only('website_id', 'user_id');

        $subscriber = $this->model->firstWhere('id', $id);
        $subscriber->website_id = $fields['website_id'] ?? $subscriber->website_id;
        $subscriber->user_id = $fields['user_id'] ?? $subscriber->user_id;

        $subscriber->save();

        return $this->response('Posts updated successfully', 200, $subscriber);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->exists($this->model, 'id', $id, 'Post was not found');
        $result = $this->model->firstWhere('id', $id)->delete();

        if ($result) return $this->response('Posts deleted successfully', 200, $result);

        return $this->response('Posts was not deleted successfully', 400, $result);
    }
}
