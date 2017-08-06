<?php

namespace Tests\Feature;

use App\Http\Middleware\TaskOwner;
use App\Invitation;
use Faker\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Task;
use App\User;
class ExampleTest extends TestCase
{
    use DatabaseTransactions;

    private function mockFollow($user_id,$task_id)
    {
        DB::insert('insert into task_user_follow (user_id,task_id) values (?,?)',[$user_id,$task_id]);
    }
    private function mockInvite($sender_id,$invited_id,$task_id)
    {
        DB::insert('insert into invitations (inviting_id,invited_id,task_id) values(?,?,?)',[$sender_id,$invited_id,$task_id]);
    }
    /** @test */
    public function registerTest()
    {

        //when
        $response = $this->post(route('register',[
            'username' => 'BobMarley',
            'name' => 'Bob Marley Bob',
            'email' => 'bob@marley.com',
            'password' => 'bobmarleypassword',
            'password_confirmation' => 'bobmarleypassword'
        ]));
        //then
        $response->assertStatus(200);
        $this->assertDatabaseHas('users',['username' => 'BobMarley']);
    }
    /** @test */
    public function loginTest()
    {
        //given
        $user = Factory(User::class)->create();

        //when
        $response = $this->post(route('login',['email' => $user->email, 'password' => 'secret']));

        //then
        $response->assertStatus(200);
        $token = $response->decodeResponseJson();
        $this->assertArrayHasKey('token',$token);
    }
    /** @test */
    public function searchUsernameTest()
    {
        //given
        $user1 = Factory(User::class)->create();
        $user2 = Factory(User::class)->create();
        //when
        $this->actingAs($user1);
        $response = $this->get(route('search-username',['username' => $user2->username]));
        //then
        $response->assertStatus(200);
        $json = $response->decodeResponseJson();
        $this->assertEquals($user2->username,$json['username']);
    }
    /** @test */
    public function searchNameTest()
    {
        //given
        $user1 = Factory(User::class)->create();
        $user2 = Factory(User::class)->create();
        //when
        $this->actingAs($user1);
        $response = $this->get(route('search-name',['name' => $user2->name]));
        //then
        $response->assertStatus(200);
        $json = $response->decodeResponseJson();
        $this->assertEquals($user2->name,$json['name']);
    }
    /** @test */
    public function searchEmailTest()
    {
        //given
        $user1 = Factory(User::class)->create();
        $user2 = Factory(User::class)->create();
        //when
        $this->actingAs($user1);
        $response = $this->get(route('search-email',['email' => $user2->email]));
        //then
        $response->assertStatus(200);
        $json = $response->decodeResponseJson();
        $this->assertEquals($user2->email,$json['email']);
    }
    /** @test */
    public function GuestTasksTest()
    {
        //given
        $publictasks = Factory(Task::class,10)->create(['private'=>false]);
        $privatetasks = Factory(Task::class,5)->create(['private'=>true]);

        //when
        $response = $this->get(route('tasks'));

        //then
        $response->assertStatus(200);
        $tasks = $response->decodeResponseJson();
        $this->assertCount(10,$tasks);

    }
    /** @test */
    public function feedTest()
    {
        //given
        $user = Factory(User::class)->create();
        $publictasks = Factory(Task::class,10)->create(['private'=>false]);
        $privatetasks = Factory(Task::class,5)->create(['private'=>true]);
        $ownedtasks = Factory(Task::class,6)->create(['user_id'=> $user->id]);
        $followedtasks = Factory(Task::class,2)->create();
        foreach($followedtasks as $task)
        {
            $this->mockFollow($user->id,$task->id);
        }

        //when
        $this->actingAs($user);
        $response = $this->get(route('feed'));

        //then
        $response->assertStatus(200);
        $feed = $response->decodeResponseJson();
        $this->assertCount(8,$feed);
    }

    /** @test */
    public function postTaskTest()
    {
        //given
        $user = Factory(User::class)->create();

        //when
        $this->actingAs($user);
        $response = $this->post(route('post-task',['title' => 'testTitle' , 'description' => 'testDescription']));

        //then
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks',['title' =>'testTitle']);
    }

    /** @test */
    public function showYourTaskTest()
    {
        //given
        $user = Factory(User::class)->create();
        $task = Factory(Task::class)->create(['user_id' => $user->id]);

        //when
        $this->actingAs($user);
        $response = $this->get(route('get-task',['task'=>$task->id]));

        //then
        $response->assertStatus(200);
        $resultTask = $response->decodeResponseJson();
        $this->assertEquals($task->title,$resultTask['title']);
    }
    public function showPublicTaskTest()
    {
        //given
        $user = Factory(User::class)->create();
        $task = Factory(Task::class)->create(['private' => false]);

        //when
        $this->actingAs($user);
        $response = $this->get(route('get-task',['task'=>$task->id]));

        //then
        $response->assertStatus(200);
        $resultTask = $response->decodeResponseJson();
        $this->assertEquals($task->title,$resultTask['title']);
    }

