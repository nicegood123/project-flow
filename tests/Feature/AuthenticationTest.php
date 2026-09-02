<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('a visitor can register an account', function () {
    $this->post('/register', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])
        ->assertRedirect('/projects');

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ]);
});

test('a user can log in and log out', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'remember' => true,
    ])
        ->assertRedirect('/projects');

    $this->assertAuthenticatedAs($user);

    $this->post('/logout')
        ->assertRedirect('/login');

    $this->assertGuest();
});

test('unauthenticated users cannot access project API endpoints', function () {
    $project = Project::factory()->create();

    $this->getJson('/api/projects')->assertUnauthorized();
    $this->postJson('/api/projects', [])->assertUnauthorized();
    $this->putJson("/api/projects/{$project->id}", [])->assertUnauthorized();
    $this->deleteJson("/api/projects/{$project->id}")->assertUnauthorized();
});
