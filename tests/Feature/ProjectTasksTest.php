<?php

namespace Tests\Feature;

use App\Project;
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
       $this->withoutExceptionHandling();
       $this->signIn();

       $project = auth()->user()->projects()->create(
           factory(Project::class)->raw()
       );

       $task = $project->addTask('test task');

       $this->patch($task->path(), [
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

        $project = factory('App\Project')->create();

        $task = $project->addTask('test task');

        $this->patch($project->path() . '/tasks/' . $task->id, ['body' => 'changed'])
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => 'changed']);
    }

    /** @test */
    public function only_the_owner_of_task_can_add_task()
    {
        $this->signIn();

        $project = factory('App\Project')->create();

        $this->post($project->path() . '/tasks', ['body' => 'Test task'])
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => 'Test task']);
    }
    /** @test */
   public function a_task_requires_a_body()
   {
       $this->signIn();

       $attributes = factory('App\Task')->raw(['body' => '']);

       $project = auth()->user()->projects()->create(
           factory(Project::class)->raw()
       );

       $this->post($project->path() . '/tasks', $attributes)->assertSessionHasErrors('body');
   }
}
