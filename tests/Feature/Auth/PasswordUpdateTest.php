<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('password can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/pengguna/profile')
        ->put('/password', [
            'current_password' => 'password',
            'password' => 'New-pass123!',
            'password_confirmation' => 'New-pass123!',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/pengguna/profile');

    $this->assertTrue(Hash::check('New-pass123!', $user->refresh()->password));
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/pengguna/profile')
        ->put('/password', [
            'current_password' => 'wrong-password',
            'password' => 'New-pass123!',
            'password_confirmation' => 'New-pass123!',
        ]);

    $response
        ->assertSessionHasErrorsIn('updatePassword', 'current_password')
        ->assertRedirect('/pengguna/profile');
});
