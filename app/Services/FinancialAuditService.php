<?php

namespace App\Services;

use App\Models\FinancialLog;
use Illuminate\Support\Facades\Auth;

class FinancialAuditService
{
    public static function log(
        string $action,
        string $entityType,
        int $entityId,
        array $metadata = []
    ) {
        FinancialLog::create([
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'metadata'    => $metadata,
            'user_id'     => Auth::id(),
        ]);
    }
}
