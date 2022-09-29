<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class LoginTest extends DuskTestCase
{

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testInitialPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Laravel');
        });
    }

    public function testGoToLoginPage()
    {
        $this->browse(function (Browser $browser) {
            if ($browser->seeLink('Log in')) {
                $browser->clickLink('Log in', 'a#login')->visit('/login')
                ->assertSee('Login')->assertPathIs('/login');
            }
        });
    }

}
