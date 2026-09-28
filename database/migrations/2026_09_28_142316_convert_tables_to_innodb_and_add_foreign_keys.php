<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The MySQL server default storage engine on this box is MyISAM, so every
 * `->constrained()->cascadeOnDelete()` in the earlier migrations was silently
 * dropped by the CREATE TABLE statement (MyISAM has no FK support) — only the
 * plain supporting index survived. This migration switches the app tables to
 * InnoDB and adds the foreign keys the original migrations intended.
 */
return new class extends Migration
{
    public function up(): void
    {
        // MySQL-only remediation: SQLite (used in testing) has no storage engines and
        // already enforces the FKs the original migrations declared, natively.
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        $tables = ['users', 'services', 'slots', 'bookings', 'reviews', 'payment_methods', 'payments'];
        foreach ($tables as $table) {
            DB::statement("ALTER TABLE `{$table}` ENGINE=InnoDB");
        }

        // Drop plain indexes that share the name Laravel wants for the real FK constraint.
        Schema::table('services', fn (Blueprint $table) => $table->dropIndex('services_provider_id_foreign'));
        Schema::table('bookings', fn (Blueprint $table) => $table->dropIndex('bookings_slot_id_foreign'));
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('reviews_customer_id_foreign');
            $table->dropIndex('reviews_service_id_foreign');
        });
        Schema::table('payment_methods', fn (Blueprint $table) => $table->dropIndex('payment_methods_provider_id_foreign'));
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_customer_id_foreign');
            $table->dropIndex('payments_provider_id_foreign');
            $table->dropIndex('payments_payment_method_id_foreign');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->foreign('provider_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('slots', function (Blueprint $table) {
            $table->foreign('service_id')->references('id')->on('services')->cascadeOnDelete();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('slot_id')->references('id')->on('slots')->cascadeOnDelete();
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('booking_id')->references('id')->on('bookings')->cascadeOnDelete();
            $table->foreign('customer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('service_id')->references('id')->on('services')->cascadeOnDelete();
        });

        Schema::table('payment_methods', function (Blueprint $table) {
            $table->foreign('provider_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('booking_id')->references('id')->on('bookings')->cascadeOnDelete();
            $table->foreign('customer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('provider_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['provider_id']);
            $table->dropForeign(['payment_method_id']);
        });

        Schema::table('payment_methods', fn (Blueprint $table) => $table->dropForeign(['provider_id']));

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['service_id']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['slot_id']);
        });

        Schema::table('slots', fn (Blueprint $table) => $table->dropForeign(['service_id']));

        Schema::table('services', fn (Blueprint $table) => $table->dropForeign(['provider_id']));
    }
};
