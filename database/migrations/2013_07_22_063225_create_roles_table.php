<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        /*
         * 0 - nothing
         * 1 - read
         * 2 - update
         * 3 - create
         * 4 - delete
         * 
         */

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();

            // access for tables (CRUD)
            $table->unsignedTinyInteger('roles')->default(0);
            $table->unsignedTinyInteger('users')->default(0);
            $table->unsignedTinyInteger('inactive_users')->default(0);
            // $table->unsignedTinyInteger('weekdays')->default(0);
            $table->unsignedTinyInteger('teachers')->default(0);
            $table->unsignedTinyInteger('assistant_teachers')->default(0);
            $table->unsignedTinyInteger('courses')->default(0);
            $table->unsignedTinyInteger('lessons')->default(0);
            $table->unsignedTinyInteger('groups')->default(0);
            $table->unsignedTinyInteger('students')->default(0);
            $table->unsignedTinyInteger('stparents')->default(0);
            $table->unsignedTinyInteger('sessions')->default(0);
            $table->unsignedTinyInteger('branches')->default(0);
            $table->unsignedTinyInteger('rooms')->default(0);
            $table->unsignedTinyInteger('schedules')->default(0);
            // $table->unsignedTinyInteger('certificates')->default(0);
            // $table->unsignedTinyInteger('failedsts')->default(0);
            // $table->unsignedTinyInteger('failedgroups')->default(0);
            $table->unsignedTinyInteger('cashiers')->default(0);
            $table->unsignedTinyInteger('access_for_courses')->default(0);
            $table->unsignedTinyInteger('cards')->default(0);
            $table->unsignedTinyInteger('payments')->default(0);
            $table->unsignedTinyInteger('changes')->default(0);

            // $table->index(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
