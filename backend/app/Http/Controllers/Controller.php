<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Models\User;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function count_rate($id, $type)
    {
        $like = Like::where($type, $id)->get();
        $point = 0;
        foreach($like as $i){
            if($i->type == "like")
                $point ++;
            else 
                $point --;  
        }
        if ($type == 'post') 
            Post::find($id)->update(['rating' => $point]);
        else 
            Comment::find($id)->update(['rating' => $point]);
    }

    public function count_rate_user($id)
    {
        $like = Comment::where('author', $id)->get();
        $point = 0;
        foreach($like as $i){
            $point += $i->rating;
        }
        $like = Post::where('author', $id)->get();
        foreach($like as $i){
            $point += $i->rating;
        }
        User::find($id)->update(['rating' => $point]);
    }
}
