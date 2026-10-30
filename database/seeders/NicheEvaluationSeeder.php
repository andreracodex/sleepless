<?php

namespace Database\Seeders;

use App\Models\NicheCandidate;
use App\Services\NicheEvaluationService;
use Illuminate\Database\Seeder;

class NicheEvaluationSeeder extends Seeder
{
    public function run(NicheEvaluationService $evalService): void
    {
        $ev = NicheCandidate::where('slug', 'electric-vehicles')->first();
        $sleep = NicheCandidate::where('slug', 'sleep-content')->first();

        if ($ev && $ev->latestVerifiedSnapshot) {
            $evalService->evaluate(
                $ev,
                $ev->latestVerifiedSnapshot,
                85, // feasibility
                85, // audience
                75, // economics
                85, // diversification
                'Initial PRD Review'
            );
        }

        if ($sleep && $sleep->latestVerifiedSnapshot) {
            $evalService->evaluate(
                $sleep,
                $sleep->latestVerifiedSnapshot,
                80, // feasibility
                80, // audience
                70, // economics
                65, // diversification
                'Initial PRD Review'
            );
        }
    }
}
