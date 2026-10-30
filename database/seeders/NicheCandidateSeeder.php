<?php

namespace Database\Seeders;

use App\Models\NicheCandidate;
use Illuminate\Database\Seeder;

class NicheCandidateSeeder extends Seeder
{
    public function run(): void
    {
        NicheCandidate::updateOrCreate(
            ['slug' => 'electric-vehicles'],
            [
                'name' => 'Electric Vehicles (EV)',
                'description' => 'Edukasi kendaraan listrik, teknologi baterai, charging, perbandingan, kepemilikan, industri, dan perkembangan pasar.',
                'status' => 'candidate',
                'audience_profile' => [
                    'primary_demographic' => 'Curious buyers, tech enthusiasts, new EV owners',
                    'intent' => 'Practical, evidence-led EV explainers, maintenance cost comparisons, and tech guides',
                    'tone' => 'Informative, analytical, objective, evidence-based',
                ],
                'content_pillars' => [
                    'EV Battery Technology & Degradation',
                    'Ownership Cost & Maintenance',
                    'Charging Infrastructure & Myths vs Facts',
                    'EV vs Hybrid Comparisons',
                    'New EV Models & Market Developments',
                ],
            ]
        );

        NicheCandidate::updateOrCreate(
            ['slug' => 'sleep-content'],
            [
                'name' => 'Sleep Content',
                'description' => 'Original sleep soundscape, ambience, nature sound, white noise, relaxing visual loops, dan konten pendukung tidur.',
                'status' => 'candidate',
                'audience_profile' => [
                    'primary_demographic' => 'People seeking relaxation, focus, study background, or sleep aid',
                    'intent' => 'High quality immersive soundscapes and visual ambience without harsh transients',
                    'tone' => 'Calm, soothing, consistent, non-intrusive',
                ],
                'content_pillars' => [
                    'Rain & Thunderstorm Ambience',
                    'Ocean & Deep Water Waves',
                    'Forest Night & Wind Sounds',
                    'Fireplace & Cozy Cabin Atmosphere',
                    'Brown Noise & Dark-Screen Soundscapes',
                ],
            ]
        );
    }
}

