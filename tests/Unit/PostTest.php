<?php

namespace Tests\Unit;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_setTitleAndSlug_sets_title_and_generates_slug(): void
    {
        $post = new Post();
        $title = 'Test Post Title';

        $post->setTitleAndSlug($title);

        $this->assertEquals($title, $post->title);
        $this->assertEquals('test-post-title', $post->slug);
    }

    public function test_setTitleAndSlug_with_special_characters(): void
    {
        $post = new Post();
        $title = 'Test Post with Special Characters! @#$%';

        $post->setTitleAndSlug($title);

        $this->assertEquals($title, $post->title);
        $this->assertEquals('test-post-with-special-characters-at', $post->slug);
    }

    public function test_setTitleAndSlug_with_cyrillic_characters(): void
    {
        $post = new Post();
        $title = 'Тестовый заголовок поста';

        $post->setTitleAndSlug($title);

        $this->assertEquals($title, $post->title);
        $this->assertEquals('testovyi-zagolovok-posta', $post->slug);
    }

    public function test_setContent_sets_content(): void
    {
        $post = new Post();
        $content = 'This is the content of the post.';

        $post->setContent($content);

        $this->assertEquals($content, $post->content);
    }

    public function test_publish_sets_published_status_and_date(): void
    {
        $post = new Post();
        $post->is_published = false;
        $post->published_at = null;

        $post->publish();

        $this->assertTrue($post->is_published);
        $this->assertInstanceOf(Carbon::class, $post->published_at);
        $this->assertNotNull($post->published_at);
    }

    public function test_publish_does_not_change_already_published_post(): void
    {
        $post = new Post();
        $post->is_published = true;
        $originalPublishedAt = Carbon::now()->subDays(1);
        $post->published_at = $originalPublishedAt;

        $post->publish();

        $this->assertTrue($post->is_published);
        $this->assertEquals($originalPublishedAt->format('Y-m-d H:i:s'), $post->published_at->format('Y-m-d H:i:s'));
    }

    public function test_publish_does_not_change_post_with_published_at_date(): void
    {
        $post = new Post();
        $post->is_published = false;
        $originalPublishedAt = Carbon::now()->subDays(1);
        $post->published_at = $originalPublishedAt;

        $post->publish();

        $this->assertFalse($post->is_published);
        $this->assertEquals($originalPublishedAt->format('Y-m-d H:i:s'), $post->published_at->format('Y-m-d H:i:s'));
    }
}
