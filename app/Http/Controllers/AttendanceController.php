<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Section;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::orderByRaw("FIELD(class_name, 'Nursery', 'LKG', 'UKG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12')")->get();
        $sections = Section::onlyABC()->with('schoolClass')->orderBy('section_name')->get();

        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));

        $students = collect();
        if ($classId && $sectionId) {
            $students = Student::with(['schoolClass', 'section'])
                ->where('class_id', $classId)
                ->where('section_id', $sectionId)
                ->where('status', true)
                ->orderBy('roll_no')
                ->get();

            $existing = Attendance::whereIn('student_id', $students->pluck('id'))
                ->where('date', $date)
                ->get()
                ->keyBy('student_id');

            $students->each(function ($s) use ($existing, $date) {
                $s->attendance_status = $existing[$s->id]->status ?? null;
                $s->attendance_remarks = $existing[$s->id]->remarks ?? null;
            });
        }

        return view('attendance.index', compact('classes', 'sections', 'students', 'date', 'classId', 'sectionId'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:Present,Absent,Late',
            'attendance.*.remarks' => 'nullable|string|max:255',
        ]);

        foreach ($request->attendance as $a) {
            Attendance::updateOrCreate(
                ['student_id' => $a['student_id'], 'date' => $request->date],
                [
                    'class_id' => $request->class_id,
                    'section_id' => $request->section_id,
                    'status' => $a['status'],
                    'remarks' => $a['remarks'] ?? null,
                ]
            );
        }

        return redirect()->route('attendance.index', [
            'class_id' => $request->class_id,
            'section_id' => $request->section_id,
            'date' => $request->date,
        ])->with('success', 'Attendance saved successfully.');
    }
}
