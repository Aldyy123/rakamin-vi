<?php

namespace Tests\Browser;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class WebCRUDTest extends DuskTestCase
{
    use RefreshDatabase, WithFaker;

    protected $seed = true;

    public function randomCategory()
    {
        return $this->faker()->randomElement(['Teknologi', 'Design']);
    }
    // UploadedFile::fake()->create('test.png')
    // storage_path('app/public/articles/test.png')
    public function fakerArticle()
    {
        $path = new UploadedFile(storage_path('app/public/articles/test.png'), 'test.png', 'image/png', null, true);
        return [
            'title' => $this->faker()->sentence(),
            'category_id' => '1',
            'image' => $path,
            'content' => $this->faker()->paragraph(7)
        ];
    }

    public function testLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')->type('email', 'test@example.com')->type('password', 'password')
                ->press('Login')->assertPathIs('/articles');
        });
    }


    public function testHomeArticles()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/articles')
                ->assertPathIs('/articles')
                ->assertSee('Laravel 9 CRUD Post');
        });
    }

    public function testCategoriesPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->clickLink('Category')->assertSee('Laravel 9 CRUD Categories');
        });
    }

    public function testCreateNewCategory()
    {
        $this->browse(function (Browser $browser) {
            $category = $this->randomCategory();
            $browser->clickLink('Create New Categories');
            $browser->type('name', $category);
            $browser->press('Submit')->assertPathIs('/categories')->assertSee($category);
            $this->assertDatabaseHas('categories', [
                'name' => $category
            ]);
        });
    }

    public function testCreateNewArticle()
    {
        $this->browse(function (Browser $b) {
            Storage::fake('local');
            $fakeArticle = $this->fakerArticle();

            $b->clickLink('Articles')->clickLink('Create New Post');
            $b->type('title', $fakeArticle['title'])
                ->select('category_id', $fakeArticle['category_id'])
                ->attach('image', $fakeArticle['image'])
                ->type('content', $fakeArticle['content'])
                ->press('Submit');

            $fakeArticle['image'] = 'public/articles/' . $fakeArticle['image']->hashName();

            $b->assertPathIs('/articles')->assertSee('Article created successfully.');
        });
    }

    public function testShowArticle()
    {
        $article = Article::find(1);
        $this->browse(function (Browser $b) use ($article) {

            $b->clickLink('Show')->assertPathIs('/articles/' . $article->id);
            $b->assertSee($article->title)->assertSee($article->category->name)
                ->assertAttributeContains('img', 'src', Storage::url($article->image))->assertSee($article->content);
            $b->back()->assertPathIs('/articles');
        });
    }

    public function testEditArticle()
    {
        $this->browse(function (Browser $b) {
            Storage::fake('local');
            $fakeArticle = $this->fakerArticle();

            $b->clickLink('Edit');
            $b->type('title', $fakeArticle['title'])
                ->select('category_id', $fakeArticle['category_id'])
                ->attach('image', $fakeArticle['image'])
                ->type('content', $fakeArticle['content'])
                ->press('Submit');

            $fakeArticle['image'] = 'public/articles/' . $fakeArticle['image']->hashName();

            $b->assertPathIs('/articles')->assertSee('Article updated successfully.');
        });
    }

    public function testDeleteArticle()
    {
        $this->browse(function (Browser $browser) {
            $title = Article::all()->first()->title;
            $browser->visit('/articles');
            $browser->press('Delete');
            $browser->assertDontSee($title);
            $browser->assertPathIs('/articles')->assertSee('Article deleted successfully.');
        });
    }

    public function testShowCategory()
    {
        $this->browse(function (Browser $browser) {
            $category = Category::all()->first();
            $browser->clickLink('Category')->clickLink('Show');
            $browser->assertSee($category->name)->assertSee($category->users->name);
            $browser->back();
        });
    }

    public function testEditCategory()
    {
        $this->browse(function (Browser $browser) {
            $category = $this->randomCategory();
            $browser->clickLink('Edit');
            $browser->type('name', $category);
            $browser->press('Submit')->assertPathIs('/categories')->assertSee($category);
            $this->assertDatabaseHas('categories', [
                'name' => $category
            ]);
        });
    }

    public function testDeleteCategory()
    {
        $this->browse(function (Browser $browser) {
            $name = Category::all()->first()->name;
            $browser->visit('/categories');
            $browser->press('Delete');
            $browser->assertDontSee($name);
            $browser->assertPathIs('/categories');
        });
    }
}
