<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('national_id', 9)->nullable()->after('id');
        });

        // Backfill existing accounts so the unique/required constraints below don't fail.
        $placeholders = [
            'admin@booking.test'    => '900000001',
            'doctor@booking.test'   => '900000002',
            'salon@booking.test'    => '900000003',
            'customer@booking.test' => '900000004',
        ];
        foreach ($placeholders as $email => $nationalId) {
            DB::table('users')->where('email', $email)->update(['national_id' => $nationalId]);
        }
        DB::table('users')->where('email', 'mamdouh.abed@gmail.com')->update([
            'national_id' => '802725697',
            'phone'       => '0592881818',
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->string('national_id', 9)->unique()->nullable(false)->change();
            $table->string('phone', 20)->nullable(false)->change();
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->string('phone', 20)->nullable()->change();
            $table->dropUnique(['national_id']);
            $table->dropColumn('national_id');
        });
    }
};
