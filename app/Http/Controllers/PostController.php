<?php
namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\EditPostRequest;
use App\Repository\PostRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        try {
            return $this->successJson($this->postRepository->findById($id));
        } catch (ModelNotFoundException $e) {
            return $this->notFoundJson();
        }
    }

    public function createPost(CreatePostRequest $request)
    {
        $post = $this->postRepository->create($request->title, $request->content, $request->is_published);
        return $this->successJson($post);
    }

    public function editPost(int $id, EditPostRequest $request)
    {
        try {
            $post = $this->postRepository->update(
                $id,
                $request->title,
                $request->content,
                $request->is_published
            );
            return $this->successJson($post);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundJson();
        }
    }
}
