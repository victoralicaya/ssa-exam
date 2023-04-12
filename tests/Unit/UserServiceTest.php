<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function it_can_return_a_paginated_list_of_users()
    {
        User::factory()->count(15)->create();

        $result = (new UserService)->usersList();

        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);

        $this->assertEquals(10, $result->total());

        $this->assertEquals(10, $result->perPage());

        $this->assertEquals(1, $result->currentPage());

        $this->assertArrayHasKey('links', $result->items());
    }

    /**
     * @test
     * @return void
     */
    public function it_can_store_a_user_to_database()
    {
        $userData = [
            'prefixname' => 'Mr.',
            'firstname' => 'John',
            'middlename' => 'Doe',
            'lastname' => 'Smith',
            'suffixname' => 'Jr.',
            'username' => 'johndoe',
            'photo' => 'photo.jpg',
            'email' => 'johndoe@example.com',
            'password' => 'password',
        ];

        $userService = new UserService();
        $user = $userService->addUser($userData);

        $this->assertDatabaseHas('users', $user->toArray());

        $this->assertNotEquals('password', $user->password);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_find_and_return_an_existing_user()
    {
        $userData = User::factory()->create();
        $userService = new UserService();

        $user = $userService->findUser($userData->id);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userData->id, $user->id);
        $this->assertEquals($userData->username, $user->username);
        $this->assertEquals($userData->email, $user->email);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_update_an_existing_user()
    {
        $user = User::factory()->create();

        $updatedData = [
            'prefixname' => 'Mrs.',
            'firstname' => 'Jane',
            'middlename' => 'Smith',
            'lastname' => 'Doe',
            'suffixname' => 'PhD',
            'username' => 'janesmith',
            'email' => 'johndoe@example.com',
            'password' => 'newpassword',
            'photo' => '',
        ];

        $userService = new UserService();
        $updatedUser = $userService->updateUser($user->id, $updatedData);

        $this->assertDatabaseHas('users', $updatedUser->toArray());

        $this->assertTrue(Hash::check('newpassword', $updatedUser->password));
    }

    /**
     * @test
     * @return void
     */
    public function it_can_soft_delete_an_existing_user()
    {
        $user = User::factory()->create();

        $userService = new UserService();
        $result = $userService->deleteUser($user->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_return_a_paginated_list_of_trashed_users()
    {
        User::factory()->count(15)->create()->each(function ($user) {
            $user->delete();
        });

        $result = (new UserService())->listTrashed();

        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);

        $this->assertEquals(10, $result->total());

        $this->assertEquals(10, $result->perPage());

        $this->assertEquals(1, $result->currentPage());

        foreach ($result->items()['data'] as $item) {
            $this->assertNotNull($item['deleted_at']);
        }
    }

    /**
     * @test
     * @return void
     */
    public function it_can_restore_a_soft_deleted_user()
    {
        $user = User::factory()->create();
        $user->delete();

        $userService = new UserService();
        $restored = $userService->restore($user->id);

        $this->assertTrue($restored);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_permanently_delete_a_soft_deleted_user()
    {
        $user = User::factory()->create();

        $userService = new UserService();
        $userService->deleteUser($user->id);

        $result = $userService->forceDeleteUser($user->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_upload_a_file()
    {
        $file = UploadedFile::fake()->image('test.jpg');

        $userService = new UserService();

        $image = $userService->uploadFile($file);

        Storage::disk('public')->assertExists('images/'.basename($image));
    }


}
