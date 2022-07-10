<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Repository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RepositoryControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;


    /* Test routes protected */
    public function test_guest()
    {
        $this->get('repositories')->assertRedirect('login'); /* Index */
        $this->get('repositories/1')->assertRedirect('login'); /* Show */
        $this->get('repositories/1/edit')->assertRedirect('login'); /* Form Edit */
        $this->put('repositories/1')->assertRedirect('login'); /* Update */
        $this->delete('repositories/1')->assertRedirect('login'); /* delete */
        $this->post('repositories', [])->assertRedirect('login'); /* Create */
        $this->get('repositories/create')->assertRedirect('login'); /* Form Create */
    }

    public function test_index_empty()
    {
        Repository::factory()->create(); // user_id = 1

        $user = User::factory()->create(); // id = 2

        $this
            ->actingAs($user)
            ->get('repositories')
            ->assertStatus(200)
            ->assertSee('No hay repositorios creados');
    }

    public function test_index_with_data()
    {
        $user = User::factory()->create(); // id = 1
        $repository = Repository::factory()->create(['user_id' => $user->id]); // user_id = 1

        $this
            ->actingAs($user)
            ->get('repositories')
            ->assertStatus(200)
            ->assertSee($repository->id)
            ->assertSee($repository->url);
    }

    /* Store */
    public function test_create()
    {
        $user = User::factory()->create();       

        $this
            ->actingAs($user)
            ->get("repositories/create")
            ->assertStatus(200);
           
    }

    public function test_store()
    {
        /* Simulamos un formulario de creacion */
        $data = [
            'url' => $this->faker->url,
            'description' => $this->faker->text
        ];

        /* Creamos el usuario y simulamos que ese usuario esta login */
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->post('repositories', $data)
            ->assertRedirect('repositories');

        $this->assertDatabaseHas('repositories', $data);
    }


    public function test_update()
    {
        /* Creamos el usuario y simulamos que ese usuario esta login */
        $user = User::factory()->create();
        /* Antes de actualizar validamos que exista un registro */
        $repository = Repository::factory()->create(['user_id' => $user->id]);

        /* Simulamos un formulario de creacion */
        $data = [
            'url' => $this->faker->url,
            'description' => $this->faker->text
        ];



        $this
            ->actingAs($user)
            ->put("repositories/$repository->id", $data)
            ->assertRedirect("repositories/$repository->id/edit");

        $this->assertDatabaseHas('repositories', $data);
    }

    public function test_update_policy()
    {
        /* Creamos el usuario y simulamos que ese usuario esta login */
        $user = User::factory()->create();

        /* Antes de actualizar validamos que exista un registro y que el registro pertenezca a el user login */
        $repository = Repository::factory()->create();

        /* Simulamos un formulario de creacion */
        $data = [
            'url' => $this->faker->url,
            'description' => $this->faker->text
        ];

        $this
            ->actingAs($user)
            ->put("repositories/$repository->id", $data)
            ->assertStatus(403);
    }


    /* Test de validacion de datos */

    public function test_validate_store()
    {
        /* Creamos el usuario y actuamos como el usuario login */
        $user = User::factory()->create();
        /* Enviamos data vacia asi que esperamos una redireccion 302 */
        $this
            ->actingAs($user)
            ->post('repositories', [])
            ->assertStatus(302)
            ->assertSessionHasErrors(['url', 'description']);
    }

    public function test_validate_update()
    {
        /* Creamos el repositorio y lo asignamos al user loging */
        $repository = Repository::factory()->create();
        $user = User::factory()->create();
        /* Enviamos data vacia asi que esperamos una redireccion de tipo 302 */
        $this
            ->actingAs($user)
            ->put("repositories/$repository->id", [])
            ->assertStatus(302)
            ->assertSessionHasErrors(['url', 'description']);
    }




    public function test_destroy()
    {

        /* Creamos el usuario y simulamos que ese usuario esta login */
        $user = User::factory()->create();
        /* Antes de actualizar validamos que exista un registro */
        $repository = Repository::factory()->create(['user_id' => $user->id]);

        $this
            ->actingAs($user)
            ->delete("repositories/$repository->id")
            ->assertRedirect('repositories');

        $this->assertDatabaseMissing('repositories', $repository->toArray());
    }

    public function test_destroy_policy()
    {
        /* Creamos el usuario y simulamos que ese usuario esta login */
        $user = User::factory()->create();

        /* Antes de actualizar validamos que exista un registro, creamos el repo con un usuario nuevo por ende al tratar de eliminarlo devuelve un 403 */
        $repository = Repository::factory()->create();
        $this
            ->actingAs($user)
            ->delete("repositories/$repository->id")
            ->assertStatus(403);
    }

    /* Show */

    public function test_show()
    {
        $user = User::factory()->create();
        $repository = Repository::factory()->create(['user_id' => $user->id]);

        $this
            ->actingAs($user)
            ->get("repositories/$repository->id")
            ->assertStatus(200);
    }

    public function test_show_policy()
    {
        $user = User::factory()->create(); // id = 1
        $repository = Repository::factory()->create(); // user_id = 2

        $this
            ->actingAs($user)
            ->get("repositories/$repository->id")
            ->assertStatus(403);
    }

    /* Edit */

    public function test_edit()
    {
        $user = User::factory()->create();
        $repository = Repository::factory()->create(['user_id' => $user->id]);

        $this
            ->actingAs($user)
            ->get("repositories/$repository->id/edit")
            ->assertStatus(200)
            ->assertSee($repository->url)
            ->assertSee($repository->description);
    }

    public function test_edit_policy()
    {
        $user = User::factory()->create(); // id = 1
        $repository = Repository::factory()->create(); // user_id = 2

        $this
            ->actingAs($user)
            ->get("repositories/$repository->id/edit")
            ->assertStatus(403);
    }

    

}
