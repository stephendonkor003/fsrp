<?php

namespace Tests\Feature;

use App\Models\MemberStateReportingCycle;
use App\Models\MemberStateReportSubmission;
use App\Models\ReportingFrequency;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MemberStateReportingPortalTest extends TestCase
{
    use DatabaseTransactions;

    public function test_member_state_dashboard_reporting_index_and_section_pages_render_without_a_sidebar(): void
    {
        $user = User::query()
            ->with('memberState')
            ->where('email', 'kenya.memberstate@fsrp.test')
            ->firstOrFail();

        // Exercise the portal without changing the seeded first-login state.
        $user->forceFill([
            'must_change_password' => false,
            'password_changed_at' => now(),
        ]);

        $this->actingAs($user);

        $sections = config('member_state_reporting.sections');
        $dashboard = $this->get(route('member-state.dashboard'));

        $dashboard
            ->assertOk()
            ->assertSee('Member State Reporting Portal')
            ->assertSee('Your reporting centre')
            ->assertSee('Portal services')
            ->assertSee('Submit Information')
            ->assertSee('View Performance')
            ->assertSee('Check Notifications')
            ->assertSee('Documents and Raw Data')
            ->assertSee('dashboard-card-search')
            ->assertSee('FSRP Member State Portal')
            ->assertDontSee('Your reporting workspace')
            ->assertDontSee('4 workspace areas')
            ->assertDontSee('id="menu-mini-button"', false);

        $this->assertCount(18, $sections);
        $this->assertSame(4, substr_count($dashboard->getContent(), 'class="ms-action-card ms-action-card--'));

        $reportingIndex = $this->get(route('member-state.reporting.index'));

        $reportingIndex
            ->assertOk()
            ->assertSee('Submit information')
            ->assertSee('Reporting Frequency:')
            ->assertSee('Quarterly')
            ->assertSee('Semi-Annual')
            ->assertSee('Annual')
            ->assertSee('18 sections')
            ->assertSee('reporting-section-search')
            ->assertSee('Back to Dashboard')
            ->assertSee('FSRP Member State Portal')
            ->assertDontSee('Sections A–R reporting workspace')
            ->assertDontSee('id="menu-mini-button"', false);

        $this->assertSame(18, substr_count($reportingIndex->getContent(), 'class="ms-module-card ms-module-card--locked"'));
        $this->assertFileExists(public_path('admin/assets/js/member-state-card-search.js'));

        $frequency = ReportingFrequency::query()->where('code', 'QUARTERLY')->firstOrFail();
        $cycle = MemberStateReportingCycle::create([
            'reporting_frequency_id' => $frequency->id,
            'period_key' => '2099-Q1',
            'label' => 'Quarter 1, 2099',
            'reporting_year' => 2099,
            'period_number' => 1,
            'period_start' => '2099-01-01',
            'period_end' => '2099-03-31',
            'status' => MemberStateReportingCycle::STATUS_OPEN,
        ]);

        $this->post(route('member-state.reporting.start'), [
            'reporting_cycle_id' => $cycle->id,
        ])->assertRedirect();

        $submission = MemberStateReportSubmission::query()
            ->where('member_state_id', $user->member_state_id)
            ->where('reporting_cycle_id', $cycle->id)
            ->firstOrFail();

        $this->post(route('member-state.reporting.start'), [
            'reporting_cycle_id' => $cycle->id,
        ])->assertRedirect();

        $this->assertSame(1, MemberStateReportSubmission::query()
            ->where('member_state_id', $user->member_state_id)
            ->where('reporting_cycle_id', $cycle->id)
            ->count());

        $selectedReportingIndex = $this->get(route('member-state.reporting.index', [
            'submission' => $submission->id,
        ]));

        $selectedReportingIndex
            ->assertOk()
            ->assertSee('Quarter 1, 2099')
            ->assertSee('Selected reporting package');

        $this->assertSame(18, substr_count($selectedReportingIndex->getContent(), 'class="ms-module-card"'));

        foreach ($sections as $section) {
            $this->assertFileExists(public_path($section['image']));

            $selectedReportingIndex
                ->assertSee('Section '.$section['letter'])
                ->assertSee($section['title']);

            $this->get(route('member-state.reporting.show', [
                'section' => $section['slug'],
                'submission' => $submission->id,
            ]))
                ->assertOk()
                ->assertSee('Section '.$section['letter'])
                ->assertSee($section['title'])
                ->assertSee('Back to Submit data')
                ->assertSee('Draft report')
                ->assertSee('FSRP Member State Portal')
                ->assertDontSee('id="menu-mini-button"', false);
        }
    }

    public function test_member_state_workspaces_share_the_same_portal_shell(): void
    {
        $user = User::query()
            ->where('email', 'kenya.memberstate@fsrp.test')
            ->firstOrFail()
            ->forceFill([
                'must_change_password' => false,
                'password_changed_at' => now(),
            ]);

        $this->actingAs($user);

        $routes = [
            'member-state.comparisons.index' => 'Performance',
            'member-state.communications.index' => 'Messages',
            'member-state.national-data.index' => 'Documents and raw data',
            'member-state.questions.index' => 'Help and feedback',
            'member-state.commodities.index' => 'Commodity reporting',
        ];

        foreach ($routes as $routeName => $breadcrumbLabel) {
            $this->get(route($routeName))
                ->assertOk()
                ->assertSee('Member State Portal')
                ->assertSee('Back to Dashboard')
                ->assertSee($breadcrumbLabel)
                ->assertSee('Help &amp; feedback', false)
                ->assertSee('Portal online')
                ->assertDontSee('id="menu-mini-button"', false);
        }
    }

    public function test_me_admin_can_open_the_member_state_reporting_cycle_configuration(): void
    {
        $admin = User::query()
            ->with(['role.permissions', 'permissions'])
            ->get()
            ->first(fn (User $user): bool => $user->hasPermission('me.configuration.manage'));

        if (! $admin) {
            $this->markTestSkipped('No M&E configuration administrator is available in the seeded database.');
        }

        $this->actingAs($admin)
            ->get(route('budget.me.member-state-reporting-cycles.index'))
            ->assertOk()
            ->assertSee('Member State Reporting Cycles')
            ->assertSee('Duplicate protection is automatic');

        $this->get(route('budget.me.member-state-reporting-cycles.create'))
            ->assertOk()
            ->assertSee('Quarterly')
            ->assertSee('Semi-Annual')
            ->assertSee('Annual');

        $frequency = ReportingFrequency::query()->where('code', 'QUARTERLY')->firstOrFail();
        $year = now()->year + 10;
        $periodNumber = collect(range(1, 4))->first(fn (int $period): bool => ! MemberStateReportingCycle::query()
            ->where('reporting_frequency_id', $frequency->id)
            ->where('period_key', $year.'-Q'.$period)
            ->exists());

        if (! $periodNumber) {
            $this->markTestSkipped('All available future quarterly test periods are already configured.');
        }

        $payload = [
            'reporting_frequency_id' => $frequency->id,
            'reporting_year' => $year,
            'period_number' => $periodNumber,
            'status' => MemberStateReportingCycle::STATUS_OPEN,
            'opens_at' => '',
            'closes_at' => '',
            'instructions' => 'Test reporting window.',
        ];

        $this->post(route('budget.me.member-state-reporting-cycles.store'), $payload)
            ->assertRedirect(route('budget.me.member-state-reporting-cycles.index'))
            ->assertSessionHasNoErrors();

        $createdCycle = MemberStateReportingCycle::query()
            ->where('reporting_frequency_id', $frequency->id)
            ->where('period_key', $year.'-Q'.$periodNumber)
            ->firstOrFail();

        $this->get(route('budget.me.member-state-reporting-cycles.edit', $createdCycle))
            ->assertOk()
            ->assertSee($createdCycle->display_label);

        $this->post(route('budget.me.member-state-reporting-cycles.store'), $payload)
            ->assertSessionHasErrors('period_number');

        $this->assertSame(1, MemberStateReportingCycle::query()
            ->where('reporting_frequency_id', $frequency->id)
            ->where('period_key', $year.'-Q'.$periodNumber)
            ->count());
    }
}
