<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Group;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GroupController extends Controller
{
    public function index()
    {
        try {
            $data = Group::with("school")->orderBy("id","desc")->paginate(10);
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

                return Inertia::render('ManagerGroups', [
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
                'school_id' => 'required',
            ]);

            $group = new Group();
            $group->name = $request->input('name');
            $group->school_id = $request->input('school_id');
            $group->save();

            return response()->json(['success' => true, 'message' => 'Turma adicionada com sucesso.'], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id) {
        try {

            $validatedData = $request->validate([
                'name' => 'required|string',
                'school_id' => 'required',
            ]);

            $group = Group::find($id);

            if (!$group) {
                return response()->json(['message' => 'Turma não encontrada na base de dados'], 404);
            }

            $group->update($validatedData);

            return response()->json(['success' => true, 'message' => 'Turma atualizada com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id) {
        try {
            $group = Group::find($id);

            if (!$group) {
                return response()->json(['message' => 'Turma não encontrada na base de dados.'], 404);
            }

            $group->delete();

            return response()->json(['success' => true, 'message' => 'Turma deletada com sucesso.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
