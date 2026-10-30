<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AuditLog;
use App\Models\NicheCandidate;

$ev = NicheCandidate::where('slug', 'electric-vehicles')->firstOrFail();
$ev->update(['status' => 'selected']);
NicheCandidate::where('id', '!=', $ev->id)->update(['status' => 'candidate']);

AuditLog::log(
    action: 'owner_select_pilot_niche',
    entityType: 'NicheCandidate',
    entityId: $ev->id,
    after: ['status' => 'selected', 'niche' => 'Electric Vehicles (EV)'],
    actorType: 'owner',
    actorId: 'Owner'
);

echo "SUCCESS: Electric Vehicles (EV) has been selected as the Pilot Niche.\n";
