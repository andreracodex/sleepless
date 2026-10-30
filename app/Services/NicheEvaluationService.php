<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\NicheCandidate;
use App\Models\NicheEvaluation;
use App\Models\NicheScoreSnapshot;
use InvalidArgumentException;

class NicheEvaluationService
{
    /**
     * Evaluate a candidate niche based on its verified snapshot and internal review scores
     */
    public function evaluate(
        NicheCandidate $candidate,
        NicheScoreSnapshot $snapshot,
        float $feasibility,
        float $audienceValidation,
        float $productionEconomics,
        float $monetizationDiversification,
        ?string $evaluatedBy = 'Reviewer'
    ): NicheEvaluation {
        if (!$snapshot->isVerified()) {
            throw new InvalidArgumentException("Cannot evaluate niche with unverified snapshot status ({$snapshot->verification_status}).");
        }

        $nicheScore = (float) ($snapshot->niche_score ?? 0);

        $internalScore = NicheEvaluation::calculateInternalScore(
            $nicheScore,
            $feasibility,
            $audienceValidation,
            $productionEconomics,
            $monetizationDiversification
        );

        $decision = 'pending';
        $rationale = '';

        if ($internalScore >= 70 && $nicheScore >= 70) {
            $decision = 'recommended';
            $rationale = "Kandidat sangat kuat dengan CollabPals Niche Score {$nicheScore} dan skor internal {$internalScore}. Layak menjadi pilot channel.";
        } elseif ($internalScore >= 60) {
            $decision = 'pending';
            $rationale = "Skor internal {$internalScore}. Layak diuji jika format editorial memiliki diferensiasi tinggi.";
        } else {
            $decision = 'rejected';
            $rationale = "Skor internal {$internalScore} di bawah ambang batas minimum kelayakan (60).";
        }

        $evaluation = NicheEvaluation::create([
            'niche_candidate_id' => $candidate->id,
            'niche_score_snapshot_id' => $snapshot->id,
            'feasibility_score' => (int) $feasibility,
            'audience_validation_score' => (int) $audienceValidation,
            'production_economics_score' => (int) $productionEconomics,
            'monetization_diversification_score' => (int) $monetizationDiversification,
            'internal_score' => $internalScore,
            'decision' => $decision,
            'rationale' => $rationale,
            'evaluated_by' => $evaluatedBy,
            'evaluated_at' => now(),
        ]);

        AuditLog::log(
            action: 'evaluate_niche_decision',
            entityType: 'NicheEvaluation',
            entityId: $evaluation->id,
            after: [
                'candidate' => $candidate->slug,
                'internal_score' => $internalScore,
                'decision' => $decision,
            ],
            actorType: 'reviewer',
            actorId: $evaluatedBy
        );

        return $evaluation;
    }

    /**
     * Compare EV vs Sleep Content and produce comparative recommendation
     */
    public function compare(NicheCandidate $ev, NicheCandidate $sleep): array
    {
        $evSnapshot = $ev->latestVerifiedSnapshot;
        $sleepSnapshot = $sleep->latestVerifiedSnapshot;

        $evEval = $ev->latestEvaluation;
        $sleepEval = $sleep->latestEvaluation;

        $evScore = $evSnapshot?->niche_score;
        $sleepScore = $sleepSnapshot?->niche_score;

        $diff = null;
        $recommendation = 'Menunggu data skor terverifikasi untuk kedua kandidat.';

        if ($evScore !== null && $sleepScore !== null) {
            $diff = abs($evScore - $sleepScore);
            if ($diff >= 5) {
                $winner = ($evScore > $sleepScore) ? $ev : $sleep;
                $loser = ($evScore > $sleepScore) ? $sleep : $ev;
                $recommendation = "Niche [{$winner->name}] unggul {$diff} poin di atas [{$loser->name}] (selisih \u{2265} 5 poin). Direkomendasikan sebagai kandidat utama pilot channel sesuai aturan PRD Section 3.4.";
            } else {
                $recommendation = "Selisih skor sangat ketat (< 5 poin, selisih {$diff}). Disarankan melakukan pilot editorial 3 video per niche sebelum keputusan akhir.";
            }
        }

        return [
            'ev' => [
                'candidate' => $ev,
                'snapshot' => $evSnapshot,
                'evaluation' => $evEval,
            ],
            'sleep' => [
                'candidate' => $sleep,
                'snapshot' => $sleepSnapshot,
                'evaluation' => $sleepEval,
            ],
            'score_difference' => $diff,
            'recommendation' => $recommendation,
        ];
    }
}
