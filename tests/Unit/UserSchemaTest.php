<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserSchemaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_user_table_has_columns()
    {
        $columns = [
            'id',
            'prefixname',
            'firstname',
            'middlename',
            'lastname',
            'suffixname',
            'username',
            'email',
            'password',
            'type',
            'photo',
            'remember_token',
            'email_verified_at',
            'created_at',
            'updated_at',
            'deleted_at'
        ];

        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasColumns('users', $columns));
    }
}
