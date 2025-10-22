<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Repository\PostRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private PostRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PostRepository();
    }

    public function test_all_returns_collection_of_posts(): void
    {
        // Создаем тестовые посты
        Post::create([
            'title' => 'First Post',
            'slug' => 'first-post',
            'content' => 'Content of first post',
            'is_published' => true,
        ]);

        Post::create([
            'title' => 'Second Post',
            'slug' => 'second-post',
            'content' => 'Content of second post',
            'is_published' => false,
        ]);

        $result = $this->repository->all();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals('First Post', $result->first()->title);
        $this->assertEquals('Second Post', $result->last()->title);
    }

    public function test_all_returns_empty_collection_when_no_posts(): void
    {
        $result = $this->repository->all();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
    }

    public function test_findById_returns_post_when_exists(): void
    {
        $post = Post::create([
            'title' => 'Test Post',
            'slug' => 'test-post',
            'content' => 'Test content',
            'is_published' => true,
        ]);

        $result = $this->repository->findById($post->id);

        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals($post->id, $result->id);
        $this->assertEquals('Test Post', $result->title);
    }

    public function test_findById_throws_exception_when_not_exists(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->findById(999);
    }

    public function test_create_returns_new_post(): void
    {
        $title = 'New Post Title';
        $content = 'New post content';
        $published = true;

        $result = $this->repository->create($title, $content, $published);

        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals($title, $result->title);
        $this->assertEquals('new-post-title', $result->slug);
        $this->assertEquals($content, $result->content);
        $this->assertTrue($result->is_published);
        $this->assertNotNull($result->published_at);
    }

    public function test_create_saves_post_to_database(): void
    {
        $title = 'Database Test Post';
        $content = 'Database test content';
        $published = false;

        $result = $this->repository->create($title, $content, $published);

        $this->assertDatabaseHas('posts', [
            'id' => $result->id,
            'title' => $title,
            'slug' => 'database-test-post',
            'content' => $content,
            'is_published' => false,
        ]);
    }

    public function test_create_with_unpublished_post(): void
    {
        $title = 'Unpublished Post';
        $content = 'This post is not published';
        $published = false;

        $result = $this->repository->create($title, $content, $published);

        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals($title, $result->title);
        $this->assertEquals('unpublished-post', $result->slug);
        $this->assertEquals($content, $result->content);
        $this->assertFalse($result->is_published);
        $this->assertNull($result->published_at);
    }

    public function test_update_returns_updated_post(): void
    {
        $originalPost = Post::create([
            'title' => 'Original Title',
            'slug' => 'original-title',
            'content' => 'Original content',
            'is_published' => false,
        ]);

        $newTitle = 'Updated Title';
        $newContent = 'Updated content';
        $newPublished = true;

        $result = $this->repository->update($originalPost->id, $newTitle, $newContent, $newPublished);

        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals($originalPost->id, $result->id);
        $this->assertEquals($newTitle, $result->title);
        $this->assertEquals('updated-title', $result->slug);
        $this->assertEquals($newContent, $result->content);
        $this->assertTrue($result->is_published);
        $this->assertNotNull($result->published_at);
    }

    public function test_update_saves_changes_to_database(): void
    {
        $originalPost = Post::create([
            'title' => 'Original Title',
            'slug' => 'original-title',
            'content' => 'Original content',
            'is_published' => false,
        ]);

        $newTitle = 'Updated Title';
        $newContent = 'Updated content';
        $newPublished = true;

        $this->repository->update($originalPost->id, $newTitle, $newContent, $newPublished);

        $this->assertDatabaseHas('posts', [
            'id' => $originalPost->id,
            'title' => $newTitle,
            'slug' => 'updated-title',
            'content' => $newContent,
            'is_published' => true,
        ]);
    }

    public function test_update_throws_exception_when_post_not_exists(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->update(999, 'Title', 'Content', true);
    }

    public function test_delete_removes_post_from_database(): void
    {
        $post = Post::create([
            'title' => 'Post to Delete',
            'slug' => 'post-to-delete',
            'content' => 'This post will be deleted',
            'is_published' => true,
        ]);

        $this->repository->delete($post->id);

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }

    public function test_delete_throws_exception_when_post_not_exists(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->delete(999);
    }

    public function test_create_with_special_characters_in_title(): void
    {
        $title = 'Post with Special Characters! @#$%';
        $content = 'Content with special characters';
        $published = false;

        $result = $this->repository->create($title, $content, $published);

        $this->assertEquals($title, $result->title);
        $this->assertEquals('post-with-special-characters-at', $result->slug);
        $this->assertEquals($content, $result->content);
    }
}
