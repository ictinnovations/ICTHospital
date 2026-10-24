<?php
/**
 * ICTHospital - how a payment was made, and the buckets cash closing totals into.
 *
 * invoice_payment.method has always been free text, so older rows hold whatever
 * was typed. bucket() maps anything to one of the five totals, so an old "cash" or
 * "CASH" still lands in the cash column and nothing falls out of the close.
 *
 * A write-off is recorded as a payment row so the invoice settles, but it is not
 * money and is kept out of every collection total.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Support;

class PaymentModes
{
    public const MODES = ['Cash', 'Bank transfer', 'Card', 'Cheque', 'Online'];

    public const WRITE_OFF = 'Write-off';

    /** cash, bank_transfer, card, cheque or other. */
    public static function bucket($method)
    {
        $m = strtolower(trim((string) $method));

        if ($m === '' || $m === 'cash') {
            return 'cash';
        }
        if (strpos($m, 'bank') !== false || strpos($m, 'transfer') !== false) {
            return 'bank_transfer';
        }
        if (strpos($m, 'card') !== false || strpos($m, 'pos') !== false) {
            return 'card';
        }
        if (strpos($m, 'cheque') !== false || strpos($m, 'check') !== false) {
            return 'cheque';
        }

        return 'other';
    }

    public static function isWriteOff($method)
    {
        return strcasecmp(trim((string) $method), self::WRITE_OFF) === 0;
    }

    public static function labels()
    {
        return ['cash' => 'Cash', 'bank_transfer' => 'Bank transfer', 'card' => 'Card', 'cheque' => 'Cheque', 'other' => 'Other'];
    }
}
