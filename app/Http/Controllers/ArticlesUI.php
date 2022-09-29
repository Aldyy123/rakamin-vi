<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ArticlesUI extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('articles.home', [
            'articles' => Article::all(),
            'i' => 0
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        return view('articles.create', [
            'user' => $user,
            'categories' => $user->categories
        ]);
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
                return redirect('articles/create')
                    ->withErrors($validated)
                    ->withInput();
            }
            $path = Storage::disk('local')->put('public/articles', $request->file('image'));
            $input['image'] = $path;
            Article::create($input);
            return redirect('articles')->with('success', 'Article created successfully.');
        } catch (\Throwable $th) {
            // throw $th;
            return redirect('articles/create')
                ->withErrors($th)->withInput();
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
        return view('articles.show', [
            'article' => $article
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        $user = Auth::user();
        return view('articles.edit', [
            'article' => $article,
            'user' => $user,
            'categories' => $user->categories
        ]);
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
                'title' => 'required',
                'content' => 'required',
                'category_id' => 'required',
                'image' => 'image',
            ]);
            if ($validated->fails()) {
                return redirect(route('articles.edit', $article->id))
                    ->withErrors($validated)
                    ->withInput();
            }

            if($request->hasFile('image')){
                Storage::delete($article->image);
                $path = Storage::disk('local')->put('public/articles', $request->file('image'));
                $input['image'] = $path;
            }

            $article->update($input);
            return redirect('articles')->with('success', 'Article updated successfully.');
        } catch (\Throwable $th) {
            // throw $th;
            return redirect(route('articles.edit', $article->id))
                ->withErrors($th)->withInput();
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
        try {
           
            $article->delete();
            return redirect('articles')->with('success', 'Article deleted successfully.');
        } catch (\Throwable $th) {
            // throw $th;
            return redirect('articles')
                ->withErrors($th)->withInput();
        }
    }
}
