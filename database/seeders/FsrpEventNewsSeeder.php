<?php

namespace Database\Seeders;

use App\Models\NewsPost;
use Illuminate\Database\Seeder;

class FsrpEventNewsSeeder extends Seeder
{
    public function run(): void
    {
        $publishedAt = now()->subDay();

        $events = [
            [
                'slug' => 'launch-of-fsrp-call-for-proposals',
                'title' => 'Launch of FSRP Call for Proposals',
                'excerpt' => 'This webinar introduced the FSRP Call for Proposals and provided an overview of the eligibility criteria, submission guidelines, and key objectives of the FSRP initiative.',
                'body' => '<p>This webinar introduced the FSRP Call for Proposals and provided an overview of the eligibility criteria, submission guidelines, and key objectives of the FSRP initiative.</p><p><a href="https://drive.google.com/file/u/0/d/1cPV1APFR0zB5rSvNL9PvISpNXHY9LcDr/view?usp=sharing&amp;pli=1" target="_blank" rel="noopener">View recording</a></p>',
                'published_at' => '2025-07-24 14:00:00',
                'tags' => ['webinar', 'call for proposals', 'completed'],
            ],
            [
                'slug' => 'follow-up-webinar-consortium-application-guidance',
                'title' => 'Follow-up Webinar: Consortium Application Guidance',
                'excerpt' => 'This webinar provided an overview of the FSRP Consortium Application Form and guidance on navigating the FSRP website, clarifying eligibility requirements and consortium formation.',
                'body' => '<p>This webinar provided an overview of the FSRP Consortium Application Form and guidance on navigating the FSRP website, clarifying eligibility requirements and consortium formation.</p><p><a href="https://drive.google.com/file/d/1gSqmT-U2guRVa7FNSfdvLp7RHS45L0Za/view" target="_blank" rel="noopener">View recording</a></p>',
                'published_at' => '2025-08-05 14:00:00',
                'tags' => ['webinar', 'application guidance', 'completed'],
            ],
            [
                'slug' => 'follow-up-webinar-budget-templates-and-commitment-letter',
                'title' => 'Follow-up Webinar: Budget, Templates & Commitment Letter',
                'excerpt' => 'This session provided additional guidance on proposal development, focusing on the budget and timeline template, CV template, past research and experience template, and the commitment letter.',
                'body' => '<p>This session provided additional guidance on proposal development, focusing on the budget and timeline template, CV template, past research and experience template, and the commitment letter.</p><p><a href="https://drive.google.com/file/d/1EzbZ7jbsf6I3FTM1urC9RG_Onld-EGjF/view" target="_blank" rel="noopener">View recording</a></p>',
                'published_at' => '2025-08-26 14:00:00',
                'tags' => ['webinar', 'budget', 'templates', 'completed'],
            ],
            [
                'slug' => 'follow-up-webinar-applicant-q-and-a-session',
                'title' => 'Follow-up Webinar: Applicant Q&A Session',
                'excerpt' => 'This webinar was conducted to address key applicant questions and provide additional clarification to support submission readiness.',
                'body' => '<p>This webinar was conducted to address key applicant questions and provide additional clarification to support submission readiness.</p><p><a href="https://drive.google.com/file/d/1LQzkyAG6ITBIRZqzLK7jEiyxD45MpsVT/view" target="_blank" rel="noopener">View recording</a></p>',
                'published_at' => '2025-09-08 14:00:00',
                'tags' => ['webinar', 'questions', 'completed'],
            ],
            [
                'slug' => 'final-follow-up-webinar-submission-deadline-preparation',
                'title' => 'Final Follow-up Webinar: Submission Deadline Preparation',
                'excerpt' => 'This webinar focused on addressing final questions and preparing applicants for the submission deadline on September 24, 2025.',
                'body' => '<p>This webinar focused on addressing final questions and preparing applicants for the submission deadline on September 24, 2025.</p>',
                'published_at' => '2025-09-23 14:00:00',
                'tags' => ['webinar', 'submission deadline', 'completed'],
            ],
            [
                'slug' => 'watch-our-guide-on-how-to-apply',
                'title' => 'Watch Our Guide on How to Apply',
                'excerpt' => 'Video walkthroughs are available in English and French to help applicants complete their application.',
                'body' => '<p>Video walkthroughs are available in English and French to help applicants complete their application.</p><p><a href="https://drive.google.com/file/d/1oFGoh93O1MnoB9bdBhQHaWn4mZlHu5ra/view" target="_blank" rel="noopener">How to Apply - English</a></p><p><a href="https://drive.google.com/file/d/19bb8Gx5SICNeZKpFAP2XUPDZH9lre-9I/view" target="_blank" rel="noopener">How to Apply - French</a></p>',
                'published_at' => $publishedAt->toDateTimeString(),
                'tags' => ['application guide', 'resource'],
            ],
            [
                'slug' => 'response-to-clarification-questions',
                'title' => 'Response to Clarification Questions',
                'excerpt' => 'For all clarification questions submitted, please refer to the updated Frequently Asked Questions page.',
                'body' => '<p>For all clarification questions submitted, please refer to the updated Frequently Asked Questions page.</p><p><a href="/faq">Visit FSRP FAQ page</a></p>',
                'published_at' => $publishedAt->subMinute()->toDateTimeString(),
                'tags' => ['faq', 'resource'],
            ],
        ];

        foreach ($events as $event) {
            NewsPost::updateOrCreate(
                ['slug' => $event['slug']],
                [
                    'title' => $event['title'],
                    'category' => 'events',
                    'excerpt' => $event['excerpt'],
                    'body' => $event['body'],
                    'status' => 'published',
                    'tags' => $event['tags'],
                    'submitted_at' => $event['published_at'],
                    'approved_at' => $event['published_at'],
                    'published_at' => $event['published_at'],
                ]
            );
        }
    }
}
