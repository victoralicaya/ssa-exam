<?php

namespace Tests\Unit;

use App\Events\UserSaved;
use App\Listeners\SaveUserBackgroundInformation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaveUserBackgroundInformationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_saves_user_details_on_user_saved_event()
    {
        $user = User::factory()->create([
            'prefixname' => 'Mr.',
            'firstname' => 'John',
            'middlename' => 'Doe',
            'lastname' => 'Smith',
            'suffixname' => 'Jr.',
            'photo' => 'http://example.com/avatar.png',
            'password' => 'password'
        ]);

        $event = new UserSaved($user);

        $listener = new SaveUserBackgroundInformation();
        $listener->handle($event);

        $this->assertDatabaseHas('details', [
            'key' => 'Full Name',
            'value' => $user->fullname,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('details', [
            'key' => 'Middle Initial',
            'value' => $user->middleinitial,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('details', [
            'key' => 'Avatar',
            'value' => $user->avatar,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('details', [
            'key' => 'Gender',
            'value' => $user->prefixname == 'Mr.' ? 'Male' : (($user->prefixname == 'Ms.' || $user->prefixname == 'Mrs.' ? 'Female' : null)),
            'user_id' => $user->id,
        ]);
    }
}
