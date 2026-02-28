<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Notice;
use App\Models\Fee;
use App\Models\FeePayment;
use App\Models\Attendance;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        $classes = ['Nursery', 'LKG', 'UKG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
        foreach ($classes as $c) {
            SchoolClass::firstOrCreate(['class_name' => $c]);
        }

        $sections = ['A', 'B', 'C'];
        foreach (SchoolClass::all() as $class) {
            foreach ($sections as $s) {
                Section::firstOrCreate(
                    ['class_id' => $class->id, 'section_name' => $s],
                    ['class_id' => $class->id, 'section_name' => $s]
                );
            }
        }

        $subjects = ['Mathematics', 'Science', 'English', 'Hindi', 'Social Studies'];
        foreach (SchoolClass::all() as $class) {
            foreach ($subjects as $sub) {
                Subject::firstOrCreate(
                    ['class_id' => $class->id, 'subject_name' => $sub],
                    ['class_id' => $class->id, 'subject_name' => $sub]
                );
            }
        }

        Exam::firstOrCreate(
            ['exam_name' => 'Half Yearly 2024'],
            ['term' => '1', 'start_date' => '2024-07-01', 'end_date' => '2024-07-15']
        );
        Exam::firstOrCreate(
            ['exam_name' => 'Annual 2024'],
            ['term' => '2', 'start_date' => '2024-12-01', 'end_date' => '2024-12-20']
        );

        $names = [
            ['Seema', 'Patel'], ['Ravi', 'Kumar'], ['Anjali', 'Verma'], ['Aryan', 'Joshi'], ['Neha', 'Sharma'],
            ['Reena', 'Mehra'], ['Nikita', 'Sharma'], ['Priya', 'Singh'], ['Rahul', 'Gupta'], ['Kavita', 'Reddy'],
        ];

        $classIds = SchoolClass::pluck('id');
        $sectionIds = Section::pluck('id');

        for ($i = 1; $i <= 20; $i++) {
            $n = $names[($i - 1) % count($names)];
            $classId = $classIds->random();
            $sectionId = Section::onlyABC()->where('class_id', $classId)->inRandomOrder()->first()->id;

            $admissionDate = Carbon::now()->subDays(rand(10, 200));
            $student = Student::firstOrCreate(
                ['admission_no' => 'ADM' . str_pad($i, 5, '0', STR_PAD_LEFT)],
                [
                    'roll_no' => (string) $i,
                    'first_name' => $n[0],
                    'last_name' => $n[1],
                    'gender' => $i % 2 ? 'Male' : 'Female',
                    'dob' => Carbon::now()->subYears(rand(6, 15)),
                    'blood_group' => ['A+', 'B+', 'O+', 'AB+'][rand(0, 3)],
                    'category' => 'General',
                    'class_id' => $classId,
                    'section_id' => $sectionId,
                    'father_name' => 'Father ' . $n[1],
                    'mother_name' => 'Mother ' . $n[1],
                    'parent_contact' => '98' . rand(100000000, 999999999),
                    'status' => $i < 18,
                    'admission_date' => $admissionDate,
                ]
            );

            Fee::firstOrCreate(
                ['student_id' => $student->id, 'academic_year' => '2024-25'],
                ['total_fee' => 50000, 'discount' => 0, 'final_amount' => 50000]
            );

            if (rand(0, 2) > 0) {
                FeePayment::create([
                    'student_id' => $student->id,
                    'fee_type' => 'Tuition',
                    'amount' => rand(5000, 25000),
                    'payment_mode' => ['Cash', 'Online', 'UPI'][rand(0, 2)],
                    'payment_date' => Carbon::now()->subDays(rand(1, 30)),
                    'receipt_no' => 'R' . $student->id . rand(100, 999),
                    'status' => 'Paid',
                ]);
            }
        }

        for ($i = 1; $i <= 50; $i++) {
            $student = Student::inRandomOrder()->first();
            if (!$student) continue;
            $date = Carbon::today()->subDays(rand(0, 7));
            Attendance::firstOrCreate(
                ['student_id' => $student->id, 'date' => $date],
                [
                    'class_id' => $student->class_id,
                    'section_id' => $student->section_id,
                    'status' => ['Present', 'Present', 'Absent', 'Late'][rand(0, 3)],
                ]
            );
        }

        Notice::firstOrCreate(
            ['title' => 'Parents Meeting'],
            ['description' => 'Annual parents meeting', 'notice_date' => Carbon::now()->addDays(5), 'end_date' => Carbon::now()->addDays(5), 'is_active' => true]
        );
        Notice::firstOrCreate(
            ['title' => 'Sports Day'],
            ['description' => 'Annual sports day event', 'notice_date' => Carbon::now()->addDays(7), 'end_date' => Carbon::now()->addDays(7), 'is_active' => true]
        );
    }
}
