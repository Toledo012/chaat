<?php

namespace Tests\Feature;

use App\Models\Cuenta;
use Tests\TestCase;

class AdminUsersAccessTest extends TestCase
{
    public function test_non_admin_user_is_redirected_from_admin_users_index()
    {
        $usuario = new Cuenta([
            'id_cuenta' => 1,
            'username' => 'usuario',
            'password' => 'secret',
            'estado' => 'activo',
            'id_rol' => 2,
        ]);

        $this->actingAs($usuario);

        $response = $this->get(route('admin.users.index'));

        $response->assertRedirect(route('user.dashboard'));
    }
}
