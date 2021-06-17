<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Category_Post;
use App\Models\Post;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Category::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(auth()->user()->role == 'admin') {
            $fields = $request->validate([
                'title' => 'string|required|unique:categories,title',
                'description' => 'string'
            ]);
    
            $category = Category::create([
                'title' => $fields['title'],
                'description' => $fields['description']
            ]);
    
            return $category;
        }

        return response([
            "message" => "You cannot create a category because you are not admin"
        ], 401);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::find($id);
        if(!$category) {
            return response([
                "message" => "This category was not found"
            ], 404);
        }
        return $category;
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
        if(auth()->user()->role == 'admin') {
            $category = Category::find($id);
            $category->update($request->all());
            return $category;
        }
        return response([
            "message" => "You cannot update a category because you are not admin"
        ], 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(auth()->user()->role == 'admin') {
            $category = Category::find($id);
            if(!$category) 
                return response([
                    "message" => "This category was not found"
                ], 401);
            
            Category::destroy($id);
            return [
                'message' => "Category was deleted"
            ];
        }
        return response([
            "message" => "You cannot delete a category because you are not admin"
        ], 401);
        
    }

    public function show_posts($id) {
        $category = Category::find($id);
         
        if(!$category)
            return response([
                'message' => 'This category was not found'
            ], 404);
        
        $posts = Category_Post::where('category_id', $category->id)->get();
        $result = array();
        $i = 0;
        foreach($posts as $id){
            $result[$i] = Post::where('id', $id->post_id)->first();
            $i++;
        }
        return $result;
    }
}
