<?php
namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\EditPostRequest;
use App\Repository\PostRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function __construct(private readonly PostRepository $postRepository)
    {
    }

    public function allPosts()
    {
        try {
            $posts = $this->postRepository->all();
            return $this->successJson($posts);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve all posts', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->failedJson('Failed to retrieve posts');
        }
    }

    public function getPost(int $id)
    {
        try {
            $post = $this->postRepository->findById($id);
            return $this->successJson($post);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundJson($e);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve post', [
                'post_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->failedJson('Failed to retrieve post');
        }
    }

    public function createPost(CreatePostRequest $request)
    {
        try {
            $post = $this->postRepository->create($request);
            return $this->successJson($post);
        } catch (\Exception $e) {
            Log::error('Failed to create post', [
                'title' => $request->title ?? 'Unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->failedJson('Failed to create post');
        }
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
            Log::warning('Post not found for update', ['post_id' => $id, 'error' => $e->getMessage()]);
            return $this->notFoundJson($e);
        } catch (\Exception $e) {
            Log::error('Failed to update post', [
                'post_id' => $id,
                'title' => $request->title ?? 'Unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->failedJson('Failed to update post');
        }
    }

    public function deletePost(int $id)
    {
        try {
            $this->postRepository->delete($id);
            return $this->successJson(['message' => 'Post deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return $this->notFoundJson($e);
        } catch (\Exception $e) {
            Log::error('Failed to delete post', [
                'post_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->failedJson('Failed to delete post');
        }
    }
}
