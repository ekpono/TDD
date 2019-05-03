<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use App\Project;
use App\Task;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    return [
        'body' => $faker->paragraph,
        'project_id' => factory(Project::class),
    ];
});
