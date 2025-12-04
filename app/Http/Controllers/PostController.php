<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with('user')
            ->active()
            ->paginate(20);

        return PostResource::collection($posts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return 'posts.create';
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        try {
            $post = Auth::user()->posts()->create($request->validated());

            Log::debug('New post created.', ['post_id' => $post->id]);

            return new PostResource($post)
                ->response()
                ->setStatusCode(201);
        } catch (Exception $error) {
            Log::error('Error when create new post', ['error' => $error->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create new post',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::with('user')
            ->active()
            ->findOrFail($id);

        return new PostResource($post);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return 'posts.edit';
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        Gate::authorize('update', $post);

        try {
            $post->update($request->validated());

            Log::debug('Post updated successfully.', ['post_id' => $post->id]);

            return new PostResource($post)
                ->response()
                ->setStatusCode(200);
        } catch (Exception $error) {
            Log::error('Error when updating post', [
                'post_id' => $post->id,
                'error' => $error->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update post',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        $post->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Post deleted successfully.',
        ], 200);
    }
}
