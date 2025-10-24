<?php
namespace App\Repository;

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

    public function create(string $title, string $content, bool $published): Post
    {
        $post = new Post();
        $this->setPostData($post, $title, $content, $published);
        return $post;
    }

    public function update(int $id, EditPostRequest $request): Post
    {
        $post = $this->findById($id);
        if ($request->title) {
            $post->setTitleAndSlug($request->title);
        }
        if ($request->post_content) {
            $post->setContent($request->post_content);
        }
        if ($request->is_published !== null) {
            if ($request->is_published === true) {
                $post->publish();
            } else {
                $post->unpublish();
            }
        }
        $post->save();
        return $post;
    }

    public function delete(int $id): void
    {
        $post = $this->findById($id);
        $post->delete();
    }

    private function setPostData(Post $post, string $title, string $content, bool $published): void
    {
        $post->setTitleAndSlug($title);
        $post->setContent($content);
        if ($published) {
            $post->publish();
        }
        $post->save();
        $post->refresh();
    }
}
