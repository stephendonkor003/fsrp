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
        Schema::table('myb_indicators', function (Blueprint $table) {
            // Hierarchical relationship for nested indicators (project under program)
            if (!Schema::hasColumn('myb_indicators', 'parent_indicator_id')) {
                $table->foreignUuid('parent_indicator_id')->nullable()->constrained('myb_indicators')->cascadeOnDelete()->after('name')->comment('Parent indicator (for nested project indicators)');
            }

            // Baseline tracking
            if (!Schema::hasColumn('myb_indicators', 'baseline_year')) {
                $table->string('baseline_year')->nullable()->after('parent_indicator_id')->comment('Baseline year/month/quarter/day (e.g., 2024, 2024-Q1, 2024-01, 2024-01-15)');
            }
            if (!Schema::hasColumn('myb_indicators', 'baseline_type')) {
                $table->enum('baseline_type', ['year', 'month', 'quarter', 'day'])->default('year')->after('baseline_year')->comment('Type of baseline period');
            }

            // Indicator metadata
            if (!Schema::hasColumn('myb_indicators', 'indicator_level_id')) {
                $table->foreignUuid('indicator_level_id')->nullable()->constrained('me_indicator_levels')->cascadeOnDelete()->after('baseline_type');
            }
            if (!Schema::hasColumn('myb_indicators', 'methodology')) {
                $table->text('methodology')->nullable()->after('indicator_level_id')->comment('How the indicator is measured');
            }
            if (!Schema::hasColumn('myb_indicators', 'notes')) {
                $table->text('notes')->nullable()->after('methodology')->comment('Additional notes');
            }
            if (!Schema::hasColumn('myb_indicators', 'responsible_party')) {
                $table->string('responsible_party')->nullable()->after('notes')->comment('Who is responsible for reporting');
            }
            if (!Schema::hasColumn('myb_indicators', 'frequency_of_reporting_id')) {
                $table->foreignUuid('frequency_of_reporting_id')->nullable()->constrained('me_reporting_frequencies')->cascadeOnDelete()->after('responsible_party');
            }
            if (!Schema::hasColumn('myb_indicators', 'unit_id')) {
                $table->foreignUuid('unit_id')->nullable()->constrained('me_indicator_units')->cascadeOnDelete()->after('frequency_of_reporting_id');
            }
            if (!Schema::hasColumn('myb_indicators', 'primary_source')) {
                $table->string('primary_source')->nullable()->after('unit_id')->comment('Primary data source');
            }
            if (!Schema::hasColumn('myb_indicators', 'definitions')) {
                $table->text('definitions')->nullable()->after('primary_source')->comment('Indicator definitions and terms');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('myb_indicators')) {
            return;
        }

        $this->dropForeignKeyIfExists('myb_indicators', 'parent_indicator_id');
        $this->dropForeignKeyIfExists('myb_indicators', 'indicator_level_id');
        $this->dropForeignKeyIfExists('myb_indicators', 'frequency_of_reporting_id');
        $this->dropForeignKeyIfExists('myb_indicators', 'unit_id');

        $columns = array_values(array_filter([
            'parent_indicator_id',
            'baseline_year',
            'baseline_type',
            'indicator_level_id',
            'methodology',
            'notes',
            'responsible_party',
            'frequency_of_reporting_id',
            'unit_id',
            'primary_source',
            'definitions',
        ], fn ($column) => Schema::hasColumn('myb_indicators', $column)));

        if ($columns !== []) {
            Schema::table('myb_indicators', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }

    private function dropForeignKeyIfExists(string $table, string $column): void
    {
        $driver = DB::getDriverName();
        $constraint = "{$table}_{$column}_foreign";

        if ($driver === 'pgsql') {
            DB::statement(sprintf(
                'ALTER TABLE %s DROP CONSTRAINT IF EXISTS %s',
                $table,
                $constraint
            ));

            return;
        }

        if ($driver === 'mysql') {
            $exists = DB::table('information_schema.KEY_COLUMN_USAGE')
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', $table)
                ->where('COLUMN_NAME', $column)
                ->whereNotNull('REFERENCED_TABLE_NAME')
                ->exists();

            if (!$exists) {
                return;
            }
        }

        Schema::table($table, function (Blueprint $blueprint) use ($column) {
            $blueprint->dropForeign([$column]);
        });
    }
};
