<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $standardClasses = ['Nursery', 'LKG', 'UKG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'];
        $now = now();
        foreach ($standardClasses as $name) {
            DB::table('classes')->updateOrInsert(
                ['class_name' => $name],
                ['class_name' => $name, 'created_at' => $now, 'updated_at' => $now]
            );
        }
        foreach (DB::table('classes')->get() as $class) {
            foreach (['A', 'B', 'C'] as $sec) {
                DB::table('sections')->updateOrInsert(
                    ['class_id' => $class->id, 'section_name' => $sec],
                    ['class_id' => $class->id, 'section_name' => $sec, 'created_at' => $now, 'updated_at' => $now]
                );
            }
        }
    }

    public function down(): void
    {
        //
    }
};
