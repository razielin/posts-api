<?php
namespace App\Repository;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

class PostRepository
{
    public function all(): Collection
    {
        return Post::all();
    }

    public function findById(int $id)
    {
        return Post::query()->findOrFail($id);
    }

    public function create(string $title, string $content, bool $published): Post
    {
        $post = new Post();
        $post->setTitleAndSlug($title);
        $post->setContent($content);
        if ($published) {
            $post->publish();
        }
        $post->save();
        $post->refresh();
        return $post;
    }
}
