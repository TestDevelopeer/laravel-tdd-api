<?php


// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
    public function a_post_can_be_updated()
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
}
