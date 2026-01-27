<?php

namespace App\Observers;

use App\Models\AdjustmentDetail;
use App\Models\Adjustment;
use App\Services\LedgerService;

class AdjustmentDetailObserver
{
    /**
     * When an AdjustmentDetail is created, log to the product ledger
     */
    public function created(AdjustmentDetail $detail)
    {
        // Get parent adjustment for ref
        $adjustment = Adjustment::find($detail->adjustment_id);

        if (!$adjustment) return;

        $in = $detail->type === 'add' ? $detail->quantity : 0;
        $out = $detail->type === 'sub' ? $detail->quantity : 0;

        LedgerService::log(
            $detail->product_id,
            'adjustment',
            $adjustment->Ref ?? 'ADJ#UNKNOWN',
            $in,
            $out
        );
    }
}
