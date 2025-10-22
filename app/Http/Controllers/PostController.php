<?php
namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
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

    public function createPost(CreatePostRequest $request)
    {
        $post = $this->postRepository->create($request->title, $request->content, $request->is_published);
        return $this->successJson($post);
    }
}
