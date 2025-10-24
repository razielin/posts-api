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

    public function test_getSlug_returns_slug(): void
    {
        $post = new Post();
        $post->slug = 'test-slug';

        $result = $post->getSlug();

        $this->assertEquals('test-slug', $result);
    }

    public function test_generateAutoincrementedSlug_with_numeric_suffix(): void
    {
        $post = new Post();
        $previousSlug = 'test-post-5';

        $post->generateAutoincrementedSlug($previousSlug);

        $this->assertEquals('test-post-6', $post->slug);
    }

    public function test_generateAutoincrementedSlug_without_numeric_suffix(): void
    {
        $post = new Post();
        $previousSlug = 'test-post';

        $post->generateAutoincrementedSlug($previousSlug);

        $this->assertEquals('test-post-1', $post->slug);
    }

    public function test_generateAutoincrementedSlug_with_multiple_words(): void
    {
        $post = new Post();
        $previousSlug = 'my-awesome-blog-post-3';

        $post->generateAutoincrementedSlug($previousSlug);

        $this->assertEquals('my-awesome-blog-post-4', $post->slug);
    }

    public function test_generateAutoincrementedSlug_with_single_word(): void
    {
        $post = new Post();
        $previousSlug = 'hello';

        $post->generateAutoincrementedSlug($previousSlug);

        $this->assertEquals('hello-1', $post->slug);
    }

    public function test_unpublish_sets_unpublished_status_and_clears_date(): void
    {
        $post = new Post();
        $post->is_published = true;
        $post->published_at = Carbon::now();

        $post->unpublish();

        $this->assertFalse($post->is_published);
        $this->assertNull($post->published_at);
    }

    public function test_unpublish_on_already_unpublished_post(): void
    {
        $post = new Post();
        $post->is_published = false;
        $post->published_at = null;

        $post->unpublish();

        $this->assertFalse($post->is_published);
        $this->assertNull($post->published_at);
    }
}
