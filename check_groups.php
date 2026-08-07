<?php

$groups = App\Models\Group::with(['leader', 'collector'])->get();
foreach ($groups as $g) {
    $leader = $g->leader ? $g->leader->first_name . ' ' . $g->leader->last_name : 'N/A';
    echo "Groupe: {$g->name} | Chef: {$leader}\n";
}
