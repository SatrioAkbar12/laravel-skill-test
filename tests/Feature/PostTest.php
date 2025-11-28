<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->post = Post::factory()->create([
            'user_id' => $this->user->id,
            'is_draft' => false,
            'published_at' => now(),
        ]);
    }

    public function test_index()
    {
        $response = $this->getJson(route('posts.index'));

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_store_requires_authentication()
    {
        $response = $this->postJson(route('posts.store'), [
            'title' => 'Post Title',
            'content' => 'Content of this post',
            'is_draft' => true,
        ]);

        $response->assertStatus(401);
    }

    public function test_store_success()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('posts.store'), [
                'title' => 'Post Title',
                'content' => 'Content of this post',
                'is_draft' => true,
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'Post Title',
                'content' => 'Content of this post',
            ]);
    }

    public function test_show_success()
    {
        $response = $this->getJson(route('posts.show', ['post' => $this->post->id]));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $this->post->id,
            ]);
    }

    public function test_show_is_draft_post()
    {
        $draft = Post::factory()->create([
            'user_id' => $this->user->id,
            'is_draft' => true,
        ]);

        $response = $this->getJson(route('posts.show', ['post' => $draft->id]));

        $response->assertStatus(404);
    }

    public function test_update_only_author_can()
    {
        $dummyUser = User::factory()->create();
        $published_time = now()->format('Y-m-d\TH:i:s.000000\Z');

        $response = $this->actingAs($dummyUser)
            ->putJson(route('posts.update', ['post' => $this->post->id]), [
                'title' => 'Post Title Updated',
                'content' => 'Content is updated',
                'is_draft' => false,
                'published_at' => $published_time,
            ]);

        $response->assertStatus(403);
    }

    public function test_update_success()
    {
        $published_time = now()->format('Y-m-d\TH:i:s.000000\Z');

        $response = $this->actingAs($this->user)
            ->putJson(route('posts.update', ['post' => $this->post->id]), [
                'title' => 'Post Title Updated',
                'content' => 'Content is updated',
                'is_draft' => false,
                'published_at' => $published_time,
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Post Title Updated',
                'content' => 'Content is updated',
                'published_at' => $published_time,
            ]);
    }

    public function test_destroy_only_author_can()
    {
        $dummyUser = User::factory()->create();

        $response = $this->actingAs($dummyUser)
            ->deleteJson(route('posts.destroy', ['post' => $this->post->id]));

        $response->assertStatus(403);
    }

    public function test_destroy_success()
    {
        $response = $this->actingAs($this->user)
            ->deleteJson(route('posts.destroy', ['post' => $this->post->id]));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'status' => 'success',
            ]);

        $this->assertDatabaseMissing('posts', ['id' => $this->post->id]);
    }
}
