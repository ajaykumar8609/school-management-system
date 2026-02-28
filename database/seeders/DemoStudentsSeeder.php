<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Fee;
use App\Models\FeePayment;
use App\Models\Mark;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DemoStudentsSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have Class 5 Section A and Class 7 Section B
        $class5 = SchoolClass::where('class_name', '5')->first();
        $class7 = SchoolClass::where('class_name', '7')->first();
        if (!$class5 || !$class7) return;

        $section5A = Section::onlyABC()->where('class_id', $class5->id)->where('section_name', 'A')->first();
        $section7B = Section::onlyABC()->where('class_id', $class7->id)->where('section_name', 'B')->first();
        if (!$section5A || !$section7B) return;

        $exams = Exam::all();
        $exam1 = $exams->first();
        $exam2 = $exams->skip(1)->first();

        $subjects5 = Subject::where('class_id', $class5->id)->get();
        $subjects7 = Subject::where('class_id', $class7->id)->get();

        // Student 1: Ravi Kumar - Class 5-A
        $student1 = Student::updateOrCreate(
            ['admission_no' => 'ADM50001'],
            [
                'roll_no' => '101',
                'first_name' => 'Ravi',
                'last_name' => 'Kumar',
                'gender' => 'Male',
                'dob' => Carbon::parse('2015-05-10'),
                'blood_group' => 'B+',
                'category' => 'General',
                'class_id' => $class5->id,
                'section_id' => $section5A->id,
                'father_name' => 'Rajesh Kumar',
                'mother_name' => 'Sunita Kumar',
                'parent_contact' => '9876543210',
                'alt_contact' => '9876543211',
                'email' => 'ravi.kumar@example.com',
                'current_address' => '123 Gandhi Nagar, Delhi',
                'permanent_address' => '123 Gandhi Nagar, Delhi',
                'status' => true,
                'admission_date' => Carbon::parse('2024-04-01'),
            ]
        );

        Fee::updateOrCreate(
            ['student_id' => $student1->id, 'academic_year' => '2024-25'],
            ['total_fee' => 50000, 'discount' => 5000, 'final_amount' => 45000]
        );

        FeePayment::firstOrCreate(
            ['student_id' => $student1->id, 'receipt_no' => 'R500011'],
            [
                'fee_type' => 'Tuition',
                'amount' => 15000,
                'payment_mode' => 'UPI',
                'transaction_id' => 'TXN50001',
                'payment_date' => Carbon::now()->subDays(30),
                'status' => 'Paid',
            ]
        );
        FeePayment::firstOrCreate(
            ['student_id' => $student1->id, 'receipt_no' => 'R500012'],
            [
                'fee_type' => 'Tuition',
                'amount' => 15000,
                'payment_mode' => 'Cash',
                'payment_date' => Carbon::now()->subDays(15),
                'status' => 'Paid',
            ]
        );

        foreach ($subjects5 as $subj) {
            $marks = rand(70, 95);
            Mark::updateOrCreate(
                ['student_id' => $student1->id, 'exam_id' => $exam1->id, 'subject_id' => $subj->id],
                ['marks_obtained' => $marks, 'total_marks' => 100, 'grade' => $marks >= 90 ? 'A+' : ($marks >= 80 ? 'A' : ($marks >= 70 ? 'B' : 'C'))]
            );
            $marks2 = rand(65, 90);
            Mark::updateOrCreate(
                ['student_id' => $student1->id, 'exam_id' => $exam2->id, 'subject_id' => $subj->id],
                ['marks_obtained' => $marks2, 'total_marks' => 100, 'grade' => $marks2 >= 90 ? 'A+' : ($marks2 >= 80 ? 'A' : ($marks2 >= 70 ? 'B' : 'C'))]
            );
        }

        for ($i = 0; $i < 10; $i++) {
            $dt = Carbon::today()->subDays($i);
            Attendance::updateOrCreate(
                ['student_id' => $student1->id, 'date' => $dt],
                [
                    'class_id' => $class5->id,
                    'section_id' => $section5A->id,
                    'status' => $i < 2 ? 'Absent' : ($i === 3 ? 'Late' : 'Present'),
                ]
            );
        }

        // Student 2: Anjali Verma - Class 7-B
        $student2 = Student::updateOrCreate(
            ['admission_no' => 'ADM50002'],
            [
                'roll_no' => '201',
                'first_name' => 'Anjali',
                'last_name' => 'Verma',
                'gender' => 'Female',
                'dob' => Carbon::parse('2013-08-20'),
                'blood_group' => 'A+',
                'category' => 'General',
                'class_id' => $class7->id,
                'section_id' => $section7B->id,
                'father_name' => 'Amit Verma',
                'mother_name' => 'Pooja Verma',
                'parent_contact' => '9234567890',
                'alt_contact' => '9234567891',
                'email' => 'anjali.verma@example.com',
                'current_address' => '456 Nehru Road, Mumbai',
                'permanent_address' => '456 Nehru Road, Mumbai',
                'status' => true,
                'admission_date' => Carbon::parse('2024-04-01'),
            ]
        );

        Fee::updateOrCreate(
            ['student_id' => $student2->id, 'academic_year' => '2024-25'],
            ['total_fee' => 55000, 'discount' => 0, 'final_amount' => 55000]
        );

        FeePayment::firstOrCreate(
            ['student_id' => $student2->id, 'receipt_no' => 'R500021'],
            [
                'fee_type' => 'Tuition',
                'amount' => 27500,
                'payment_mode' => 'Online',
                'transaction_id' => 'TXN50002',
                'payment_date' => Carbon::now()->subDays(20),
                'status' => 'Paid',
            ]
        );
        FeePayment::firstOrCreate(
            ['student_id' => $student2->id, 'receipt_no' => 'R500022'],
            [
                'fee_type' => 'Tuition',
                'amount' => 15000,
                'payment_mode' => 'Card',
                'payment_date' => Carbon::now()->subDays(5),
                'status' => 'Paid',
            ]
        );

        foreach ($subjects7 as $subj) {
            $marks = rand(75, 98);
            Mark::updateOrCreate(
                ['student_id' => $student2->id, 'exam_id' => $exam1->id, 'subject_id' => $subj->id],
                ['marks_obtained' => $marks, 'total_marks' => 100, 'grade' => $marks >= 90 ? 'A+' : ($marks >= 80 ? 'A' : ($marks >= 70 ? 'B' : 'C'))]
            );
            $marks2 = rand(72, 92);
            Mark::updateOrCreate(
                ['student_id' => $student2->id, 'exam_id' => $exam2->id, 'subject_id' => $subj->id],
                ['marks_obtained' => $marks2, 'total_marks' => 100, 'grade' => $marks2 >= 90 ? 'A+' : ($marks2 >= 80 ? 'A' : ($marks2 >= 70 ? 'B' : 'C'))]
            );
        }

        for ($i = 0; $i < 10; $i++) {
            $dt = Carbon::today()->subDays($i);
            Attendance::updateOrCreate(
                ['student_id' => $student2->id, 'date' => $dt],
                [
                    'class_id' => $class7->id,
                    'section_id' => $section7B->id,
                    'status' => $i === 1 ? 'Absent' : 'Present',
                ]
            );
        }
    }
}
