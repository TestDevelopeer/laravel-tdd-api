<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function store(StoreRequest $request): void
    {
        $data = $request->validated();

        if(isset($data['image'])) {
            $path = Storage::disk('local')->put('/images', $data['image']);
            $data['image'] = $path;
        }

        Post::create($data);
    }

    public function update(UpdateRequest $request, Post $post)
    {
        $data = $request->validated();

        if(isset($data['image'])) {
            $path = Storage::disk('local')->put('/images', $data['image']);
            $data['image'] = $path;
        }

        $post->update($data);
    }
}
