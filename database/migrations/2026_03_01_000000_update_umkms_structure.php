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
        if (! Schema::hasTable('umkms')) {
            return;
        }

        Schema::table('umkms', function (Blueprint $table) {
            // add verification flag
            if (! Schema::hasColumn('umkms', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('owner_id');
            }

            // new platform fee structure
            if (! Schema::hasColumn('umkms', 'platform_fee_type')) {
                $table->enum('platform_fee_type', ['percentage', 'flat'])->default('percentage')->after('owner_id');
            }
            if (! Schema::hasColumn('umkms', 'platform_fee_rate')) {
                $table->decimal('platform_fee_rate', 5, 2)->default(0)->after('platform_fee_type');
            }
            if (! Schema::hasColumn('umkms', 'platform_fee_flat')) {
                $table->decimal('platform_fee_flat', 12, 2)->default(0)->after('platform_fee_rate');
            }

            // drop old columns if they exist
            if (Schema::hasColumn('umkms', 'platform_fee')) {
                $table->dropColumn('platform_fee');
            }
            if (Schema::hasColumn('umkms', 'revenue_total')) {
                $table->dropColumn('revenue_total');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('umkms')) {
            return;
        }

        Schema::table('umkms', function (Blueprint $table) {
            if (Schema::hasColumn('umkms', 'is_verified')) {
                $table->dropColumn('is_verified');
            }
            if (Schema::hasColumn('umkms', 'platform_fee_type')) {
                $table->dropColumn('platform_fee_type');
            }
            if (Schema::hasColumn('umkms', 'platform_fee_rate')) {
                $table->dropColumn('platform_fee_rate');
            }
            if (Schema::hasColumn('umkms', 'platform_fee_flat')) {
                $table->dropColumn('platform_fee_flat');
            }

            // recreate old columns
            if (! Schema::hasColumn('umkms', 'platform_fee')) {
                $table->float('platform_fee')->default(0)->after('owner_id');
            }
            if (! Schema::hasColumn('umkms', 'revenue_total')) {
                $table->float('revenue_total')->default(0)->after('platform_fee');
            }
        });
    }
};
