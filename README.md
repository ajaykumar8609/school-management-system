# School Management System - Student Management Module

Laravel-based School Management System with full CRUD for students, attendance, academic marks, and fee management.

## Requirements

- PHP 8.2+
- MySQL
- Composer
- XAMPP (or Apache + MySQL)

## Installation

1. Database is already configured for MySQL. Update `.env` if needed:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_DATABASE=school_management_system
   DB_USERNAME=root
   DB_PASSWORD=
   ```

2. Run migrations (if not done):
   ```bash
   php artisan migrate
   ```

3. Seed sample data:
   ```bash
   php artisan db:seed
   ```

4. Start the server:
   ```bash
   php artisan serve
   ```

5. Open: http://localhost:8000

## Features

### PAGE 1: Student Dashboard
- Summary cards: Total Students, Active, New Admissions, Due Fees, Fee Collection
- Attendance Overview chart
- Class-wise distribution (Pie chart)
- Recent Admissions table
- Notices section

### PAGE 2: Student List
- Search by Name, Roll No, Contact
- Filters: Class, Section, Status
- Full CRUD: View, Edit, Delete
- Quick links: Academic Record, Fee Report

### PAGE 3: Add/Edit Student
- Personal, Academic, Parent, Address details
- Photo upload
- Admission number, Roll number

### PAGE 4: Student Profile
- Tabs: Personal Info, Academic Records, Attendance, Fee Details, Documents

### PAGE 5: Attendance Management
- Select Class, Section, Date
- Mark Present/Absent/Late
- Bulk actions: Mark All Present/Absent

### PAGE 6: Academic Management
- Select Class, Exam, Subject
- Enter marks per student
- Auto grade calculation

### PAGE 7: Fee Management
- Student fee list with Paid/Due status
- Add Payment modal
- View Fee Report per student

## Database Structure

- **students** - Main student records
- **classes** - Class names (1-10)
- **sections** - Sections per class (A, B, C)
- **subjects** - Subjects per class
- **exams** - Exam records
- **marks** - Student marks (exam + subject)
- **attendance** - Daily attendance
- **fees** - Fee structure per student
- **fee_payments** - Payment records
- **documents** - Student documents
- **notices** - School notices

## UI Theme

- Clean flat design
- Blue & white professional theme
- Dark blue sidebar
- Card-based layout
- Responsive
