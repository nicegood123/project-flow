<?php

use App\Models\Project;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('it lists projects as json', function () {
    $projects = Project::factory()->count(2)->create();

    $this->getJson('/api/projects')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $projects->first()->id);
});

test('it searches projects by client or project name', function () {
    $clientMatch = Project::factory()->create([
        'client_name' => 'Acme Studio',
        'project_name' => 'Brand guide',
    ]);
    $projectMatch = Project::factory()->create([
        'client_name' => 'Northwind',
        'project_name' => 'Acme launch',
    ]);
    Project::factory()->create([
        'client_name' => 'Contoso',
        'project_name' => 'Website refresh',
    ]);

    $this->getJson('/api/projects?search=acme')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $clientMatch->id)
        ->assertJsonPath('data.1.id', $projectMatch->id);
});

test('it filters projects by status and priority', function () {
    $matchingProject = Project::factory()->create([
        'status' => 'In Progress',
        'priority' => 'High',
    ]);
    Project::factory()->create(['status' => 'Planning', 'priority' => 'High']);
    Project::factory()->create(['status' => 'In Progress', 'priority' => 'Low']);

    $this->getJson('/api/projects?status=In%20Progress&priority=High')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $matchingProject->id);
});

test('it rejects invalid project filter values', function () {
    $this->getJson('/api/projects?status=Unknown&priority=Urgent')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['status', 'priority']);
});

test('it sorts projects by an approved field in either direction', function () {
    $laterProject = Project::factory()->create(['due_date' => '2026-12-01']);
    $earlierProject = Project::factory()->create(['due_date' => '2026-01-01']);

    $this->getJson('/api/projects?sort=due_date&direction=asc')
        ->assertOk()
        ->assertJsonPath('data.0.id', $earlierProject->id)
        ->assertJsonPath('data.1.id', $laterProject->id);

    $this->getJson('/api/projects?sort=due_date&direction=desc')
        ->assertOk()
        ->assertJsonPath('data.0.id', $laterProject->id)
        ->assertJsonPath('data.1.id', $earlierProject->id);
});

test('it rejects invalid project sort values', function () {
    $this->getJson('/api/projects?sort=created_at&direction=sideways')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['sort', 'direction']);
});

test('it shows a project as json', function () {
    $project = Project::factory()->create();

    $this->getJson("/api/projects/{$project->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $project->id);
});

test('it creates a project', function () {
    $attributes = Project::factory()->make()->toArray();

    $this->postJson('/api/projects', $attributes)
        ->assertCreated()
        ->assertJsonPath('data.client_name', $attributes['client_name']);

    $this->assertDatabaseHas('projects', [
        'client_name' => $attributes['client_name'],
        'project_name' => $attributes['project_name'],
    ]);
});

test('it returns json validation errors for an invalid project', function () {
    $this->postJson('/api/projects', [
        'status' => 'Unknown',
        'priority' => 'Urgent',
        'start_date' => '2026-09-03',
        'due_date' => '2026-09-02',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['client_name', 'project_name', 'status', 'priority', 'due_date']);
});

test('it updates a project', function () {
    $project = Project::factory()->create();

    $this->putJson("/api/projects/{$project->id}", [
        'status' => 'Completed',
        'priority' => 'High',
    ])
        ->assertOk()
        ->assertJsonPath('data.status', 'Completed')
        ->assertJsonPath('data.priority', 'High');

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'status' => 'Completed',
        'priority' => 'High',
    ]);
});

test('it validates invalid project updates', function () {
    $project = Project::factory()->create();

    $this->putJson("/api/projects/{$project->id}", [
        'status' => 'Unknown',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
});

test('it validates a due date before the persisted start date', function () {
    $project = Project::factory()->create([
        'start_date' => '2026-09-10',
        'due_date' => '2026-09-20',
    ]);

    $this->putJson("/api/projects/{$project->id}", [
        'due_date' => '2026-09-09',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['due_date']);
});

test('it returns a json 404 when updating a missing project', function () {
    $this->putJson('/api/projects/999', [
        'status' => 'Completed',
    ])
        ->assertNotFound()
        ->assertJsonStructure(['message']);
});

test('it deletes a project', function () {
    $project = Project::factory()->create();

    $this->deleteJson("/api/projects/{$project->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('projects', ['id' => $project->id]);
});

test('it returns a json 404 when deleting a missing project', function () {
    $this->deleteJson('/api/projects/999')
        ->assertNotFound()
        ->assertJsonStructure(['message']);
});

test('it returns a json 404 for a missing project', function () {
    $this->getJson('/api/projects/999')
        ->assertNotFound()
        ->assertJsonStructure(['message']);
});
