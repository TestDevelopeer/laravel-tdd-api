<?php


// use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $res = $this->post('/posts', $data);

        $res->assertStatus(200);

        $this->assertDatabaseCount('posts', 1);

        $post = Post::first();

        $this->assertEquals($data['title'], $post->title);
        $this->assertEquals($data['description'], $post->description);
        $this->assertEquals('images/' . $file->hashName(), $post->image);

        Storage::disk('local')->assertExists($post->image);
    }

    #[Test]
    public function attr_title_is_required_for_storing_post(): void
    {
        $data = [
            'title' => '',
            'description' => 'Test Description',
            'image' => ''
        ];

        $res = $this->post('/posts', $data);

        $res->assertRedirect();
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

        $res->assertRedirect();
        $res->assertInvalid('image');
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

        $res = $this->patch('/posts/' . $post->id, $data);

        $res->assertStatus(200);

        $updatedPost = Post::first();

        $this->assertEquals($data['title'], $updatedPost->title);
        $this->assertEquals($data['description'], $updatedPost->description);
        $this->assertEquals('images/' . $file->hashName(), $updatedPost->image);

        $this->assertEquals($post->id, $updatedPost->id);
    }

    #[Test]
    public function response_for_route_posts_index_is_view_post_index_with_posts(): void
    {
        $this->withoutExceptionHandling();

        $posts = Post::factory(10)->create();

        $res = $this->get('/posts');

        $res->assertViewIs('posts.index');

        $res->assertSeeText('Posts page');

        $titles = $posts->pluck('title')->toArray();

        $res->assertSeeText($titles);
    }

    #[Test]
    public function response_for_route_posts_show_is_view_post_show_with_single_post(): void
    {
        $this->withoutExceptionHandling();
        $post = Post::factory()->create();

        $res = $this->get('/posts/' . $post->id);

        $res->assertViewIs('posts.show');

        $res->assertSeeText('Show post page');
        $res->assertSeeText($post->title);
        $res->assertSeeText($post->description);
    }

    #[Test]
    public function a_post_can_be_deleted_by_auth_user(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $post = Post::factory()->create();

        $res = $this->actingAs($user)->delete('/posts/' . $post->id);

        $res->assertStatus(200);

        $this->assertDatabaseCount('posts', 0);
    }

    #[Test]
    public function a_post_can_be_deleted_by_only_auth_user(): void
    {
        $post = Post::factory()->create();

        $res = $this->delete('/posts/' . $post->id);

        $res->assertRedirect();

        $this->assertDatabaseCount('posts', 1);
    }
}
