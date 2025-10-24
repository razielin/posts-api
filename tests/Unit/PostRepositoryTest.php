<?php

namespace Tests\Unit;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\EditPostRequest;
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
        $request = $this->createMock(CreatePostRequest::class);
        $request->title = 'New Post Title';
        $request->post_content = 'New post content';
        $request->is_published = true;

        $result = $this->repository->create($request);

        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals('New Post Title', $result->title);
        $this->assertEquals('new-post-title', $result->slug);
        $this->assertEquals('New post content', $result->content);
        $this->assertTrue($result->is_published);
        $this->assertNotNull($result->published_at);
    }

    public function test_create_saves_post_to_database(): void
    {
        $request = $this->createMock(CreatePostRequest::class);
        $request->title = 'Database Test Post';
        $request->post_content = 'Database test content';
        $request->is_published = false;

        $result = $this->repository->create($request);

        $this->assertDatabaseHas('posts', [
            'id' => $result->id,
            'title' => 'Database Test Post',
            'slug' => 'database-test-post',
            'content' => 'Database test content',
            'is_published' => false,
        ]);
    }

    public function test_create_with_unpublished_post(): void
    {
        $request = $this->createMock(CreatePostRequest::class);
        $request->title = 'Unpublished Post';
        $request->post_content = 'This post is not published';
        $request->is_published = false;

        $result = $this->repository->create($request);

        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals('Unpublished Post', $result->title);
        $this->assertEquals('unpublished-post', $result->slug);
        $this->assertEquals('This post is not published', $result->content);
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

        $request = $this->createMock(EditPostRequest::class);
        $request->title = 'Updated Title';
        $request->post_content = 'Updated content';
        $request->is_published = true;

        $result = $this->repository->update($originalPost->id, $request);

        $this->assertInstanceOf(Post::class, $result);
        $this->assertEquals($originalPost->id, $result->id);
        $this->assertEquals('Updated Title', $result->title);
        $this->assertEquals('updated-title', $result->slug);
        $this->assertEquals('Updated content', $result->content);
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

        $request = $this->createMock(EditPostRequest::class);
        $request->title = 'Updated Title';
        $request->post_content = 'Updated content';
        $request->is_published = true;

        $this->repository->update($originalPost->id, $request);

        $this->assertDatabaseHas('posts', [
            'id' => $originalPost->id,
            'title' => 'Updated Title',
            'slug' => 'updated-title',
            'content' => 'Updated content',
            'is_published' => true,
        ]);
    }

    public function test_update_throws_exception_when_post_not_exists(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $request = $this->createMock(EditPostRequest::class);
        $request->title = 'Title';
        $request->post_content = 'Content';
        $request->is_published = true;

        $this->repository->update(999, $request);
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
        $request = $this->createMock(CreatePostRequest::class);
        $request->title = 'Post with Special Characters! @#$%';
        $request->post_content = 'Content with special characters';
        $request->is_published = false;

        $result = $this->repository->create($request);

        $this->assertEquals('Post with Special Characters! @#$%', $result->title);
        $this->assertEquals('post-with-special-characters-at', $result->slug);
        $this->assertEquals('Content with special characters', $result->content);
    }

    public function test_update_only_title(): void
    {
        $originalPost = Post::create([
            'title' => 'Original Title',
            'slug' => 'original-title',
            'content' => 'Original content',
            'is_published1' => false,
        ]);

        $request = $this->createMock(EditPostRequest::class);
        $request->title = 'Updated Title Only';
        $request->post_content = null;
        $request->is_published = null;

        $result = $this->repository->update($originalPost->id, $request);

        $this->assertEquals('Updated Title Only', $result->title);
        $this->assertEquals('updated-title-only', $result->slug);
        $this->assertEquals('Original content', $result->content); // Не изменилось
        $this->assertFalse($result->is_published); // Не изменилось
    }

    public function test_update_only_content(): void
    {
        $originalPost = Post::create([
            'title' => 'Original Title',
            'slug' => 'original-title',
            'content' => 'Original content',
            'is_published' => false,
        ]);

        $request = $this->createMock(EditPostRequest::class);
        $request->title = null;
        $request->post_content = 'Updated content only';
        $request->is_published = null;

        $result = $this->repository->update($originalPost->id, $request);

        $this->assertEquals('Original Title', $result->title); // Не изменилось
        $this->assertEquals('original-title', $result->slug); // Не изменилось
        $this->assertEquals('Updated content only', $result->content);
        $this->assertFalse($result->is_published); // Не изменилось
    }

    public function test_update_only_published_status(): void
    {
        $originalPost = Post::create([
            'title' => 'Original Title',
            'slug' => 'original-title',
            'content' => 'Original content',
            'is_published' => false,
        ]);

        $request = $this->createMock(EditPostRequest::class);
        $request->title = null;
        $request->post_content = null;
        $request->is_published = true;

        $result = $this->repository->update($originalPost->id, $request);

        $this->assertEquals('Original Title', $result->title); // Не изменилось
        $this->assertEquals('original-title', $result->slug); // Не изменилось
        $this->assertEquals('Original content', $result->content); // Не изменилось
        $this->assertTrue($result->is_published);
        $this->assertNotNull($result->published_at);
    }

    public function test_create_with_duplicate_slug_handles_autoincrement(): void
    {
        // Создаем первый пост
        $firstRequest = $this->createMock(CreatePostRequest::class);
        $firstRequest->title = 'Test Post';
        $firstRequest->post_content = 'First post content';
        $firstRequest->is_published = false;

        $firstPost = $this->repository->create($firstRequest);

        // Создаем второй пост с тем же заголовком
        $secondRequest = $this->createMock(CreatePostRequest::class);
        $secondRequest->title = 'Test Post';
        $secondRequest->post_content = 'Second post content';
        $secondRequest->is_published = false;

        $secondPost = $this->repository->create($secondRequest);

        $this->assertEquals('test-post', $firstPost->slug);
        $this->assertEquals('test-post-1', $secondPost->slug);
    }

    public function test_create_with_multiple_duplicate_slugs(): void
    {
        // Создаем несколько постов с одинаковыми заголовками
        $titles = ['Test Post', 'Test Post', 'Test Post'];
        $slugs = [];

        foreach ($titles as $index => $title) {
            $request = $this->createMock(CreatePostRequest::class);
            $request->title = $title;
            $request->post_content = "Content $index";
            $request->is_published = false;

            $post = $this->repository->create($request);
            $slugs[] = $post->slug;
        }

        $this->assertEquals('test-post', $slugs[0]);
        $this->assertEquals('test-post-1', $slugs[1]);
        $this->assertEquals('test-post-2', $slugs[2]);
    }

    public function test_update_with_null_fields_does_not_change_existing(): void
    {
        $originalPost = Post::create([
            'title' => 'Original Title',
            'slug' => 'original-title',
            'content' => 'Original content',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $request = $this->createMock(EditPostRequest::class);
        $request->title = null;
        $request->post_content = null;
        $request->is_published = null;

        $result = $this->repository->update($originalPost->id, $request);

        $this->assertEquals('Original Title', $result->title);
        $this->assertEquals('original-title', $result->slug);
        $this->assertEquals('Original content', $result->content);
        $this->assertTrue($result->is_published);
        $this->assertNotNull($result->published_at);
    }

    public function test_update_with_partial_fields(): void
    {
        $originalPost = Post::create([
            'title' => 'Original Title',
            'slug' => 'original-title',
            'content' => 'Original content',
            'is_published' => false,
        ]);

        $request = $this->createMock(EditPostRequest::class);
        $request->title = 'Updated Title';
        $request->post_content = null; // Не обновляем контент
        $request->is_published = true; // Обновляем статус

        $result = $this->repository->update($originalPost->id, $request);

        $this->assertEquals('Updated Title', $result->title);
        $this->assertEquals('updated-title', $result->slug);
        $this->assertEquals('Original content', $result->content); // Не изменился
        $this->assertTrue($result->is_published);
        $this->assertNotNull($result->published_at);
    }

    public function test_update_with_unpublish(): void
    {
        $originalPost = Post::create([
            'title' => 'Original Title',
            'slug' => 'original-title',
            'content' => 'Original content',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $request = $this->createMock(EditPostRequest::class);
        $request->title = null;
        $request->post_content = null;
        $request->is_published = false;

        $result = $this->repository->update($originalPost->id, $request);

        $this->assertEquals('Original Title', $result->title);
        $this->assertEquals('original-title', $result->slug);
        $this->assertEquals('Original content', $result->content);
        $this->assertFalse($result->is_published);
        $this->assertNull($result->published_at);
    }
}
