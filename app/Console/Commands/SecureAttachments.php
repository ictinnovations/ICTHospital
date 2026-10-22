<?php
/**
 * ICTHospital - move medical history attachments off the public disk.
 *
 * Reports used to be stored under storage/app/public, which the web server serves to
 * anyone holding the link. New uploads go to the private disk; this moves the old ones
 * across so they open only through /history/{id}/attachment. Safe to run more than once.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Console\Commands;

use App\Http\Controllers\MedicalHistoryController;
use App\Models\MedicalHistory;
use Illuminate\Console\Command;

class SecureAttachments extends Command
{
    protected $signature = 'hospital:secure-attachments';

    protected $description = 'Move medical history attachments from the public disk to the private disk';

    public function handle(): int
    {
        $moved = 0;
        $missing = 0;
        MedicalHistory::query()->where('img_url', '<>', '')->whereNotNull('img_url')
            ->orderBy('id')->each(function ($entry) use (&$moved, &$missing) {
                if (MedicalHistoryController::makePrivate($entry->img_url)) {
                    $moved++;
                } elseif (! \Illuminate\Support\Facades\Storage::disk('local')->exists($entry->img_url)) {
                    $missing++;
                }
            });

        $this->info("Moved {$moved} attachment(s) to private storage." . ($missing ? " {$missing} file(s) were not found on either disk." : ''));

        return self::SUCCESS;
    }
}
