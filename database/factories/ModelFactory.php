<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/
use App\Task;
use App\User;
use App\Invitation;
/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'username' => $faker->unique()->userName,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(Task::class, function (Faker\Generator $faker) {

    return [
        'title' => $faker->sentence,
        'user_id' => function(){
          return Factory(User::class)->create()->id;
        },
        'description' => $faker->text,
        'deadline' => $faker->dateTimeBetween('now','+2 months'),
        'private' => $faker->boolean,
        'completed' => $faker->boolean,
        'warned' => $faker->boolean,
    ];
});

$factory->define(Invitation::class, function (Faker\Generator $faker) {

    return [
        'inviting_id' => function(){
            return Factory(User::class)->create()->id;
        },
        'invited_id' => function(){
            return Factory(User::class)->create()->id;
        },
        'task_id' => function(){
            return Factory(Task::class)->create()->id;
        }
    ];
});