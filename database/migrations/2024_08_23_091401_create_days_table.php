<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateDaysTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('days', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Unique day name like 'monday', 'tuesday', etc.
            $table->timestamps();
        });

        // Seed the table with the days of the week
        DB::table('days')->insert([
            ['name' => 'monday'],
            ['name' => 'tuesday'],
            ['name' => 'wednesday'],
            ['name' => 'thursday'],
            ['name' => 'friday'],
            ['name' => 'saturday'],
            ['name' => 'sunday'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('days');
    }
}
