<?php

namespace Tests\Feature;

use App\Project;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageProjectsTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */

    public function a_user_can_create_a_project()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'notes' => 'General note'
        ];

        $response = $this->post('/projects', $attributes);

        $project = Project::where($attributes)->first(); //we track down the project

        $response->assertRedirect($project->path());  //we should be redirected to the project path

        $this->assertDatabaseHas('projects', $attributes); //it should be in the database

        $this->get($project->path())
            ->assertSee($attributes['title'])  //we should see the title in the dashboard
            ->assertSee($attributes['description'])
            ->assertSee($attributes['notes']);
    }
    
    /** @test */
    public function a_user_can_update_a_project()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $project = factory('App\Project')->create(['owner_id' => auth()->id()]);

        $this->patch($project->path(), [
           'notes' => 'changed',
        ])->assertRedirect($project->path());

        $this->assertDatabaseHas('projects', ['notes' => 'changed']);

    }

    /** @test */
    public function guest_cannot_control_a_project()
    {
        $project = factory('App\Project')->create();

        $this->post('/projects', $project->toArray())->assertRedirect('/login');

        $this->get('/projects/create')->assertRedirect('/login');

        $this->get($project->path())->assertRedirect('/login');

        $this->get('/projects')->assertRedirect('/login');

    }

    /** @test */
    public function authenticated_user_cannot_update_projects_of_others()
    {
        $this->signIn();

//        $this->withoutExceptionHandling();

        $project = factory('App\Project')->create();

        $this->patch($project->path(), [])->assertStatus(403);
    }

    /** @test */
    public function authenticated_user_cannot_view_projects_of_others()
    {
            $this->signIn();
//        $this->withoutExceptionHandling();

        $project = factory('App\Project')->create();

        $this->get($project->path())->assertStatus(403);
    }

    /** @test */
    public function a_user_can_view_their_project()
    {
//        $this->withoutExceptionHandling();

        $this->signIn();

        $this->get('/projects/create')->assertStatus(200);

        $project = factory('App\Project')->create(['owner_id' => auth()->id()]);

        $this->get($project->path())
            ->assertSee($project->title)
            ->assertSee($project->description);
    }

    /** @test */
    public function a_project_must_have_a_title ()
    {
        $this->signIn();
        $attributes = factory('App\Project')->raw(['title' => '']);
        $this->post('/projects', $attributes)->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_project_must_have_a_description ()
    {
        $this->signIn();
        $attributes = factory('App\Project')->raw(['description' => '']);
        $this->post('/projects', $attributes)->assertSessionHasErrors('description');
    }
}
