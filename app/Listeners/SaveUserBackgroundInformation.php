<?php

namespace App\Listeners;

use App\Events\UserSaved;
use App\Models\Detail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SaveUserBackgroundInformation
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UserSaved $event)
    {
        $user = $event->user;

        $details = [
            [
                'key' => 'Full Name',
                'value' => $user->fullname,
                'user_id' => $user->id,
            ],
            [
                'key' => 'Middle Initial',
                'value' => $user->middleinitial,
                'user_id' => $user->id,
            ],
            [
                'key' => 'Avatar',
                'value' => $user->avatar,
                'user_id' => $user->id,
            ],
            [
                'key' => 'Gender',
                'value' => $user->prefixname == 'Mr.' ? 'Male' : (($user->prefixname == 'Ms.' || $user->prefixname == 'Mrs.' ? 'Female' : null)),
                'user_id' => $user->id,
            ],

        ];

        foreach ($details as $detail) {
            Detail::updateOrCreate(['key' => $detail['key'], 'user_id' => $detail['user_id']], $detail);
        }
    }
}
