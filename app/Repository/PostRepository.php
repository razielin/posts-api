<?php
namespace App\Repository;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\EditPostRequest;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

class PostRepository
{
    public function all(): Collection
    {
        return Post::all();
    }

    public function findById(int $id): Post
    {
        return Post::query()->findOrFail($id);
    }

    public function create(CreatePostRequest $request): Post
    {
        $post = new Post();
        $this->setPostData($request, $post);
        return $post;
    }

    public function update(int $id, EditPostRequest $request): Post
    {
        $post = $this->findById($id);
        $this->setPostData($request, $post);
        return $post;
    }

    public function delete(int $id): void
    {
        $post = $this->findById($id);
        $post->delete();
    }

    private function findBySlug(string $slug, ?int $excludeId = null): ?Post
    {
        $query = Post::query()->where('slug', $slug);
        if ($excludeId) {
            $query->whereNot('id', $excludeId);
        }
        return $query->first();
    }

    private function setPostData(EditPostRequest|CreatePostRequest $request, Post $post): void
    {
        if ($request->title) {
            $post->setTitleAndSlug($request->title);
        }
        $this->setAutoincrementedSlugIfRequired($post);

        if ($request->post_content) {
            $post->setContent($request->post_content);
        }
        $this->setPublishedStatus($request->is_published, $post);
        $post->save();
        $post->refresh();
    }

    private function setAutoincrementedSlugIfRequired(Post $post): void
    {
        $postWithDuplicateSlug = $this->findBySlug($post->getSlug(), $post->id);
        while ($postWithDuplicateSlug) {
            $postWithDuplicateSlug = $this->findBySlug($post->getSlug(), $post->id);
            if (!$postWithDuplicateSlug) {
                break;
            }
            $post->generateAutoincrementedSlug($postWithDuplicateSlug->getSlug());
        }
    }

    private function setPublishedStatus(?bool $is_published, Post $post): void
    {
        if ($is_published !== null) {
            if ($is_published === true) {
                $post->publish();
            } else {
                $post->unpublish();
            }
        }
    }
}
