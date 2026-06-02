<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('myb_member_state_commodity_trends')) {
            return;
        }

        Schema::table('myb_member_state_commodity_trends', function (Blueprint $table) {
            if (!Schema::hasColumn('myb_member_state_commodity_trends', 'stock_volume')) {
                $table->decimal('stock_volume', 20, 3)->nullable()->after('production_volume');
            }

            if (!Schema::hasColumn('myb_member_state_commodity_trends', 'import_volume')) {
                $table->decimal('import_volume', 20, 3)->nullable()->after('export_volume');
            }

            if (!Schema::hasColumn('myb_member_state_commodity_trends', 'market_price')) {
                $table->decimal('market_price', 20, 2)->nullable()->after('export_value_usd');
            }

            if (!Schema::hasColumn('myb_member_state_commodity_trends', 'market_price_currency')) {
                $table->string('market_price_currency', 12)->nullable()->after('market_price');
            }

            if (!Schema::hasColumn('myb_member_state_commodity_trends', 'availability_score')) {
                $table->decimal('availability_score', 5, 2)->nullable()->after('growth_rate_pct');
            }

            if (!Schema::hasColumn('myb_member_state_commodity_trends', 'review_status')) {
                $table->string('review_status', 30)->default('pending')->after('updated_by');
            }

            if (!Schema::hasColumn('myb_member_state_commodity_trends', 'reviewed_by')) {
                $table->foreignUuid('reviewed_by')->nullable()->after('review_status')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('myb_member_state_commodity_trends', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            }

            if (!Schema::hasColumn('myb_member_state_commodity_trends', 'review_notes')) {
                $table->longText('review_notes')->nullable()->after('reviewed_at');
            }
        });

        Schema::table('myb_member_state_commodity_trends', function (Blueprint $table) {
            $table->index(['review_status', 'recorded_on'], 'ms_comm_trend_review_idx');
            $table->index(['member_state_id', 'review_status'], 'ms_comm_trend_state_review_idx');
            $table->index(['commodity_id', 'review_status'], 'ms_comm_trend_commodity_review_idx');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('myb_member_state_commodity_trends')) {
            return;
        }

        Schema::table('myb_member_state_commodity_trends', function (Blueprint $table) {
            $table->dropIndex('ms_comm_trend_review_idx');
            $table->dropIndex('ms_comm_trend_state_review_idx');
            $table->dropIndex('ms_comm_trend_commodity_review_idx');

            if (Schema::hasColumn('myb_member_state_commodity_trends', 'reviewed_by')) {
                $table->dropForeign(['reviewed_by']);
            }
        });

        $columns = array_values(array_filter([
            'stock_volume',
            'import_volume',
            'market_price',
            'market_price_currency',
            'availability_score',
            'review_status',
            'reviewed_by',
            'reviewed_at',
            'review_notes',
        ], fn (string $column) => Schema::hasColumn('myb_member_state_commodity_trends', $column)));

        if (!empty($columns)) {
            Schema::table('myb_member_state_commodity_trends', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
