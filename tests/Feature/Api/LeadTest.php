<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\StatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

it('should be able return a list of leads', function () {
    Lead::factory()->count(3)->create();

    $response = $this->getJson('/api/leads');

    $response
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('should be able successfully create a new lead and notify n8n', function () {
    Http::fake();

    $leadData = [
        'full_name' => 'Gabriel Developer',
        'email'     => 'gabriel@example.com',
        'phone'     => '99999-8888',
        'status'    => StatusEnum::novo,
    ];

    $response = $this->postJson('/api/leads', $leadData);

    $response
        ->assertStatus(201)
        ->assertJsonFragment([
            'full_name' => 'Gabriel Developer',
            'email' => 'gabriel@example.com',
            'phone' => '99999-8888',
            'status' => StatusEnum::novo,
        ]);

    Http::assertSent(function ($request) {
        return $request->url() == 'https://gabdevtest.app.n8n.cloud/webhook-test/leads' &&
               $request['email'] == 'gabriel@example.com';
    });
});

it('should be able return a validation error when attempting to create a lead with invalid data', function () {
    $leadData = [
        'full_name' => 'Invalid Lead',
        'phone'     => '12345',
    ];

    $response = $this->postJson('/api/leads', $leadData);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors('email');
});

it('should be able to display a specific lead', function () {
    $lead = Lead::factory()->create([
        'full_name' => 'Gabriel Developer',
        'email'     => 'gabriel@example.com',
        'phone'     => '99999-8888',
        'status'    => StatusEnum::novo,
    ]);

    $response = $this->getJson("/api/leads/{$lead->id}");

    $response
        ->assertStatus(200)
        ->assertJsonFragment(['id' => $lead->id]);
});

it('should be able successfully update a lead', function () {
    $lead = Lead::factory()->create();
    $updateData = [
        'full_name' => 'Nome Atualizado',
        'status' => StatusEnum::novo,
    ];

    $response = $this->putJson("/api/leads/{$lead->id}", $updateData);

    $response
        ->assertStatus(200)
        ->assertJsonFragment(['full_name' => 'Nome Atualizado']);
});

it('should be able to successfully delete a lead', function () {
    $lead = Lead::factory()->create();

    $response = $this->deleteJson("/api/leads/{$lead->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('leads', ['id' => $lead->id]);
});

it('should be possible to log an error if the send to n8n fails', function () {
    Http::fake(['*' => Http::response('Server Error', 500)]);
    Log::shouldReceive('error')->once()->with(
        'Falha ao enviar lead para o n8n.',
        \Mockery::any()
    );

    $leadData = Lead::factory()->make()->toArray();

    $response = $this->postJson('/api/leads', $leadData);

    $response->assertStatus(201);
    $this->assertDatabaseHas('leads', ['email' => $leadData['email']]);
});

it('should be able return 404 when trying to retrieve a lead that does not exist', function () {
    $response = $this->getJson('/api/leads/999');

    $response->assertStatus(404);
});
