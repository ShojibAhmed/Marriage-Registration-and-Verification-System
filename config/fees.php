<?php
/**
 * Marriage & Divorce Fee Schedule Configuration
 * Academic Prototype Demonstration Model
 * 
 * DISCLAIMER:
 * The fee rates defined herein represent proposed academic demonstration models
 * and are subject to verification against the latest official statutory rules of Bangladesh.
 * They are not represented as officially verified government fees.
 */

// Configuration Constants for Academic Demonstration
define('PROPOSED_MUSLIM_FEE_RATE_PER_THOUSAND', 14.00); // ৳14 per ৳1,000 or part thereof of Denmohor
define('PROPOSED_MUSLIM_MIN_FEE', 200.00);              // Minimum proposed filing fee
define('PROPOSED_HINDU_MARRIAGE_FEE', 1000.00);          // Proposed fixed fee for Hindu marriage registration
define('PROPOSED_OTHER_MARRIAGE_FEE', 1000.00);          // Proposed fixed fee for Christian / other marriages
define('PROPOSED_DIVORCE_SERVICE_FEE', 1000.00);         // Proposed service fee for divorce recording/notice
define('APPLICABLE_TAX_PERCENT', 0.00);                  // 0.00% (No unverified tax is invented or charged)

/**
 * Calculate marriage registration fee based on religion and Denmohor.
 * 
 * @param string $religion ('Muslim', 'Hindu', 'Christian', 'Buddhist', 'Other')
 * @param float $denmohor_amount Applicable Denmohor amount in BDT
 * @return array Associative array with 'fee_amount', 'tax_amount', 'total_amount', 'note'
 */
function calculate_marriage_registration_fee($religion, $denmohor_amount = 0.0) {
    $religion = ucfirst(strtolower(trim($religion)));
    $fee = 0.00;
    $note = "";

    if ($religion === 'Muslim') {
        $denmohor = max(0.0, floatval($denmohor_amount));
        if ($denmohor <= 0) {
            $fee = PROPOSED_MUSLIM_MIN_FEE;
            $note = "Minimum proposed filing fee applied (Denmohor is ৳0.00).";
        } else {
            // Rate: ৳14 per ৳1,000 or part thereof
            $units = ceil($denmohor / 1000.0);
            $calculated = $units * PROPOSED_MUSLIM_FEE_RATE_PER_THOUSAND;
            $fee = max($calculated, PROPOSED_MUSLIM_MIN_FEE);
            $note = "Calculated at proposed rate of ৳" . PROPOSED_MUSLIM_FEE_RATE_PER_THOUSAND . " per ৳1,000 of Denmohor (" . $units . " units).";
        }
    } elseif ($religion === 'Hindu') {
        $fee = PROPOSED_HINDU_MARRIAGE_FEE;
        $note = "Fixed proposed academic registration fee for Hindu marriage solemnization.";
    } else {
        $fee = PROPOSED_OTHER_MARRIAGE_FEE;
        $note = "Standard fixed proposed academic fee for Special/Other marriage solemnization.";
    }

    $tax = ($fee * APPLICABLE_TAX_PERCENT) / 100.0;
    $total = $fee + $tax;

    return [
        'fee_amount' => round($fee, 2),
        'tax_amount' => round($tax, 2),
        'total_amount' => round($total, 2),
        'note' => $note,
        'is_official' => false
    ];
}

/**
 * Calculate divorce service fee.
 * 
 * @param string $category
 * @return array
 */
function calculate_divorce_service_fee($category = 'General') {
    $fee = PROPOSED_DIVORCE_SERVICE_FEE;
    $tax = ($fee * APPLICABLE_TAX_PERCENT) / 100.0;
    $total = $fee + $tax;

    return [
        'fee_amount' => round($fee, 2),
        'tax_amount' => round($tax, 2),
        'total_amount' => round($total, 2),
        'note' => "Proposed fixed academic service fee for recording divorce notice/procedure.",
        'is_official' => false
    ];
}
