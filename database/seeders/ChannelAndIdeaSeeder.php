<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Channel;
use App\Models\ContentIdea;
use App\Models\NicheCandidate;
use App\Models\ResearchDossier;
use Illuminate\Database\Seeder;

class ChannelAndIdeaSeeder extends Seeder
{
    public function run(): void
    {
        $ev = NicheCandidate::where('slug', 'electric-vehicles')->firstOrFail();

        // 1. Create or update Pilot Channel
        $channel = Channel::updateOrCreate(
            ['channel_name' => 'VoltWise: The EV Explainer'],
            [
                'niche_candidate_id' => $ev->id,
                'handle' => '@VoltWiseEV',
                'primary_language' => 'en',
                'target_region' => 'US',
                'timezone' => 'America/New_York',
                'default_visibility' => 'private',
                'status' => 'active',
                'brand_config' => [
                    'primary_color' => '#06b6d4',
                    'accent_color' => '#6366f1',
                    'background_color' => '#0f172a',
                    'font_family' => 'Plus Jakarta Sans',
                    'tone_of_voice' => 'Objective, analytical, evidence-backed, consumer-friendly',
                    'editorial_policy' => 'Every factual specification must cite manufacturer testing cycles (EPA/WLTP) or third-party verified telemetry.',
                    'disclaimer' => 'Independent automotive and battery tech educational explainers. No manufacturer sponsorship bias.',
                ],
            ]
        );

        // 2. Seed Initial Topic Ideas (FR-005)
        $ideas = [
            [
                'title_idea' => 'EV Battery Degradation: What 100,000 Miles Really Looks Like',
                'angle' => 'Comparing real-world fleet telemetry data against the common myth that EV batteries die after 5 years.',
                'audience_promise' => 'Clear, data-backed evidence on exactly how much range you lose after 5-8 years of daily driving.',
                'format' => 'long_form',
                'target_duration_seconds' => 660, // 11 mins
                'keyword_set' => ['ev battery degradation', 'electric car battery lifespan', 'ev battery replacement cost', 'lfp vs nmc battery'],
                'novelty_notes' => 'Uses verified fleet telemetry data from Geotab and Recurrent Auto instead of single-owner anecdotes.',
                'status' => 'accepted',
                'priority' => 'critical',
            ],
            [
                'title_idea' => 'The True Cost of Fast Charging vs Home Charging in 2026',
                'angle' => 'Financial breakdown: Why DC fast charging on road trips costs as much as gasoline, but home Level 2 charging saves $1,200/year.',
                'audience_promise' => 'A realistic cost calculator showing the break-even mileage for prospective EV buyers.',
                'format' => 'long_form',
                'target_duration_seconds' => 540, // 9 mins
                'keyword_set' => ['ev charging cost', 'supercharger vs home charging', 'electricity rates for ev', 'is ev cheaper than gas'],
                'novelty_notes' => 'Includes peak vs off-peak TOU (Time of Use) utility rate modeling across 5 key US states.',
                'status' => 'accepted',
                'priority' => 'high',
            ],
            [
                'title_idea' => 'LFP vs NMC: Which Battery Should You Actually Buy?',
                'angle' => 'Demystifying battery chemistry: Why charging to 100% every day is fine for LFP but harms NMC cells.',
                'audience_promise' => 'Know exactly which chemistry is in your car and how to charge it without stressing over battery health.',
                'format' => 'long_form',
                'target_duration_seconds' => 600, // 10 mins
                'keyword_set' => ['lfp vs nmc', 'lithium iron phosphate', 'ev battery chemistry explained', 'charging to 100 percent'],
                'novelty_notes' => 'Explains thermal runaway resistance differences and cold weather performance trade-offs.',
                'status' => 'backlog',
                'priority' => 'medium',
            ],
            [
                'title_idea' => 'Why Heat Kills EV Batteries Faster Than Freezing Winters [Shorts]',
                'angle' => 'Debunking winter range loss vs permanent summer capacity degradation in 45 seconds.',
                'audience_promise' => 'Understand the critical difference between temporary winter efficiency drop and irreversible heat degradation.',
                'format' => 'shorts',
                'target_duration_seconds' => 52,
                'keyword_set' => ['ev in winter', 'ev battery heat', 'ev range loss', 'electric car myth'],
                'novelty_notes' => 'High engagement myth-busting format with side-by-side animated temperature graphs.',
                'status' => 'accepted',
                'priority' => 'high',
            ],
            [
                'title_idea' => 'The 80% Charging Rule Explained in 30 Seconds [Shorts]',
                'angle' => 'Quick visual explanation of why EV charging slows down dramatically above 80%.',
                'audience_promise' => 'Save 20 minutes at every fast charging stop with one simple charging curve rule.',
                'format' => 'shorts',
                'target_duration_seconds' => 38,
                'keyword_set' => ['ev charging curve', 'why does ev slow charging at 80', 'fast charging tips'],
                'novelty_notes' => 'Uses the smartphone charging curve analogy with clear visual cues.',
                'status' => 'accepted',
                'priority' => 'medium',
            ],
        ];

        foreach ($ideas as $data) {
            $idea = ContentIdea::updateOrCreate(
                [
                    'channel_id' => $channel->id,
                    'title_idea' => $data['title_idea']
                ],
                array_merge($data, ['niche_candidate_id' => $ev->id])
            );

            // 3. For the first critical pilot idea, create the Research Dossier (FR-006)
            if ($data['priority'] === 'critical') {
                $idea->update(['status' => 'researching']);

                ResearchDossier::updateOrCreate(
                    ['content_idea_id' => $idea->id],
                    [
                        'research_summary' => 'Comprehensive empirical analysis of real-world electric vehicle battery degradation based on telemetry from over 30,000 consumer and commercial fleet vehicles. Findings indicate an average annual degradation rate of ~1.8% to 2.3%, meaning over 80% original capacity remains after 100,000 miles (8+ years). Liquid thermal management and LFP chemistry significantly outperform older passive-cooled designs.',
                        'source_ledger' => [
                            [
                                'source_id' => 'SRC-01',
                                'publisher' => 'Geotab Telematics Fleet Research',
                                'title' => 'To what extent does EV battery degradation affect real-world range over time?',
                                'url' => 'https://www.geotab.com/blog/ev-battery-health/',
                                'date' => '2025-06-12',
                                'type' => 'primary_fleet_telemetry',
                                'sample_size' => '10,000+ commercial and passenger vehicles across 21 EV models',
                            ],
                            [
                                'source_id' => 'SRC-02',
                                'publisher' => 'Recurrent Auto Battery Reports',
                                'title' => 'Real-World Used EV Battery Lifespan and Degradation Benchmarks',
                                'url' => 'https://www.recurrentauto.com/research/battery-degradation',
                                'date' => '2025-09-18',
                                'type' => 'secondary_consumer_dataset',
                                'sample_size' => '20,000+ active monitored US vehicles',
                            ],
                            [
                                'source_id' => 'SRC-03',
                                'publisher' => 'National Renewable Energy Laboratory (NREL)',
                                'title' => 'Impact of Fast Charging on Lithium-Ion Battery Life in Extreme Temperatures',
                                'url' => 'https://www.nrel.gov/docs/fy24osti/battery-thermal-life.pdf',
                                'date' => '2024-11-04',
                                'type' => 'government_laboratory_study',
                            ],
                            [
                                'source_id' => 'SRC-04',
                                'publisher' => 'U.S. Environmental Protection Agency (EPA)',
                                'title' => 'Federal Regulations for EV Battery Durability and Warranty Mandates (8 yr / 100,000 mi)',
                                'url' => 'https://www.epa.gov/regulations-emissions-vehicles-and-engines',
                                'date' => '2024-03-20',
                                'type' => 'regulatory_mandate',
                            ],
                        ],
                        'claim_ledger' => [
                            [
                                'claim_id' => 'CLM-01',
                                'claim' => 'The average modern EV loses between 1.8% and 2.3% of its total battery capacity per year under mixed climates.',
                                'source_ref' => 'SRC-01',
                                'confidence' => 0.96,
                                'verification_note' => 'Consistent across both Geotab and Recurrent datasets for post-2019 vehicles.',
                                'is_verified' => true,
                            ],
                            [
                                'claim_id' => 'CLM-02',
                                'claim' => 'Vehicles with active liquid thermal management degrade 40% slower than passive air-cooled packs like early Nissan Leafs.',
                                'source_ref' => 'SRC-01',
                                'confidence' => 0.98,
                                'verification_note' => 'Air-cooled packs showed 4.2% annual loss in hot climates vs 1.6% for liquid-cooled.',
                                'is_verified' => true,
                            ],
                            [
                                'claim_id' => 'CLM-03',
                                'claim' => 'Frequent DC fast charging (3+ times/week) increases degradation by less than 1.5% over 5 years compared to Level 2 home charging, provided the vehicle conditions the battery.',
                                'source_ref' => 'SRC-03',
                                'confidence' => 0.92,
                                'verification_note' => 'Thermal management pre-conditioning mitigates dendrite formation effectively.',
                                'is_verified' => true,
                            ],
                            [
                                'claim_id' => 'CLM-04',
                                'claim' => 'Federal law requires all EV batteries sold in the US to be warrantied against defects and capacity loss below 70% for at least 8 years or 100,000 miles.',
                                'source_ref' => 'SRC-04',
                                'confidence' => 1.0,
                                'verification_note' => 'Direct statutory mandate under Clean Air Act Title II.',
                                'is_verified' => true,
                            ],
                        ],
                        'risk_flags' => [
                            'Do not generalize battery longevity for pre-2018 air-cooled models without clearly stating the distinction.',
                            'Specify that extreme desert climates (ambient > 40°C) without garage parking can add ~0.5% annual degradation.',
                        ],
                        'reviewer_id' => 'Senior Content Editor',
                        'status' => 'needs_review',
                    ]
                );
            }
        }

        AuditLog::log(
            action: 'seed_pilot_channel_and_ideas',
            entityType: 'Channel',
            entityId: $channel->id,
            after: [
                'channel_name' => $channel->channel_name,
                'ideas_count' => count($ideas),
            ],
            actorType: 'system'
        );
    }
}
