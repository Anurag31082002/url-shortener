<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function test_public_registration_is_disabled_and_accounts_are_created_by_invitation(): void
    {
        $this->get('/register')->assertNotFound();

        $this->post('/register', [
            'name' => 'Uninvited User',
            'email' => 'uninvited@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertNotFound();
    }
}
