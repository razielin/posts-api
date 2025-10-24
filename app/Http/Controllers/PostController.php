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
            return $this->notFoundJson($e);
        }
    }

    public function createPost(CreatePostRequest $request)
    {
        $post = $this->postRepository->create($request->title, $request->post_content, $request->is_published);
        return $this->successJson($post);
    }

    public function editPost(int $id, EditPostRequest $request)
    {
        try {
            $data = $request->toArray();
            if (empty($data)) {
                return $this->failedJson("One or more required parameters are missing");
            }
            $post = $this->postRepository->update($id, $request);
            return $this->successJson($post);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundJson($e);
        }
    }

    public function deletePost(int $id)
    {
        try {
            $this->postRepository->delete($id);
            return $this->successJson(['message' => 'Post deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundJson($e);
        }
    }
}
