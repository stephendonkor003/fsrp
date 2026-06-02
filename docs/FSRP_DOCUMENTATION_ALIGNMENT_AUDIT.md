# FSRP Documentation Alignment Audit

Source documentation reviewed from `D:\FSRP`:

- `AUC DRAFT FSRP_PROJECT IMPLEMENTATION MANUAL_AUDA 18062024 (2).docx`
- `Project Appraisal Document (PAD).docx`
- `AUC-RFW-RA-REVISED.xlsx`
- `Revised AU-NEPAD-FSRPMTR_TheoryofChange Final-MK.xlsx`

## Current Platform Baseline

- Laravel 12.59.0, PHP 8.5.6 CLI, PostgreSQL connection, database-backed cache/session/queue.
- Core modules already present: finance and budget planning, AWP/no-objection review, procurement lifecycle, M&E indicators and surveys, World Bank indicators, AU member-state data, partner/vendor/think-tank portals, HR, news, and audit logs.
- Routes are web-only in the Laravel bootstrap; `routes/api.php` is present but not wired.
- Test runner was missing `phpunit.xml.dist`; standalone smoke scripts existed under `tests/Smoke`.

## Documentation Requirements Mapped To App Areas

### Planning, Budgeting, and IFR

The PIM requires AWPB planning by component/output, procurement plan linkage, Steering Committee/AUC/WB approval, variance reporting, and semi-annual progress/IFR reporting. The app already has program/project/activity/sub-activity budgeting, approved work plan review, and IFR exports. Remaining gaps:

- Add explicit AWPB submission dates, WB no-objection dates, and approval milestones at plan level.
- Extend IFR outputs with designated-account activity, bank-statement attachment references, and expenditures subject/not subject to prior review.
- Add quarterly/semi-annual submission due-date tracking and late-report alerts.

### Procurement

The PAD/PIM require PPSD alignment, STEP updates, prior review/no-objection tracking, procurement risk monitoring, contract logbook records, and timely document uploads. First upgrade pass added these fields to procurement plans:

- `ppsd_reference`
- `step_plan_id`
- `step_plan_status`
- `step_last_uploaded_at`
- `prior_review_required`
- `world_bank_no_objection_status`
- `world_bank_no_objection_date`
- `procurement_risk_level`
- `contract_log_reference`
- `procurement_record_notes`

Remaining gaps:

- Add document attachments per STEP package and contract log reference.
- Add dashboard widgets for overdue STEP uploads, prior-review packages awaiting no-objection, and high-risk procurement items.
- Add annual/as-needed Procurement Plan update prompts.

### M&E and Results Framework

The PIM and workbooks require harmonized regional reporting tools, LOP target tracking, period target/achievement, performance percentage, remarks, and country/member-state breakdowns. The app already has indicator definitions, baselines, units, reporting frequencies, targets, actuals, surveys, and management exports. Remaining gaps:

- Add LOP target and disaggregation fields to indicator profiles.
- Add reporting-period target/achievement/percentage and LOP performance percentage to management exports.
- Seed the AUC PDO/results indicators from the PIM/PAD/workbook.
- Add country/member-state summaries for regional progress reports.

### Knowledge, Policy, and Component Taxonomy

The docs define AUC Phase 3 components/subcomponents around resilient production capacity, markets/trade, policy resilience, knowledge platforms, and project management. The app has general finance/procurement/M&E structures but does not yet enforce this FSRP component taxonomy. Remaining gaps:

- Add canonical FSRP component/subcomponent reference data.
- Link activities, work plans, procurement plans, indicators, and reports to the FSRP component/subcomponent taxonomy.
- Add knowledge-sharing mechanism tracking for conferences, platforms, policy products, and digital agriculture tools.

### Environmental and Social Safeguards / GRM

The PIM requires ESCP/ESS tracking, E&S instruments, stakeholder consultations, grievance management, screening, training, and monitoring. This is the largest missing first-class module. Remaining gaps:

- Add safeguards screening records for activities/procurements.
- Add ESCP action tracking with responsible parties, due dates, and evidence.
- Add GRM case intake, classification, resolution workflow, and reporting.
- Add stakeholder engagement log and disclosure records.

## Quality Issues Found During Review

- `NewsPublishedNotification` declared a `$queue` property that conflicts with Laravel's `Queueable` trait.
- `MethodPlannedImport.php` has a UTF-8 BOM before `<?php`, which breaks PHP linting.
- Finance AJAX routes were unnamed and collapsed into duplicate route names.
- Procurement plan edit did not submit `program_plan_id`, although the controller required it.
- Procurement plan forms used `notes`, but the controller/model persist `remarks`.
- `composer audit --no-dev` reports Symfony/Laravel advisory exposure; dependency patching should be handled in a controlled update pass.
- Public smoke tests contain stale ATTP/Think Tank copy expectations after the FSRP rebrand.

## Recommended Next Upgrade Pass

1. Run the new procurement migration and verify create/edit/show flows.
2. Add M&E LOP/disaggregation/reporting-period fields and update exports.
3. Seed the PDO/results framework indicators and FSRP component taxonomy.
4. Build safeguards/GRM as a first-class module.
5. Patch Composer dependencies and refresh stale smoke test expectations.
