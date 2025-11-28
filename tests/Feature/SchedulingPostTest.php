<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchedulingPostTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_scheduled_posts_are_published_when_time_comes()
    {
        $now = now();
        $this->travelTo($now);

        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'is_draft' => true,
            'published_at' => $now->subMinute(),
        ]);

        $this->artisan('posts:publish-scheduled')
            ->assertExitCode(0);

        $post->refresh();

        $this->assertFalse($post->is_draft);
    }

    public function test_draft_without_published_at_will_not_be_published()
    {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'is_draft' => true,
            'published_at' => null,
        ]);

        $this->artisan('posts:publish-scheduled')
            ->assertExitCode(0);

        $post->refresh();

        $this->assertTrue($post->is_draft);
    }

    public function test_scheduled_post_is_not_published_before_time()
    {
        $post = Post::factory()->create([
            'user_id' => $this->user->id,
            'is_draft' => true,
            'published_at' => now()->addHour(),
        ]);

        $this->artisan('posts:publish-scheduled')
            ->assertExitCode(0);

        $post->refresh();

        $this->assertTrue($post->is_draft);
    }
}
