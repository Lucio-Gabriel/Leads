<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeadResource;
use App\Models\Lead;
use App\StatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class LeadController extends Controller
{
    public function index()
    {
        return LeadResource::collection(Lead::latest()->get());
    }

    /**
     * Armazena um novo lead e o envia para o n8n.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|unique:leads,email',
            'phone'     => 'required|string|max:20',
            'status'    => [new Enum(StatusEnum::class)],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        // Adiciona campos que são definidos pelo servidor
        $validatedData['registration_date'] = now();

        $lead = Lead::create($validatedData);

        if ($lead) {
            // Envia os dados do lead recém-criado para o n8n
            $this->sendToN8n($lead->toArray());

            // Retorna o recurso criado com o status 201 (Created)
            return (new LeadResource($lead))
                ->response()
                ->setStatusCode(201);
        }

        return response()->json(['message' => 'Houve algum erro ao criar o lead'], 500);
    }

    /**
     * Envia os dados para o webhook do n8n.
     *
     * @param array $leadData
     * @return void
     */
    private function sendToN8n(array $leadData): void
    {
        // Sua URL do webhook do n8n
        $webhookUrl = 'https://gabdevtest.app.n8n.cloud/webhook-test/leads';

        try {
            $response = Http::post($webhookUrl, $leadData);

            // Log para depuração
            if ($response->successful()) {
                Log::info('Lead enviado para o n8n com sucesso.', ['lead_id' => $leadData['id'] ?? null]);
            } else {
                Log::error('Falha ao enviar lead para o n8n.', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'lead' => $leadData,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exceção ao enviar lead para o n8n: ' . $e->getMessage(), [
                'lead' => $leadData,
            ]);
        }
    }

    public function show(Lead $lead)
    {
        return new LeadResource($lead);
    }

    public function update(Request $request, Lead $lead)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'sometimes|required|string|max:255',
            'email'     => ['sometimes', 'required', 'email', Rule::unique('leads')->ignore($lead->id)],
            'phone'     => 'sometimes|required|string|max:20',
            'status'    => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updated = $lead->update($validator->validated());

        if ($updated) {
            // Opcional: você também pode enviar para o n8n na atualização
            // $this->sendToN8n($lead->fresh()->toArray());

            return new LeadResource($lead->fresh());
        }

        return response()->json(['message' => 'Houve algum erro ao atualizar o lead'], 500);
    }

    public function destroy(Lead $lead)
    {
        $deleted = $lead->delete();

        if ($deleted) {
            return response()->json(null, 204);
        }

        return response()->json(['message' => 'Houve algum erro ao deletar o lead'], 500);
    }
}
