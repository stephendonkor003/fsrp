<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // widen baseline_type to simple string to allow "week"
        if (!Schema::hasTable('myb_indicators') || !Schema::hasColumn('myb_indicators', 'baseline_type')) {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            $this->dropPostgresBaselineTypeChecks();
            DB::statement("ALTER TABLE myb_indicators ALTER COLUMN baseline_type TYPE varchar(20)");
            DB::statement("ALTER TABLE myb_indicators ALTER COLUMN baseline_type SET DEFAULT 'year'");
            DB::statement("ALTER TABLE myb_indicators ALTER COLUMN baseline_type SET NOT NULL");

            return;
        }

        Schema::table('myb_indicators', function (Blueprint $table) {
            $table->string('baseline_type', 20)->default('year')->change();
        });
    }

    public function down(): void
    {
        // revert to enum if needed
        if (!Schema::hasTable('myb_indicators') || !Schema::hasColumn('myb_indicators', 'baseline_type')) {
            return;
        }

        if (DB::getDriverName() === 'pgsql') {
            $this->dropPostgresBaselineTypeChecks();
            DB::statement("UPDATE myb_indicators SET baseline_type = 'year' WHERE baseline_type NOT IN ('year', 'month', 'quarter', 'day')");
            DB::statement("ALTER TABLE myb_indicators ALTER COLUMN baseline_type TYPE varchar(255)");
            DB::statement("ALTER TABLE myb_indicators ALTER COLUMN baseline_type SET DEFAULT 'year'");
            DB::statement("ALTER TABLE myb_indicators ALTER COLUMN baseline_type SET NOT NULL");
            DB::statement("ALTER TABLE myb_indicators ADD CONSTRAINT myb_indicators_baseline_type_check CHECK (baseline_type IN ('year', 'month', 'quarter', 'day'))");

            return;
        }

            Schema::table('myb_indicators', function (Blueprint $table) {
                $table->enum('baseline_type', ['year','month','quarter','day'])->default('year')->change();
            });
    }

    private function dropPostgresBaselineTypeChecks(): void
    {
        DB::statement(<<<'SQL'
DO $$
DECLARE
    constraint_record record;
BEGIN
    FOR constraint_record IN
        SELECT con.conname
        FROM pg_constraint con
        JOIN pg_class rel ON rel.oid = con.conrelid
        JOIN pg_namespace nsp ON nsp.oid = rel.relnamespace
        WHERE rel.relname = 'myb_indicators'
          AND con.contype = 'c'
          AND pg_get_constraintdef(con.oid) ILIKE '%baseline_type%'
    LOOP
        EXECUTE format('ALTER TABLE myb_indicators DROP CONSTRAINT IF EXISTS %I', constraint_record.conname);
    END LOOP;
END $$;
SQL);
    }
};
