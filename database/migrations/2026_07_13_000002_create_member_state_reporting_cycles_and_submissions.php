<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $this->ensureMemberStateReportingFrequencies();

        Schema::create('me_member_state_reporting_cycles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('reporting_frequency_id')
                ->constrained('me_reporting_frequencies')
                ->restrictOnDelete();
            $table->string('period_key', 50);
            $table->string('label');
            $table->unsignedSmallInteger('reporting_year');
            $table->unsignedTinyInteger('period_number')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamp('opens_at')->nullable();
            $table->timestamp('closes_at')->nullable();
            $table->string('status', 30)->default('draft');
            $table->text('instructions')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(
                ['reporting_frequency_id', 'period_key'],
                'ms_reporting_cycles_frequency_period_unique'
            );
            $table->index(['status', 'period_start', 'period_end'], 'ms_reporting_cycles_status_dates_idx');
            $table->index(['reporting_year', 'period_number'], 'ms_reporting_cycles_year_period_idx');
            $table->index(['opens_at', 'closes_at'], 'ms_reporting_cycles_window_idx');
        });

        Schema::create('myb_member_state_report_submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('member_state_id')
                ->constrained('myb_au_member_states')
                ->restrictOnDelete();
            $table->foreignUuid('reporting_cycle_id')
                ->constrained('me_member_state_reporting_cycles')
                ->restrictOnDelete();
            $table->string('status', 30)->default('draft');
            $table->foreignUuid('started_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['member_state_id', 'reporting_cycle_id'],
                'ms_report_submissions_state_cycle_unique'
            );
            $table->index(['member_state_id', 'status'], 'ms_report_submissions_state_status_idx');
            $table->index(['reporting_cycle_id', 'status'], 'ms_report_submissions_cycle_status_idx');
            $table->index('submitted_at', 'ms_report_submissions_submitted_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('myb_member_state_report_submissions');
        Schema::dropIfExists('me_member_state_reporting_cycles');
    }

    /**
     * Ensure the three cadences required by the Member State portal are
     * immediately available, including on databases that were seeded before
     * this feature existed.
     */
    private function ensureMemberStateReportingFrequencies(): void
    {
        $frequencies = [
            [
                'name' => 'Quarterly',
                'code' => 'QUARTERLY',
                'interval_unit' => 'quarterly',
                'interval_value' => 1,
                'frequency_in_days' => 90,
                'description' => 'Member State reporting every quarter.',
            ],
            [
                'name' => 'Semi-Annual',
                'code' => 'SEMI_ANNUAL',
                'interval_unit' => 'month',
                'interval_value' => 6,
                'frequency_in_days' => 182,
                'description' => 'Member State reporting every six months.',
            ],
            [
                'name' => 'Annual',
                'code' => 'ANNUAL',
                'interval_unit' => 'annual',
                'interval_value' => 1,
                'frequency_in_days' => 365,
                'description' => 'Member State reporting once per year.',
            ],
        ];

        $nextSortOrder = ((int) DB::table('me_reporting_frequencies')->max('sort_order')) + 1;
        $now = now();

        foreach ($frequencies as $frequency) {
            $existingId = DB::table('me_reporting_frequencies')
                ->where('code', $frequency['code'])
                ->value('id');

            if ($existingId) {
                DB::table('me_reporting_frequencies')
                    ->where('id', $existingId)
                    ->update([
                        ...$frequency,
                        'is_active' => true,
                        'updated_at' => $now,
                    ]);

                continue;
            }

            DB::table('me_reporting_frequencies')->insert([
                'id' => (string) Str::uuid(),
                ...$frequency,
                'sort_order' => $nextSortOrder++,
                'is_active' => true,
                'created_by' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
};
