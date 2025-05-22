<?php


// use Illuminate\Foundation\Testing\RefreshDatabase;
namespace Api;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->withHeaders([
            'Accept' => 'application/json',
        ]);
    }

    #[Test]
    public function a_post_can_be_stored(): void
    {
        $this->withoutExceptionHandling();

        $file = File::create('test_image.jpg');

        $data = [
            'title' => 'Test Title',
            'description' => 'Test Description',
            'image' => $file,
        ];

        $res = $this->post('/api/posts', $data);

        $res->assertStatus(201);

        $this->assertDatabaseCount('posts', 1);

        $post = Post::first();

        $this->assertEquals($data['title'], $post->title);
        $this->assertEquals($data['description'], $post->description);
        $this->assertEquals('images/' . $file->hashName(), $post->image);

        Storage::disk('local')->assertExists($post->image);

        $res->assertJson([
            'data' => [
                'id' => $post->id,
                'title' => $post->title,
                'description' => $post->description,
                'image' => $post->image
            ]
        ]);
    }

    #[Test]
    public function attr_title_is_required_for_storing_post(): void
    {
        $data = [
            'title' => '',
            'description' => 'Test Description',
            'image' => ''
        ];

        $res = $this->post('/api/posts', $data);

        $res->assertStatus(422);
        $res->assertInvalid('title');
    }

    #[Test]
    public function attr_image_is_file_for_storing_post(): void
    {
        $data = [
            'title' => 'Test Title',
            'description' => 'Test Description',
            'image' => 'test_image'
        ];

        $res = $this->post('/posts', $data);

        $res->assertStatus(422);
        $res->assertInvalid('image');
        $res->assertJsonValidationErrors([
            'image' => 'The image field must be a file.',
        ]);
    }

    #[Test]
    public function a_post_can_be_updated(): void
    {
        $this->withoutExceptionHandling();

        $post = Post::factory()->create();

        $file = File::create('test_image.jpg');

        $data = [
            'title' => 'Test Title Edited',
            'description' => 'Test Description Edited',
            'image' => $file,
        ];

        $res = $this->patch('/api/posts/' . $post->id, $data);

        $res->assertJson([
            'data' => [
                'id' => $post->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'image' => 'images/' . $file->hashName(),
            ]
        ]);
    }

    #[Test]
    public function response_for_route_posts_index_is_get_all_posts(): void
    {
        $this->withoutExceptionHandling();

        $posts = Post::factory(10)->create();

        $res = $this->get('/api/posts');

        $res->assertStatus(200);

        $json['data'] = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'description' => $post->description,
                'image' => $post->image,
            ];
        })->toArray();

        $res->assertExactJson($json);
    }

    #[Test]
    public function response_for_route_posts_show_is_get_single_post(): void
    {
        $this->withoutExceptionHandling();
        $post = Post::factory()->create();

        $res = $this->get('/api/posts/' . $post->id);

        $res->assertJson([
            'data' => [
                'id' => $post->id,
                'title' => $post->title,
                'description' => $post->description,
                'image' => $post->image
            ]
        ]);
    }

    #[Test]
    public function a_post_can_be_deleted_by_auth_user(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $post = Post::factory()->create();

        $res = $this->actingAs($user)->delete('/api/posts/' . $post->id);

        $res->assertStatus(200);

        $this->assertDatabaseCount('posts', 0);

        $res->assertJson([
            'message' => 'Post deleted successfully',
        ]);
    }

    #[Test]
    public function a_post_can_be_deleted_by_only_auth_user(): void
    {
        $post = Post::factory()->create();

        $res = $this->delete('/api/posts/' . $post->id);

        $res->assertUnauthorized();

        $this->assertDatabaseCount('posts', 1);
    }
}
