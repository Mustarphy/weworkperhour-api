<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SmartGuidesContentSeeder extends Seeder
{
    public function run(): void
    {
        $guides = [
            [
                'guide_id' => 'va-beginner',
                'title' => 'SmartGuide™ – Beginner Virtual Assistant (VA)',
                'category' => 'VA',
                'level' => 'Beginner',
                'roleOverview' => 'Intro to VA work, basic admin tasks, communication support, and simple online tools.',
                'clientAcquisition' => json_encode([
                    "Target LinkedIn, WeWorkPerHour, beginner-friendly job boards (e.g., Fiverr, Upwork).",
                    "Send 5–10 personalised proposals daily.",
                    "Join 3 Facebook/LinkedIn groups for VA opportunities."
                ]),
                'pricingPackages' => json_encode([
                    "Start low to build a portfolio (e.g., £5–$10/hour or ₦5,000–₦8,000/hour).",
                    "Offer fixed packages (10 hours/month basic admin support)."
                ]),
                'toolsTemplates' => json_encode(["Google Workspace, Zoom, Canva.", "Starter templates: task tracker, basic invoice."]),
                'serviceStandards' => json_encode(["Respond within 24 hours.", "Send weekly progress updates."]),
                'quickWins' => json_encode([
                    "Create a SmartCV.",
                    "Offer the first client a discount in exchange for a testimonial.",
                    "Use AI to help draft emails quickly."
                ]),
                'aiAutomationTips' => json_encode(["Use ChatGPT for drafting responses.", "Automate scheduling with Calendly."]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guide_id' => 'va-advanced',
                'title' => 'SmartGuide™ – Advanced Virtual Assistant (VA)',
                'category' => 'VA',
                'level' => 'Advanced',
                'roleOverview' => 'Experienced VA handling complex tasks: project management, CRM, advanced research, and automation.',
                'clientAcquisition' => json_encode([
                    "Target high-paying clients on LinkedIn, via cold outreach, and premium platforms.",
                    "Leverage referrals and testimonials from past work."
                ]),
                'pricingPackages' => json_encode(["$20–$40/hour (₦15,000–₦25,000/hour).", "Offer premium retainers (40+ hours/month)."]),
                'toolsTemplates' => json_encode([
                    "Asana, Notion, Zapier integrations, and advanced Canva branding kits.",
                    "Templates: client onboarding forms, SOP guides, multi-project trackers."
                ]),
                'serviceStandards' => json_encode(["4–6 hour response time.", "Deliver work ahead of deadlines."]),
                'quickWins' => json_encode(["Run a LinkedIn content series sharing VA tips.", "Create a portfolio page with client results."]),
                'aiAutomationTips' => json_encode(["Use AI for summarising client meetings.", "Automate recurring workflows in Zapier or Make."]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guide_id' => 'editor-beginner',
                'title' => 'SmartGuide™ – Beginner Editor',
                'category' => 'Editor',
                'level' => 'Beginner',
                'roleOverview' => 'Focus on proofreading and basic grammar correction for small businesses and individuals.',
                'clientAcquisition' => json_encode([
                    "Target LinkedIn, WeWorkPerHour, student networks.",
                    "Offer free sample edits (up to 500 words) to attract first clients."
                ]),
                'pricingPackages' => json_encode(["$5–$10 per 1,000 words (₦5,000–₦8,000).", "Bundle: edit + basic formatting."]),
                'toolsTemplates' => json_encode(["Grammarly, Google Docs.", "Style guide checklist (APA, Chicago)."]),
                'serviceStandards' => json_encode(["24–48 hour turnaround for small jobs.", "One free revision included."]),
                'quickWins' => json_encode([
                    "Create portfolio samples by editing public domain texts.",
                    "Join writing communities for potential clients."
                ]),
                'aiAutomationTips' => json_encode(["Use AI grammar checks as a second layer after manual editing."]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'guide_id' => 'editor-advanced',
                'title' => 'SmartGuide™ – Advanced Editor',
                'category' => 'Editor',
                'level' => 'Advanced',
                'roleOverview' => 'A freelancer already established on WeWorkPerHour, aiming to scale, increase rates, and win premium clients.',
                'clientAcquisition' => json_encode([
                    "Leverage AI Matching for high-value leads.",
                    "Pitch to repeat clients for long-term contracts."
                ]),
                'pricingPackages' => json_encode([
                    "Increase rates by 20–50% based on portfolio strength.",
                    "Offer VIP packages (priority delivery, extra revisions, monthly retainers)."
                ]),
                'toolsTemplates' => json_encode(["CRM integration to track clients.", "Personalised proposal templates for faster pitching."]),
                'serviceStandards' => json_encode(["2–4 hour response time.", "Deliver work 1–2 days ahead of deadline."]),
                'quickWins' => json_encode([]),
                'aiAutomationTips' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('smart_guides_content')->insert($guides);
    }
}
