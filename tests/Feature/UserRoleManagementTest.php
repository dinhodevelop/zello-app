<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Household;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_change_user_role_to_admin()
    {
        // Criar um admin
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Criar um usuário regular
        $user = User::factory()->create(['role' => 'user']);

        // Fazer login como admin
        $this->actingAs($admin);

        // Tentar alterar role do usuário para admin
        $response = $this->patch("/user-management/{$user->id}/role", [
            'role' => 'admin'
        ]);

        // Verificar se foi redirecionado com sucesso
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verificar se a role foi alterada no banco
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'admin'
        ]);
    }

    public function test_admin_can_change_admin_role_to_user_when_not_last_admin()
    {
        // Criar dois admins
        $admin1 = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin']);

        // Fazer login como admin1
        $this->actingAs($admin1);

        // Tentar alterar role do admin2 para user
        $response = $this->patch("/user-management/{$admin2->id}/role", [
            'role' => 'user'
        ]);

        // Verificar se foi redirecionado com sucesso
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verificar se a role foi alterada no banco
        $this->assertDatabaseHas('users', [
            'id' => $admin2->id,
            'role' => 'user'
        ]);
    }

    public function test_admin_cannot_change_last_admin_role_to_user()
    {
        // Criar apenas um admin
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Criar um usuário regular
        $user = User::factory()->create(['role' => 'user']);

        // Fazer login como admin
        $this->actingAs($admin);

        // Tentar alterar própria role para user (deve falhar com 403)
        $response = $this->patch("/user-management/{$admin->id}/role", [
            'role' => 'user'
        ]);

        // Verificar se foi negado (403) pela Policy
        $response->assertStatus(403);

        // Verificar se a role não foi alterada
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'admin'
        ]);
    }

    public function test_admin_cannot_change_own_role()
    {
        // Criar um admin
        $admin = User::factory()->create(['role' => 'admin']);

        // Fazer login como admin
        $this->actingAs($admin);

        // Tentar alterar própria role
        $response = $this->patch("/user-management/{$admin->id}/role", [
            'role' => 'user'
        ]);

        // Verificar se foi negado (403) pela Policy
        $response->assertStatus(403);

        // Verificar se a role não foi alterada
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'role' => 'admin'
        ]);
    }

    public function test_regular_user_cannot_change_roles()
    {
        // Criar um usuário regular
        $user = User::factory()->create(['role' => 'user']);
        
        // Criar outro usuário
        $otherUser = User::factory()->create(['role' => 'user']);

        // Fazer login como usuário regular
        $this->actingAs($user);

        // Tentar alterar role do outro usuário
        $response = $this->patch("/user-management/{$otherUser->id}/role", [
            'role' => 'admin'
        ]);

        // Verificar se foi negado (403)
        $response->assertStatus(403);

        // Verificar se a role não foi alterada
        $this->assertDatabaseHas('users', [
            'id' => $otherUser->id,
            'role' => 'user'
        ]);
    }

    public function test_role_validation_requires_valid_role()
    {
        // Criar um admin
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Criar um usuário regular
        $user = User::factory()->create(['role' => 'user']);

        // Fazer login como admin
        $this->actingAs($admin);

        // Tentar alterar role com valor inválido
        $response = $this->patch("/user-management/{$user->id}/role", [
            'role' => 'invalid_role'
        ]);

        // Verificar se houve erro de validação
        $response->assertSessionHasErrors('role');

        // Verificar se a role não foi alterada
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'user'
        ]);
    }
}