<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Like;

class CommentController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->count_rate($id, 'comment');

        $comment = Comment::find($id);
        if(!$comment) {
            return response([
                "message" => "This comment was not found"
            ], 404);
        }
        
        return $comment;
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
        $comment = Comment::find($id);

        if(!$comment) {
            return response([
                "message" => "This comment was not found"
            ], 404);
        }

        if(auth()->user()->id == $comment->author) {
            $comment->update($request->all());
            return $comment;
        }
        else {
            return response([
                "message" => "You cannot update this comment because you are not a creator"
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
        $comment = Comment::find($id);

        if(!$comment) {
            return response([
                "message" => "This comment was not found"
            ], 404);
        }

        if(auth()->user()->id == $comment->author || auth()->user()->$role == 'admin') {
            Comment::destroy($id);
            return [
                'message' => 'Comment was deleted'
            ];
        }
        else {
            return response([
                "message" => "You cannot delete this comment because you are not a creator or admin"
            ], 401);
        }
    }

    //replies
    public function reply_comment(Request $request, $id)
    {
        $comment = Comment::find($id);

        if(!$comment)
            return response([
                'message' => 'This comment was not found'
            ], 404);

        $fields = $request->validate([
            'content' => 'required|string'
        ]);

        $reply = Comment::create([
            'author' => auth()->user()->id,
            'comment' => $comment->id,
            'content' => $fields['content']
        ]);

        return $reply;
    }

    public function get_reply($id) {
        return Comment::where('comment', $id)->get();
    }

    //likes
    public function create_like(Request $request, $id) {
        $comment = Comment::find($id);

        if(!$comment) {
            return response([
                "message" => "This comment was not found"
            ], 404);
        }

        $request->validate([
            'type' => 'required'
        ]);

        $check_like = Like::where('author', auth()->user()->id)->where('comment', $comment->id)->first();

        if($check_like) {
            if($check_like->type == $request['type']) {
                return response([
                    "message" => "You cannot like this comment twice"
                ], 401);
            }
            $check_like->update($request->all());
            return $check_like;
        }

        $like = Like::create([
            'author' => auth()->user()->id,
            'comment' => $comment->id,
            'type' => $request['type']
        ]);

        return $like;
    }

    public function show_likes($id) {
        return Like::where('comment', $id)->get();
    }

    public function delete_like($id) {
        $comment = Comment::find($id);

        if(!$comment) {
            return response([
                "message" => "This comment was not found"
            ], 404);
        }

        $like = Like::where('comment', $comment->id)->where('author', auth()->user()->id)->first();

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
}
