<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Mark;
use App\Models\Fee;
use App\Models\FeePayment;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['schoolClass', 'section']);

        if ($request->filled('search')) {
            $s = $request->search;
            $searchTerm = '%' . $s . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', $searchTerm)
                    ->orWhere('last_name', 'like', $searchTerm)
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$searchTerm])
                    ->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", [$searchTerm])
                    ->orWhere('roll_no', 'like', $searchTerm)
                    ->orWhere('parent_contact', 'like', $searchTerm)
                    ->orWhere('admission_no', 'like', $searchTerm);
            });
        }
        if ($request->filled('class_id')) $query->where('class_id', $request->class_id);
        if ($request->filled('section_id')) $query->where('section_id', $request->section_id);
        if ($request->filled('status')) {
            if ($request->status === 'active') $query->where('status', true);
            elseif ($request->status === 'inactive') $query->where('status', false);
        }

        $query->with(['fees', 'feePayments']);
        $students = $query->orderBy('first_name')->paginate($request->get('per_page', 15));

        $classes = SchoolClass::orderByClassOrder()->get();
        $sections = Section::onlyABC()->with('schoolClass')->orderBy('section_name')->get();
        return view('students.index', compact('students', 'classes', 'sections'));
    }

    public function sectionsByClass($classId)
    {
        $sections = Section::onlyABC()
            ->where('class_id', $classId)
            ->orderBy('section_name')
            ->get(['id', 'section_name'])
            ->map(fn ($s) => ['id' => $s->id, 'name' => $s->section_name])
            ->values();
        return response()->json($sections);
    }

    public function create()
    {
        $classes = SchoolClass::orderByClassOrder()->get();
        $sections = Section::onlyABC()->with('schoolClass')->orderBy('section_name')->get();
        return view('students.create', compact('classes', 'sections'));
    }

    public function store(Request $request)
    {
        $valid = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'required|in:Male,Female,Other',
            'blood_group' => 'nullable|string|max:20',
            'category' => 'nullable|string|max:50',
            'admission_date' => 'nullable|date',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'roll_no' => 'nullable|string|max:50',
            'admission_no' => 'required|string|unique:students,admission_no',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'parent_contact' => 'nullable|string|max:20',
            'alt_contact' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'current_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $valid['status'] = $request->boolean('status', true);

        if ($request->hasFile('photo')) {
            $valid['photo'] = $request->file('photo')->store('students', 'public');
        }

        Student::create($valid);
        return redirect()->route('students.index')->with('success', 'Student added successfully.');
    }

    public function show(Student $student)
    {
        $student->load(['schoolClass', 'section', 'marks.exam', 'marks.subject', 'attendance', 'fees', 'feePayments', 'documents']);
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $classes = SchoolClass::orderByClassOrder()->get();
        $sections = Section::onlyABC()->with('schoolClass')->orderBy('section_name')->get();
        return view('students.edit', compact('student', 'classes', 'sections'));
    }

    public function update(Request $request, Student $student)
    {
        $valid = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'required|in:Male,Female,Other',
            'blood_group' => 'nullable|string|max:20',
            'category' => 'nullable|string|max:50',
            'admission_date' => 'nullable|date',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'roll_no' => 'nullable|string|max:50',
            'admission_no' => 'required|string|unique:students,admission_no,' . $student->id,
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'parent_contact' => 'nullable|string|max:20',
            'alt_contact' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'current_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $valid['status'] = $request->boolean('status', true);

        if ($request->hasFile('photo')) {
            if ($student->photo) Storage::disk('public')->delete($student->photo);
            $valid['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student->update($valid);
        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        if ($student->photo) Storage::disk('public')->delete($student->photo);
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }

    public function academic(Student $student)
    {
        $student->load(['marks.exam', 'marks.subject', 'schoolClass', 'section']);
        $exams = Exam::orderBy('start_date', 'desc')->get();
        return view('students.academic', compact('student', 'exams'));
    }

    public function feeReport(Student $student)
    {
        $student->load(['feePayments', 'fees', 'schoolClass', 'section']);
        $totalFee = $student->fees->sum('final_amount') ?: 0;
        $paid = $student->feePayments->sum('amount');
        return view('students.fee-report', compact('student', 'totalFee', 'paid'));
    }
}
