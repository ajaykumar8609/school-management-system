<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\FeePayment;
use App\Models\Notice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        $totalStudents = Student::count();
        $activeStudents = Student::where('status', true)->count();
        $newAdmissions = Student::where('admission_date', '>=', $monthStart)->count();

        $studentsWithDueFees = 0;
        foreach (Student::with(['fees', 'feePayments'])->get() as $s) {
            $total = $s->fees->sum('final_amount') ?: 0;
            $paid = $s->feePayments->sum('amount');
            if ($total > 0 && $paid < $total) $studentsWithDueFees++;
        }

        $feeCollected = FeePayment::where('payment_date', '>=', $monthStart)->sum('amount');

        $presentToday = Attendance::where('date', $today)->where('status', 'Present')->distinct('student_id')->count('student_id');
        $absentToday = Attendance::where('date', $today)->whereIn('status', ['Absent', 'Late'])->distinct('student_id')->count('student_id');

        $recentAdmissions = Student::with(['schoolClass', 'section'])
            ->orderByDesc('admission_date')
            ->limit(5)
            ->get();

        $recentNotices = Notice::where('is_active', true)
            ->orderByDesc('notice_date')
            ->limit(5)
            ->get();

        $attendanceByDay = [];
        foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $i => $day) {
            $date = Carbon::now()->startOfWeek()->addDays($i);
            $attendanceByDay[$day] = [
                'present' => Attendance::where('date', $date)->where('status', 'Present')->count(),
                'absent' => Attendance::where('date', $date)->whereIn('status', ['Absent', 'Late'])->count(),
            ];
        }

        $classDistribution = Student::selectRaw('class_id, count(*) as count')
            ->groupBy('class_id')
            ->with('schoolClass')
            ->get()
            ->map(fn ($s) => ['label' => $s->schoolClass?->class_name ?? 'N/A', 'count' => $s->count])
            ->toArray();

        return view('dashboard.index', compact(
            'totalStudents', 'activeStudents', 'newAdmissions', 'studentsWithDueFees', 'feeCollected',
            'presentToday', 'absentToday', 'recentAdmissions', 'recentNotices',
            'attendanceByDay', 'classDistribution'
        ));
    }
}
