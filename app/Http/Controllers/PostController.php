<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\StoreRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function store(StoreRequest $request): void
    {
        $data = $request->validated();
        $path = Storage::disk('local')->put('/images', $data['image']);
        $data['image'] = $path;

        Post::create($data);
    }
}
