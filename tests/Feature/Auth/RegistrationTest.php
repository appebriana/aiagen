<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => 'New-pass123!',
        'password_confirmation' => 'New-pass123!',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('pengguna.dashboard', absolute: false));
});
