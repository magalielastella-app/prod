<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('employee')->after('email');
            $table->string('position')->nullable()->after('role');
            $table->string('department')->nullable()->after('position');
            $table->date('hired_on')->nullable()->after('department');
            $table->foreignId('manager_id')->nullable()->after('hired_on')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('manager_id');
            $table->dropColumn(['role', 'position', 'department', 'hired_on']);
        });
    }
};
