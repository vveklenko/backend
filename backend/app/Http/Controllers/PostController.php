<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category_Post;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();
        foreach($posts as $some) {
            $this->count_rate($some->id, 'post');
        }
        return $posts;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|string|unique:posts,title',
            'content' => 'required|string',
            'categories' => 'required|array'
        ]);

        $post = Post::create([
            'title' => $fields['title'],
            'content' => $fields['content'],
            'categories' => json_encode($fields['categories']),
            'author' => auth()->user()->id
        ]);

        foreach($fields['categories'] as $category_id) {
            Category_Post::create([
                'post_id' => $post->id,
                'category_id' => $category_id
            ]);
        }

        return $post;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->count_rate($id, 'post');
        
        $post = Post::find($id);
        if(!$post) {
            return response([
                "message" => "This post was not found"
            ], 404);
        }
        

        return $post;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if(!$post) {
            return response([
                "message" => "This post was not found"
            ], 404);
        }

        if(auth()->user()->id == $post->author) {
            $post->update($request->all());
            return $post;
        }
        else {
            return response([
                "message" => "You cannot update this post because you are not a creator"
            ], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        if(!$post) {
            return response([
                "message" => "This post was not found"
            ], 404);
        }

        if(auth()->user()->id == $post->author || auth()->user()->$role == 'admin') {
            Post::destroy($id);
            return [
                'message' => 'Post was deleted'
            ];
        }
        else {
            return response([
                "message" => "You cannot delete this post because you are not a creator or admin"
            ], 401);
        }
    }

    public function show_categories($id) {
        $post = Post::find($id);
         
        if(!$post)
            return response([
                'message' => 'This post was not found'
            ], 404);
        
        $posts = Category_Post::where('post_id', $post->id)->get();
        $result = array();
        $i = 0;
        foreach($posts as $id){
            $result[$i] = Category::where('id', $id->category_id)->first();
            $i++;
        }
        return $result;
    }

    public function create_comment(Request $request, $id) {
        $post = Post::find($id);

        if(!$post) {
            return response([
                "message" => "This post was not found"
            ], 404);
        }

        $fields = $request->validate([
            'content' => "required|string"
        ]);

        $comment = Comment::create([
            'author' => auth()->user()->id,
            'post' => $post->id,
            'content' => $fields['content']
        ]);

        return $comment;
    }

    public function show_comments($id) {
        $comments = Comment::where('post', $id)->get();
        foreach($comments as $some) {
            $this->count_rate($some->id, 'comment');
        }
        return $comments;
    }

    public function create_like(Request $request, $id) {
        $post = Post::find($id);

        if(!$post) {
            return response([
                "message" => "This post was not found"
            ], 404);
        }

        $request->validate([
            'type' => 'required'
        ]);

        $check_like = Like::where('author', auth()->user()->id)->where('post', $post->id)->first();

        if($check_like) {
            if($check_like->type == $request['type']) {
                return response([
                    "message" => "You cannot like this post twice"
                ], 401);
            }
            $check_like->update($request->all());

            return $check_like;
        }

        $like = Like::create([
            'author' => auth()->user()->id,
            'post' => $post->id,
            'type' => $request['type']
        ]);

        return $like;
    }

    public function show_likes($id) {
        return Like::where('post', $id)->get();
    }

    public function delete_like($id) {
        $post = Post::find($id);

        if(!$post) {
            return response([
                "message" => "This post was not found"
            ], 404);
        }

        $like = Like::where('post', $post->id)->where('author', auth()->user()->id)->first();

        if(!$like) {
            return response([
                "message" => "You cannot delete a like"
            ], 401);
        }
       
        Like::destroy($like->id);
        return [
            'message' => 'Like was deleted'
        ];
    }


    public function filter(Request $request) {
        if($request['categories'])
            return Post::where('categories', $request['categories'])->get();

        if($request['status'])
            return Post::where('status', $request['status'])->get();
    }
    
}
