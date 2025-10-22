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
}
