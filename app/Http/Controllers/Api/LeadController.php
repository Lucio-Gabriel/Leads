<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    public function index()
    {
        return Lead::get();
    }

    public function create()
    {
        $validator = Validator::make(request()->all(), [
            'full_name'         => 'required|string|max:255',
            'email'             => 'required|email|unique:leads,email',
            'phone'             => 'required|string|max:20',
            'status'            => 'required',
            'registration_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $created = Lead::create($validator->validate());

        if ($created) {
            return response()->json('Lead criado com sucesso', 200);
        }

        return response()->json('Houve algum erro ao criar o lead', 400);
    }

    public function show(Lead $lead)
    {
        return $lead;
    }

    public function update(Request $request, Lead $lead)
    {
        $validator = Validator::make(request()->all(), [
            'full_name'         => 'required|string|max:255',
            'email'             => 'required|email|unique:leads,email',
            'phone'             => 'required|string|max:20',
            'status'            => 'required',
            'registration_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validate();

        $updated = $lead->update([
            'full_name'         => $validated['full_name'],
            'email'             => $validated['email'],
            'phone'             => $validated['phone'],
            'status'            => $validated['status'],
            'registration_date' => $validated['registration_date'],
        ]);

        if ($updated) {
            return response()->json('Lead atualizado com sucesso', 200);
        }

        return response()->json('Houve algum erro ao atualizar o lead', 400);
    }

    public function destroy(Lead $lead)
    {
        $deleted = $lead->delete();

        if ($deleted) {
            return response()->json('Lead deletado com sucesso', 200);
        }

        return response()->json('Houve algum erro ao deletar o lead', 400);
    }
}
