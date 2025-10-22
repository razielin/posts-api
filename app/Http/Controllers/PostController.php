<?php
namespace App\Http\Controllers;

use App\Models\Post;
use App\Repository\PostRepository;

class PostController extends Controller
{
    public function __construct(private readonly PostRepository $postRepository)
    {
    }

    public function allPosts()
    {
        return $this->successJson($this->postRepository->all());
    }

    public function getPost(int $id)
    {
        return $this->successJson($this->postRepository->findById($id));
    }
}
