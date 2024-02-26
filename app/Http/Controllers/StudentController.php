<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentLog;
use App\Models\School;
use App\Models\Group;
use Illuminate\Http\Request;
use Inertia\Inertia;


class StudentController extends Controller
{
    public function index()
    {
        try {

            $data = Student::with(["group.school", "logs"])->orderBy("id","desc")->paginate(10);

            $data->each(function ($student) {
                $student->logs->each(function ($log) {
                    $log->group_name = "Turma não existe/Deletado";
                    $log->school_name = "Escola não existe/Deletada";

                    $group = Group::find($log->previous_group_id);
                    if ($group) {
                        $log->group_name = $group->name;

                        $school = School::find($group->school_id);
                        if ($school) {
                            $log->school_name = $school->name;
                        }
                    }
                });
            });
            
            $user = auth()->user();

            if (!$user) {
                return Inertia::render('Welcome', [
                    'data' => $data,
                    'canLogin' => true,
                    'canRegister' => true,
                ]);
            }

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

            return Inertia::render('Manager', [
                'data' => $data,
                'schools' => $schools
            ]);
        } catch (\Exception $th) {
            dd($th->getMessage());
            return Inertia::render('Welcome');
        }

    }

    public function create(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'group_id' => 'required',
            ]);

            $student = new Student();
            $student->name = $request->input('name');
            $student->email = $request->input('email');
            $student->group_id = $request->input('group_id');
            $student->save();

            return response()->json(['success' => true, 'message' => 'Estudante adicionado com sucesso.'], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id) {
        try {

            $validatedData = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'group_id' => 'required|numeric',
            ]);

            $student = Student::find($id);

            if (!$student) {
                return response()->json(['message' => 'Estudante não encontrado na base de dados'], 404);
            }

            if($student->group_id != $validatedData["group_id"]) {
                $log = StudentLog::create([
                    'student_id' => $student->id,
                    'previous_group_id' => $student->group_id,
                ]);
            }

            $student->update($validatedData);

            return response()->json(['success' => true, 'message' => 'Estudante atualizado com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id) {
        try {
            $student = Student::find($id);

            if (!$student) {
                return response()->json(['message' => 'Estudante não encontrado na base de dados.'], 404);
            }

            $student->delete();

            return response()->json(['success' => true, 'message' => 'Estudante deletado com sucesso.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
