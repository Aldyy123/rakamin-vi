<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Articles extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $article = Article::with('category')->paginate(2);
        return response()->json([
            'articles' => $article
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $input = $request->all();
            $validated = Validator::make($input, [
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required',
                'image' => 'required|image',
            ]);

            if ($validated->fails()) {
                return response()->json([
                    'errors' => $validated->errors()
                ]);
            }
            $input['user_id'] = Auth::guard('api')->user()->id;
            // dd($input);
            $path = Storage::disk('local')->put('public/articles', $request->file('image'));
            $input['image'] = $path;
            $article = Article::create($input);
            return response()->json([
                'message' => 'Article successfully created.',
                'article' => $article
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        return response()->json([
            'article' => $article->with('category')->where('id', $article->id)->first()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        try {
            $input = $request->all();
            $validated = Validator::make($input, [
                'image' => 'image',
            ]);

            if ($validated->fails()) {
                return response()->json([
                    'errors' => $validated->errors()
                ]);
            }
            $input['user_id'] = Auth::guard('api')->user()->id;

            if($request->hasFile('image')){
                Storage::delete($article->image);
                $path = Storage::disk('local')->put('public/articles', $request->file('image'));
                $input['image'] = $path;
            }
            $article = $article->update($input);
            return response()->json([
                'message' => 'Article successfully created.',
                'article' => $article
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        $article->delete();

        return response()->json([
            'message' => "Delete article successfully",
        ]);
    }
}
