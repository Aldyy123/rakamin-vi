<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Dusk\Concerns\MakesAssertions;
use Tests\TestCase;

class APITest extends TestCase
{

    use RefreshDatabase, MakesAssertions, WithFaker;
    protected $seed = true;

    public function testGetToken()
    {
        shell_exec('php artisan passport:install');

        $response = $this->post('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);
        $response->assertJson([
            'token' => $response['token']
        ]);
        $response->assertSuccessful();
        $response->assertStatus(201);
        return $response;
    }

    public function Token()
    {
        return [
            'Authorization' => 'Bearer ' . $this->testGetToken()['token'],
        ];
    }

    public function DataDummyCategory()
    {
        return [
            'name' => $this->faker()->randomElement(['Teknologi', 'Design', 'Bisnis']),
        ];
    }

    public function DataDummyArticle($category)
    {
        $path = new UploadedFile(storage_path('app/public/articles/test.png'), 'test.png', 'image/png', null, true);
        return [
            'name' => $this->faker()->randomElement(['Teknologi', 'Design', 'Bisnis']),
            'category_id' => $category->id,
            'title' => $this->faker()->sentence(),
            'content' => $this->faker()->paragraph(10),
            'image' => $path
        ];
    }

    public function testCreateFailCategoryCauseToken()
    {
        $this->testGetToken();
        $response = $this->postJson('/api/v1/categories', $this->DataDummyCategory());
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'message',
        ]);
        $response->assertJsonFragment([
            'message' => $response->decodeResponseJson()['message'],
        ]);
    }

    public function testCreateCategorySuccess()
    {
        $token = $this->Token();
        $response = $this->json('POST', '/api/v1/categories', $this->DataDummyCategory(), $token);
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'message',
            'category'
        ]);
        $response->assertJsonFragment([
            'name' => $response->decodeResponseJson()['category']['name'],
            'id' => $response->decodeResponseJson()['category']['id'],
        ]);
        $this->assertDatabaseHas('categories', [
            'name' => $response->decodeResponseJson()['category']['name'],
            'id' => $response->decodeResponseJson()['category']['id'],
        ]);
    }

    public function testGetCategories()
    {
        $this->testCreateCategorySuccess();
        $response = $this->json('GET', '/api/v1/categories', [], $this->Token());
        $response->assertSuccessful();
        $response->assertJsonFragment([
            'categories' => $response->decodeResponseJson()['categories'],
        ]);
    }

    public function testUpdateCategories()
    {
        $this->testCreateCategorySuccess();
        $category = Category::all()->first();
        $dummy = $this->DataDummyCategory();
        $response = $this->json('PUT', '/api/v1/categories/' . $category->id, $dummy, $this->Token());
        $response->assertSuccessful();
        $response->assertJsonFragment([
            'name' => $response->decodeResponseJson()['category']['name'],
        ]);

        $category = Category::all()->first();
        $this->assertDatabaseHas('categories', [
            'name' => $category->name
        ]);

    }

    public function testDeleteCategory()
    {
        $this->testCreateCategorySuccess();
        $category = Category::all()->first();
        $response = $this->json('DELETE', '/api/v1/categories/' . $category->id, [], $this->Token());

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
        $category = Category::all()->first();

        $response->assertSuccessful();
        $response->assertJsonStructure(['message']);
        $response->assertJsonMissing([
            $category
        ]);

    }

    public function testEmptyFormToCreateCategory()
    {
        $response = $this->postJson('/api/v1/categories', [], $this->Token());
        $response->assertStatus(400);
        $response->assertJsonStructure([
            'errors',
        ]);
        $response->assertJsonFragment([
            'errors' => $response->decodeResponseJson()['errors']
        ]);
    }

    public function testFailCreateArticleCauseNoToken()
    {
        $this->testCreateCategorySuccess();
        $category = Category::all()->first();
        $response = $this->json('POST', '/api/v1/articles', $this->DataDummyArticle($category));
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'message' => $response->decodeResponseJson()['message'],
        ]);
    }

    public function testSuccessCreateArticleToken()
    {
        $this->testCreateCategorySuccess();
        $category = Category::all()->first();
        $article = $this->DataDummyArticle($category);
        $response = $this->json('POST', '/api/v1/articles', $article, $this->Token());
        $response->assertSuccessful();
        $response->assertJsonFragment([
            'article' => $response->decodeResponseJson()['article'],
            'message' => $response->decodeResponseJson()['message'],
        ]);

        $this->assertDatabaseHas('articles', [
            'id' => $response->decodeResponseJson()['article']['id'],
        ]);
        Storage::assertExists($response->decodeResponseJson()['article']['image']);
    }

    public function testShowArticlesNoToken()
    {
        $this->testSuccessCreateArticleToken();
        $response = $this->json('GET', '/api/v1/articles');
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'message' => $response->decodeResponseJson()['message'],
        ]);
    }

    public function testShowArticlesToken(){
        $this->testSuccessCreateArticleToken();
        $response = $this->json('GET', '/api/v1/articles', [], $this->Token());
        $response->assertSuccessful();
        $response->assertJsonFragment([
            'articles' => $response->decodeResponseJson()['articles'],
        ]);
    }

    public function testUpdateArticleNoToken(){
        $this->testSuccessCreateArticleToken();
        $category = Category::all()->first();
        $article = Article::all()->first();
        
        $response = $this->json('PUT', '/api/v1/articles/' . $article->id, $this->DataDummyArticle($category));
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'message' => $response->decodeResponseJson()['message'],
        ]);
    }

    public function testUpdateArticleToken(){
        $this->testSuccessCreateArticleToken();
        $category = Category::all()->first();
        $article = Article::all()->first();
        
        $response = $this->json('PUT', '/api/v1/articles/' . $article->id, $this->DataDummyArticle($category), $this->Token());
        $this->assertDatabaseMissing('articles', [
            'title' => $article->id
        ]);

        $article = Article::all()->first();
        $this->assertDatabaseHas('articles', [
            'title' => $article->title
        ]);
        $response->assertSuccessful();
        $response->assertJsonFragment([
            'article' => $response->decodeResponseJson()['article'],
            'message' => $response->decodeResponseJson()['message'],
        ]);
    }

    public function testShowArticleNoToken(){
        $this->testSuccessCreateArticleToken();
        $article = Article::all()->first();
        
        $response = $this->json('GET', '/api/v1/articles/' . $article->id);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'message' => $response->decodeResponseJson()['message'],
        ]);
    }

    public function testShowArticleToken(){
        $this->testSuccessCreateArticleToken();
        $article = Article::all()->first();
        
        $response = $this->json('GET', '/api/v1/articles/' . $article->id, [], $this->Token());
        $response->assertSuccessful();
        $response->assertJsonFragment([
            'article' => $response->decodeResponseJson()['article'],
        ]);
    }

    public function testDeleteArticleNoToken(){
        $this->testSuccessCreateArticleToken();
        $article = Article::all()->first();
        
        $response = $this->json('DELETE', '/api/v1/articles/' . $article->id);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'message' => $response->decodeResponseJson()['message'],
        ]);
    }

    public function testDeleteArticleToken(){
        $this->testSuccessCreateArticleToken();
        $article = Article::all()->first();
        
        $response = $this->json('DELETE', '/api/v1/articles/' . $article->id, [], $this->Token());
        $response->assertSuccessful();
        $response->assertJsonFragment([
            'message' => $response->decodeResponseJson()['message'],
        ]);

        $this->assertDatabaseMissing('articles', [
            'title' => $article->title,
            'id' => $article->id,
        ]);
    }


}