    /** @test */
    public function taskPrivacyTest()
    {
        //given
        $user = Factory(User::class)->create();
        $task = Factory(Task::class)->create(['private'=>true]);

        //when
        $this->actingAs($user);
        $response = $this->get(route('get-task',['task'=>$task->id]));

        //then
        $response->assertStatus(401);
    }
    /** @test */
    public function followPublicTest()
    {
        //given
        $user = Factory(User::class)->create();
        $task = Factory(Task::class)->create(['private' =>false]);

        //when
        $this->actingAs($user);
        $response = $this->post(route('follow-task',['task'=>$task->id]));

        //then
        $response->assertStatus(200);
        $this->assertDatabaseHas('task_user_follow',['task_id' => $task->id, 'user_id' => $user->id ]);
    }
    public function followInvitedTest()
    {
        //given
        $user1 = Factory(User::class)->create();
        $user2 = Factory(User::class)->create();
        $task = Factory(Task::class)->create(['private' =>true,'user_id' => $user1->id]);

        //when
        $this->mockInvite($user1->id,$user2->id,$task->id);
        $this->actingAs($user2);
        $response = $this->post(route('follow-task',['task'=>$task->id]));

        //then
        $response->assertStatus(200);
        $this->assertDatabaseHas('task_user_follow',['task_id' => $task->id, 'user_id' => $user2->id ]);
    }
    /** @test */
    public function setCompleteTest()
    {
        //given
        $user = Factory(User::class)->create();
        $task = Factory(Task::class)->create(['user_id'=>$user->id, 'completed' => false]);

        //when
        $this->actingAs($user);
        $response = $this->post(route('set-complete',['task' =>$task]));
        //then
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks',['id'=> $task->id, 'completed' => true]);
    }
    /** @test */
    public function setIncompleteTest()
    {
        //given
        $user = Factory(User::class)->create();
        $task = Factory(Task::class)->create(['user_id'=>$user->id, 'completed' => true]);

        //when
        $this->actingAs($user);
        $response = $this->post(route('set-incomplete',['task' =>$task]));
        //then
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks',['id'=> $task->id, 'completed' => false]);
    }
    /** @test */
    public function setPrivateTest()
    {
        //given
        $user = Factory(User::class)->create();
        $task = Factory(Task::class)->create(['user_id'=>$user->id, 'private' => false]);

        //when
        $this->actingAs($user);
        $response = $this->post(route('set-private',['task' =>$task]));
        //then
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks',['id'=> $task->id, 'private' => true]);
    }
    /** @test */
    public function setPublicTest()
    {
        //given
        $user = Factory(User::class)->create();
        $task = Factory(Task::class)->create(['user_id'=>$user->id, 'private' => true]);

        //when
        $this->actingAs($user);
        $response = $this->post(route('set-public',['task' =>$task]));
        //then
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks',['id'=> $task->id, 'private' => false]);
    }
    /** @test */
    public function toggleCompleteTest()
    {
        //given
        $user = Factory(User::class)->create();
        $task = Factory(Task::class)->create(['user_id'=>$user->id]);

        //when
        $this->actingAs($user);
        $response = $this->post(route('toggle-complete',['task' =>$task]));
        //then
        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks',['id'=> $task->id, 'completed' => !$task->completed]);
    }
    /** @test */
    public function deleteTaskTest()
    {
        //given
        $user = Factory(User::class)->create();
        $task = Factory(Task::class)->create(['user_id'=>$user->id]);

        //when
        $this->actingAs($user);
        $response = $this->delete(route('delete-task',['task' =>$task]));
        //then
        $response->assertStatus(200);

        $this->assertDatabaseMissing('tasks',['id'=> $task->id]);
    }
    /** @test */
    public function inviteTest()
    {
        //given
        $user = Factory(User::class)->create();
        $user2 = Factory(User::class)->create();
        $task = Factory(Task::class)->create(['user_id'=>$user->id]);

        //when
        $this->actingAs($user);
        $response = $this->post(route('invite',['task' =>$task,'invited' =>$user2]));

        //then
        $response->assertStatus(200);


        $this->assertDatabaseHas('invitations',[
            'inviting_id'=> $user->id,
            'invited_id'=>$user2->id,
            'task_id' => $task->id
        ]);
    }

    /** @test */
    public function acceptInvitationTest()
    {
        //given
        $invitation = Factory(Invitation::class)->create();
        $user = User::find($invitation->invited_id);

        //when
        $this->actingAs($user);
        $response = $this->post(route('accept-invitation',['invitation' => $invitation]));

        //then
        $response->assertStatus(200);
        $this->assertTrue($user->followedTasks()->get()->contains($invitation->task_id));
        $this->assertDatabaseMissing('invitations',['id' => $invitation->id]);
    }
    /** @test */
    public function rejectInvitationTest()
    {
        //given
        $invitation = Factory(Invitation::class)->create();
        $user = User::find($invitation->invited_id);

        //when
        $this->actingAs($user);
        $response = $this->post(route('reject-invitation',['invitation' => $invitation]));

        //then
        $response->assertStatus(200);
        $this->assertTrue(!($user->followedTasks()->get()->contains($invitation->task_id)));
        $this->assertDatabaseMissing('invitations',['id' => $invitation->id]);
    }

    /** @test */
    public function changePasswordTest()
    {
        //given
        $user = Factory(User::class)->create();

        //when
        $this->actingAs($user);
        $response = $this->post(route('change-password',['old_password' => 'secret', 'new_password' => 'newsecret', 'new_password_confirmation' => 'newsecret']));
        //then
        $response->assertStatus(200);
        $this->assertTrue(password_verify('newsecret',User::find($user->id)->password));
    }
    /** @test */
    public function avatarTest()
    {
        //given
        $user = Factory(User::class)->create();

        //when
        $this->actingAs($user);
        $response = $this->post(route('upload-avatar'),['avatar'=> UploadedFile::fake()->image('my_avatar.png') ]);

        //then
        $response->assertStatus(200);
        $this->assertFileExists(storage_path().'/app/avatars/avatar.'.$user->id.'.png');
        Storage::delete('/avatars/avatar.'.$user->id.'.png');
    }

}
