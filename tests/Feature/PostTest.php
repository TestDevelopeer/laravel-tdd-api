<?php


// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_post_can_be_stored(): void
    {
        $this->withoutExceptionHandling();

        Storage::fake('local');

        $file = File::create(['test_image.jpg']);

        $data = [
            'title' => 'Test Title',
            'description' => 'Test Description',
            'image' => $file,
        ];

        $res = $this->post('/posts', $data);

        $res->assertStatus(200);

        $this->assertDatabaseCount('posts', 1);

        $post = Post::first();

        $this->assertEquals($data['title'], $post->title);
        $this->assertEquals($data['description'], $post->description);
        $this->assertEquals($data['image'], $post->image);
    }
}
