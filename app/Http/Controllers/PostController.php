<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::all();
        return view('posts.index', compact('posts'));
    }

    public function show(Post $post): View
    {
        return view('posts.show', compact('post'));
    }

    public function store(StoreRequest $request): void
    {
        $data = $request->validated();

        if(isset($data['image'])) {
            $path = Storage::disk('local')->put('/images', $data['image']);
            $data['image'] = $path;
        }

        Post::create($data);
    }

    public function update(UpdateRequest $request, Post $post): void
    {
        $data = $request->validated();

        if(isset($data['image'])) {
            $path = Storage::disk('local')->put('/images', $data['image']);
            $data['image'] = $path;
        }

        $post->update($data);
    }

    public function destroy(Post $post): void
    {
        $post->delete();
    }
}
