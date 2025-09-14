<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Household;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
    }

    public function test_new_users_can_register()
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_new_users_automatically_get_a_household()
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        
        /** @var User $user */
        $user = User::where('email', 'test@example.com')->first();
        
        // Verificar se o usuário tem um household_id
        $this->assertNotNull($user->household_id);
        
        // Verificar se o household foi criado
        $household = Household::find($user->household_id);
        $this->assertNotNull($household);
        $this->assertEquals("Lar de {$user->name}", $household->name);
        $this->assertEquals($user->id, $household->created_by);
        
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
