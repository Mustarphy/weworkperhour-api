<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SmartGuideSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $guides = [
            [
                'guide_id' => 'va-beginner',
                'title' => 'SmartGuide™ – Beginner Virtual Assistant (VA)',
                'category' => 'VA',
                'level' => 'Beginner',
                'roleOverview' => 'Intro to VA work, basic admin tasks, communication support, and simple online tools.',
                'clientAcquisition' => json_encode([
                    "Target LinkedIn, WeWorkPerHour, beginner-friendly job boards",
                    "Send 5–10 personalised proposals daily",
                    "Join 3 Facebook/LinkedIn groups for VA opportunities"
                ]),
                'pricingPackages' => json_encode([
                    "Start low to build a portfolio",
                    "Offer fixed packages"
                ]),
                'toolsTemplates' => json_encode(["Google Workspace, Zoom, Canva.", "Starter templates: task tracker, basic invoice."]),
                'serviceStandards' => json_encode(["Respond within 24 hours.", "Send weekly progress updates."]),
                'quickWins' => json_encode(["Create a SmartCV.", "Offer the first client a discount.", "Use AI to help draft emails quickly."]),
                'aiAutomationTips' => json_encode(["Use ChatGPT for drafting responses.", "Automate scheduling with Calendly."]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'guide_id' => 'va-advanced',
                'title' => 'SmartGuide™ – Advanced Virtual Assistant (VA)',
                'category' => 'VA',
                'level' => 'Advanced',
                'roleOverview' => 'Experienced VA handling complex tasks: project management, CRM, advanced research, and automation.',
                'clientAcquisition' => json_encode([
                    "Target high-paying clients on LinkedIn, cold outreach, premium platforms",
                    "Leverage referrals and testimonials from past work"
                ]),
                'pricingPackages' => json_encode([
                    "$20–$40/hour",
                    "Offer premium retainers"
                ]),
                'toolsTemplates' => json_encode(["Asana, Notion, Zapier integrations", "Templates: client onboarding forms, SOP guides"]),
                'serviceStandards' => json_encode(["4–6 hour response time.", "Deliver work ahead of deadlines."]),
                'quickWins' => json_encode(["Run a LinkedIn content series", "Create a portfolio page with client results."]),
                'aiAutomationTips' => json_encode(["Use AI for summarising client meetings.", "Automate recurring workflows in Zapier or Make."]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('smart_guides_content')->insert($guides);
    }
}
