<h1>Posts page</h1>
<div>
    @foreach($posts as $post)
        <div>
            <h2>{{ $post->title }}</h2>
        </div>
    @endforeach
</div>
