<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fsrp_components', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 40)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('auc_allocation_usd', 18, 2)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('fsrp_subcomponents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('component_id')->constrained('fsrp_components')->cascadeOnDelete();
            $table->string('code', 40)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['component_id', 'sort_order']);
        });

        $this->addTaxonomyLinks('approved_work_plans');
        $this->addTaxonomyLinks('myb_procurement_plans');
        $this->addTaxonomyLinks('myb_indicators');
        $this->addTaxonomyLinks('attp_activity_reports');
        $this->addTaxonomyLinks('attp_news_posts');
        $this->addTaxonomyLinks('attp_workplans');
        $this->addTaxonomyLinks('attp_think_tank_research_outputs');

        Schema::table('myb_indicators', function (Blueprint $table) {
            if (!Schema::hasColumn('myb_indicators', 'disaggregation')) {
                $table->string('disaggregation')->nullable()->after('definitions');
            }
            if (!Schema::hasColumn('myb_indicators', 'lop_target_value')) {
                $table->decimal('lop_target_value', 20, 4)->nullable()->after('disaggregation');
            }
            if (!Schema::hasColumn('myb_indicators', 'reporting_period_target_value')) {
                $table->decimal('reporting_period_target_value', 20, 4)->nullable()->after('lop_target_value');
            }
            if (!Schema::hasColumn('myb_indicators', 'reporting_period_achievement_value')) {
                $table->decimal('reporting_period_achievement_value', 20, 4)->nullable()->after('reporting_period_target_value');
            }
            if (!Schema::hasColumn('myb_indicators', 'reporting_period_performance_pct')) {
                $table->decimal('reporting_period_performance_pct', 8, 2)->nullable()->after('reporting_period_achievement_value');
            }
            if (!Schema::hasColumn('myb_indicators', 'lop_performance_pct')) {
                $table->decimal('lop_performance_pct', 8, 2)->nullable()->after('reporting_period_performance_pct');
            }
            if (!Schema::hasColumn('myb_indicators', 'performance_remarks')) {
                $table->text('performance_remarks')->nullable()->after('lop_performance_pct');
            }
        });

        Schema::table('procurement_disbursements', function (Blueprint $table) {
            if (!Schema::hasColumn('procurement_disbursements', 'designated_account_activity')) {
                $table->string('designated_account_activity')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('procurement_disbursements', 'bank_statement_reference')) {
                $table->string('bank_statement_reference')->nullable()->after('designated_account_activity');
            }
            if (!Schema::hasColumn('procurement_disbursements', 'bank_statement_file_path')) {
                $table->string('bank_statement_file_path')->nullable()->after('bank_statement_reference');
            }
            if (!Schema::hasColumn('procurement_disbursements', 'prior_review_expenditure')) {
                $table->boolean('prior_review_expenditure')->default(false)->after('bank_statement_file_path');
            }
            if (!Schema::hasColumn('procurement_disbursements', 'ifr_notes')) {
                $table->text('ifr_notes')->nullable()->after('prior_review_expenditure');
            }
        });

        Schema::table('procurement_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('procurement_invoices', 'bank_statement_reference')) {
                $table->string('bank_statement_reference')->nullable()->after('currency');
            }
            if (!Schema::hasColumn('procurement_invoices', 'prior_review_expenditure')) {
                $table->boolean('prior_review_expenditure')->default(false)->after('bank_statement_reference');
            }
            if (!Schema::hasColumn('procurement_invoices', 'ifr_notes')) {
                $table->text('ifr_notes')->nullable()->after('prior_review_expenditure');
            }
        });

        Schema::create('fsrp_safeguard_screenings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('screening_code')->unique();
            $table->string('title');
            $table->foreignUuid('fsrp_component_id')->nullable()->constrained('fsrp_components')->nullOnDelete();
            $table->foreignUuid('fsrp_subcomponent_id')->nullable()->constrained('fsrp_subcomponents')->nullOnDelete();
            $table->foreignUuid('activity_id')->nullable()->constrained('myb_activities')->nullOnDelete();
            $table->foreignUuid('sub_activity_id')->nullable()->constrained('myb_sub_activities')->nullOnDelete();
            $table->foreignUuid('procurement_plan_id')->nullable()->constrained('myb_procurement_plans')->nullOnDelete();
            $table->foreignUuid('approved_work_plan_id')->nullable()->constrained('approved_work_plans')->nullOnDelete();
            $table->string('risk_level', 30)->default('low')->index();
            $table->string('screening_status', 30)->default('draft')->index();
            $table->date('screened_on')->nullable();
            $table->foreignUuid('screened_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('environmental_risks')->nullable();
            $table->text('social_risks')->nullable();
            $table->text('mitigation_measures')->nullable();
            $table->text('evidence_reference')->nullable();
            $table->date('next_review_due_on')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('fsrp_stakeholder_engagements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('engagement_code')->unique();
            $table->string('title');
            $table->foreignUuid('fsrp_component_id')->nullable()->constrained('fsrp_components')->nullOnDelete();
            $table->foreignUuid('fsrp_subcomponent_id')->nullable()->constrained('fsrp_subcomponents')->nullOnDelete();
            $table->date('engagement_date')->nullable();
            $table->string('location')->nullable();
            $table->string('stakeholder_group')->nullable();
            $table->integer('participants_count')->nullable();
            $table->text('summary')->nullable();
            $table->text('commitments_made')->nullable();
            $table->text('follow_up_actions')->nullable();
            $table->date('follow_up_due_on')->nullable();
            $table->string('status', 30)->default('open')->index();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('fsrp_grievances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('case_code')->unique();
            $table->string('complainant_name')->nullable();
            $table->string('complainant_contact')->nullable();
            $table->string('category', 50)->default('general')->index();
            $table->string('priority', 20)->default('normal')->index();
            $table->string('status', 30)->default('open')->index();
            $table->foreignUuid('fsrp_component_id')->nullable()->constrained('fsrp_components')->nullOnDelete();
            $table->foreignUuid('fsrp_subcomponent_id')->nullable()->constrained('fsrp_subcomponents')->nullOnDelete();
            $table->date('received_on')->nullable();
            $table->text('description');
            $table->text('resolution_actions')->nullable();
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_on')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('closure_notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fsrp_grievances');
        Schema::dropIfExists('fsrp_stakeholder_engagements');
        Schema::dropIfExists('fsrp_safeguard_screenings');

        Schema::table('procurement_invoices', function (Blueprint $table) {
            $table->dropColumn(['bank_statement_reference', 'prior_review_expenditure', 'ifr_notes']);
        });

        Schema::table('procurement_disbursements', function (Blueprint $table) {
            $table->dropColumn([
                'designated_account_activity',
                'bank_statement_reference',
                'bank_statement_file_path',
                'prior_review_expenditure',
                'ifr_notes',
            ]);
        });

        Schema::table('myb_indicators', function (Blueprint $table) {
            $table->dropColumn([
                'disaggregation',
                'lop_target_value',
                'reporting_period_target_value',
                'reporting_period_achievement_value',
                'reporting_period_performance_pct',
                'lop_performance_pct',
                'performance_remarks',
            ]);
        });

        foreach ([
            'attp_think_tank_research_outputs',
            'attp_workplans',
            'attp_news_posts',
            'attp_activity_reports',
            'myb_indicators',
            'myb_procurement_plans',
            'approved_work_plans',
        ] as $tableName) {
            $this->dropTaxonomyLinks($tableName);
        }

        Schema::dropIfExists('fsrp_subcomponents');
        Schema::dropIfExists('fsrp_components');
    }

    private function addTaxonomyLinks(string $tableName): void
    {
        if (!Schema::hasTable($tableName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (!Schema::hasColumn($tableName, 'fsrp_component_id')) {
                $table->foreignUuid('fsrp_component_id')->nullable()->constrained('fsrp_components')->nullOnDelete();
            }
            if (!Schema::hasColumn($tableName, 'fsrp_subcomponent_id')) {
                $table->foreignUuid('fsrp_subcomponent_id')->nullable()->constrained('fsrp_subcomponents')->nullOnDelete();
            }
        });
    }

    private function dropTaxonomyLinks(string $tableName): void
    {
        if (!Schema::hasTable($tableName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (Schema::hasColumn($tableName, 'fsrp_subcomponent_id')) {
                $table->dropConstrainedForeignId('fsrp_subcomponent_id');
            }
            if (Schema::hasColumn($tableName, 'fsrp_component_id')) {
                $table->dropConstrainedForeignId('fsrp_component_id');
            }
        });
    }
};
