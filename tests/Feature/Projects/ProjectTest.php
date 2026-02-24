<?php

declare(strict_types=1);

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test-token')->plainTextToken;
});

test('can list projects', function (): void {
    Project::factory()->count(5)->create();

    $response = $this->getJson('/api/v1/projects', [
        'Authorization' => "Bearer $this->token",
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
            'meta' => [
                'pagination' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                    'from',
                    'to',
                ],
            ],
        ]);

    expect($response->json('meta.pagination.total'))->toBe(5);
});

test('can create project', function (): void {
    $payload = [
        'name' => 'New Project',
        'description' => 'Project description here.',
        'status' => ProjectStatus::PLANNING->value,
    ];

    $response = $this->postJson('/api/v1/projects', $payload, [
        'Authorization' => "Bearer $this->token",
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'name',
                'description',
                'status',
                'status_label',
            ],
        ]);

    $this->assertDatabaseHas('projects', [
        'name' => 'New Project',
        'description' => 'Project description here.',
        'status' => ProjectStatus::PLANNING->value,
    ]);
});

test('can show project', function (): void {
    $project = Project::factory()->create();

    $response = $this->getJson("/api/v1/projects/$project->id", [
        'Authorization' => "Bearer $this->token",
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'name',
                'description',
                'status',
            ],
        ]);
});

test('can update project', function (): void {
    $project = Project::factory()->create();

    $payload = [
        'name' => 'Updated Project Name',
        'description' => 'Updated description.',
        'status' => ProjectStatus::IN_PROGRESS->value,
    ];

    $response = $this->putJson("/api/v1/projects/$project->id", $payload, [
        'Authorization' => "Bearer $this->token",
    ]);

    $response->assertStatus(200);

    expect($response->json('data.name'))->toBe('Updated Project Name')
        ->and($response->json('data.status'))->toBe(ProjectStatus::IN_PROGRESS->value);
});

test('can delete project', function (): void {
    $project = Project::factory()->create();

    $response = $this->deleteJson("/api/v1/projects/$project->id", [], [
        'Authorization' => "Bearer $this->token",
    ]);

    $response->assertStatus(200);

    $this->assertSoftDeleted('projects', [
        'id' => $project->id,
    ]);
});

test('can filter projects by status', function (): void {
    Project::factory()->count(3)->create(['status' => ProjectStatus::PLANNING]);
    Project::factory()->count(2)->create(['status' => ProjectStatus::IN_PROGRESS]);

    $response = $this->getJson('/api/v1/projects?status=planning', [
        'Authorization' => "Bearer $this->token",
    ]);

    $response->assertStatus(200);

    expect($response->json('meta.pagination.total'))->toBe(3);
});

test('can search projects', function (): void {
    Project::factory()->create(['name' => 'Alpha Release']);
    Project::factory()->create(['name' => 'Beta Release']);
    Project::factory()->create(['name' => 'Gamma Deploy']);

    $response = $this->getJson('/api/v1/projects?search=Release', [
        'Authorization' => "Bearer $this->token",
    ]);

    $response->assertStatus(200);

    expect($response->json('meta.pagination.total'))->toBe(2);
});
