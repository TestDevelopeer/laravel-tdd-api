<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Post\StoreRequest;
use App\Http\Requests\Api\Post\UpdateRequest;
use App\Http\Resources\Post\PostResource;
use App\Models\Post;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return PostResource::collection(Post::all());
    }

    public function store(StoreRequest $request): PostResource
    {
        $data = $request->validated();

        if(isset($data['image'])) {
            $path = Storage::disk('local')->put('/images', $data['image']);
            $data['image'] = $path;
        }

        $post = Post::create($data);

        return PostResource::make($post);
    }

    public function update(UpdateRequest $request, Post $post): PostResource
    {
        $data = $request->validated();

        if(isset($data['image'])) {
            $path = Storage::disk('local')->put('/images', $data['image']);
            $data['image'] = $path;
        }

        $post->update($data);

        return PostResource::make($post);
    }
}
