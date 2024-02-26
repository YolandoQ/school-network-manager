<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SchoolController extends Controller
{
    public function index()
    {
        try {

            $data = School::orderBy("id","desc")->paginate(10);

            $user = auth()->user();

            if ($user) {
                $schools = School::with('groups')->get();
                $schools = $schools->map(function ($school) {
                    return [
                        'id' => $school->id,
                        'name' => $school->name,
                        'groups' => $school->groups->map(function ($group) {
                            return [
                                'id' => $group->id,
                                'name' => $group->name,
                            ];
                        })->toArray(),
                    ];
                });

                return Inertia::render('ManagerSchools', [
                    'data' => $data,
                    'schools' => $schools
                ]);
            }

            return Inertia::render('Welcome');
        } catch (\Exception $th) {
            return Inertia::render('Welcome');
        }
    }

    public function create(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
            ]);

            $school = new School();
            $school->name = $request->input('name');
            $school->save();

            return response()->json(['success' => true, 'message' => 'Escola adicionada com sucesso.'], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id) {
        try {

            $validatedData = $request->validate([
                'name' => 'required|string',
            ]);

            $school = School::find($id);

            if (!$school) {
                return response()->json(['message' => 'Escola não encontrada na base de dados'], 404);
            }

            $school->update($validatedData);

            return response()->json(['success' => true, 'message' => 'Escola atualizada com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id) {
        try {

            $school = School::find($id);

            if (!$school) {
                return response()->json(['message' => 'Escola não encontrada na base de dados.'], 404);
            }

            $school->delete();

            return response()->json(['success' => true, 'message' => 'Escola deletada com sucesso.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
