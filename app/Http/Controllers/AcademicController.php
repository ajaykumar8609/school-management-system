<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Http\Request;

class AcademicController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::orderByRaw("FIELD(class_name, 'Nursery', 'LKG', 'UKG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12')")->get();
        $exams = Exam::orderBy('start_date', 'desc')->get();

        $classId = $request->get('class_id');
        $examId = $request->get('exam_id');
        $subjectId = $request->get('subject_id');

        $students = collect();
        $subjects = collect();
        $marks = collect();

        if ($classId && $examId && $subjectId) {
            $subject = Subject::find($subjectId);
            if ($subject && $subject->class_id == $classId) {
                $subjects = Subject::where('class_id', $classId)->orderBy('subject_name')->get();
                $students = Student::with(['schoolClass', 'section'])
                    ->where('class_id', $classId)
                    ->where('status', true)
                    ->orderBy('roll_no')
                    ->get();

                $existing = Mark::where('exam_id', $examId)
                    ->where('subject_id', $subjectId)
                    ->whereIn('student_id', $students->pluck('id'))
                    ->get()
                    ->keyBy('student_id');

                $students->each(function ($s) use ($existing, $examId, $subjectId) {
                    $m = $existing[$s->id] ?? null;
                    $s->marks_obtained = $m ? $m->marks_obtained : null;
                    $s->total_marks = $m ? $m->total_marks : 100;
                    $s->grade = $m ? $m->grade : null;
                });
            }
        } elseif ($classId) {
            $subjects = Subject::where('class_id', $classId)->orderBy('subject_name')->get();
        }

        return view('academic.index', compact(
            'classes', 'exams', 'subjects', 'students',
            'classId', 'examId', 'subjectId'
        ));
    }

    public function saveMarks(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|exists:students,id',
            'marks.*.marks_obtained' => 'required|numeric|min:0',
            'marks.*.total_marks' => 'required|numeric|min:0',
        ]);

        foreach ($request->marks as $m) {
            $pct = $m['total_marks'] > 0 ? ($m['marks_obtained'] / $m['total_marks']) * 100 : 0;
            $grade = $pct >= 90 ? 'A+' : ($pct >= 80 ? 'A' : ($pct >= 70 ? 'B' : ($pct >= 60 ? 'C' : ($pct >= 50 ? 'D' : 'F'))));

            Mark::updateOrCreate(
                [
                    'student_id' => $m['student_id'],
                    'exam_id' => $request->exam_id,
                    'subject_id' => $request->subject_id,
                ],
                [
                    'marks_obtained' => $m['marks_obtained'],
                    'total_marks' => $m['total_marks'],
                    'grade' => $grade,
                ]
            );
        }

        return redirect()->back()->with('success', 'Marks saved successfully.');
    }
}
