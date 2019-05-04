<?php

namespace Tests\Feature;

use App\Project;
use Facades\Tests\Setup\ProjectFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTasksTest extends TestCase
{
   use RefreshDatabase;
   
   /** @test */
   public function a_user_can_create_tasks()
   {
       $this->withoutExceptionHandling();

       $this->signIn();

       $project = auth()->user()->projects()->create(
           factory(Project::class)->raw()
       );

       $this->post($project->path() . '/tasks', ['body' => 'Test test']);

       $this->get($project->path())
           ->assertSee('Test test');
   }
   
   /** @test */
   public function a_task_can_be_updated()
   {
       $project = ProjectFactory::withTasks(1)->create();

       $this->actingAs($project->owner)->patch($project->tasks->first()->path(), [
           'body' => 'changed',
           'completed' => true,
       ]);

       $this->assertDatabaseHas('tasks', [
           'body' => 'changed',
           'completed' => true,
       ]);
   }

    /** @test */
    public function only_the_owner_of_a_project_may_update_a_task()
    {
        $this->signIn();

        $project = ProjectFactory::withTasks(1)->create();

        $this->patch($project->tasks->first()->path(), ['body' => 'changed'])
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => 'changed']);
    }

    /** @test */
    public function only_the_owner_of_task_can_add_task()
    {
        $project = ProjectFactory::create();

        $this->actingAs($this->signIn())->post($project->path() . '/tasks', ['body' => 'Test task'])
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => 'Test task']);
    }

    /** @test */
   public function a_task_requires_a_body()
   {
       $project = ProjectFactory::create();

       $attributes = factory('App\Task')->raw(['body' => '']);

       $this->actingAs($project->owner)->post($project->path() . '/tasks', $attributes)->assertSessionHasErrors('body');
   }
}
