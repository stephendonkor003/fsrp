<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('myb_procurement_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('myb_procurement_plans', 'ppsd_reference')) {
                $table->string('ppsd_reference')->nullable()->after('step_approval_id');
            }

            if (!Schema::hasColumn('myb_procurement_plans', 'step_plan_id')) {
                $table->string('step_plan_id')->nullable()->after('ppsd_reference');
            }

            if (!Schema::hasColumn('myb_procurement_plans', 'step_plan_status')) {
                $table->string('step_plan_status', 30)->default('not_uploaded')->after('step_plan_id');
            }

            if (!Schema::hasColumn('myb_procurement_plans', 'step_last_uploaded_at')) {
                $table->date('step_last_uploaded_at')->nullable()->after('step_plan_status');
            }

            if (!Schema::hasColumn('myb_procurement_plans', 'prior_review_required')) {
                $table->boolean('prior_review_required')->default(false)->after('step_last_uploaded_at');
            }

            if (!Schema::hasColumn('myb_procurement_plans', 'world_bank_no_objection_status')) {
                $table->string('world_bank_no_objection_status', 30)->default('pending')->after('prior_review_required');
            }

            if (!Schema::hasColumn('myb_procurement_plans', 'world_bank_no_objection_date')) {
                $table->date('world_bank_no_objection_date')->nullable()->after('world_bank_no_objection_status');
            }

            if (!Schema::hasColumn('myb_procurement_plans', 'procurement_risk_level')) {
                $table->string('procurement_risk_level', 20)->nullable()->after('world_bank_no_objection_date');
            }

            if (!Schema::hasColumn('myb_procurement_plans', 'contract_log_reference')) {
                $table->string('contract_log_reference')->nullable()->after('procurement_risk_level');
            }

            if (!Schema::hasColumn('myb_procurement_plans', 'procurement_record_notes')) {
                $table->text('procurement_record_notes')->nullable()->after('contract_log_reference');
            }
        });

        Schema::table('myb_procurement_plans', function (Blueprint $table) {
            $table->index(['step_plan_status', 'world_bank_no_objection_status'], 'proc_plans_step_wb_status_idx');
            $table->index(['prior_review_required', 'procurement_risk_level'], 'proc_plans_review_risk_idx');
        });
    }

    public function down(): void
    {
        Schema::table('myb_procurement_plans', function (Blueprint $table) {
            $table->dropIndex('proc_plans_step_wb_status_idx');
            $table->dropIndex('proc_plans_review_risk_idx');
        });

        Schema::table('myb_procurement_plans', function (Blueprint $table) {
            $table->dropColumn([
                'ppsd_reference',
                'step_plan_id',
                'step_plan_status',
                'step_last_uploaded_at',
                'prior_review_required',
                'world_bank_no_objection_status',
                'world_bank_no_objection_date',
                'procurement_risk_level',
                'contract_log_reference',
                'procurement_record_notes',
            ]);
        });
    }
};
