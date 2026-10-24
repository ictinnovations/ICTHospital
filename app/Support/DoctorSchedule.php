<?php
/**
 * ICTHospital - when a doctor can be booked.
 *
 * A doctor with no weekly slots set up can be booked at any time, exactly as before,
 * so an install that never touches this screen behaves the same. Once a doctor has
 * slots, a booking has to fit inside one of that weekday's windows. Blocked dates
 * (leave, a half day) apply either way.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Support;

use App\Models\Appointment;
use App\Models\DoctorSlot;
use App\Models\SlotBlock;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class DoctorSchedule
{
    public static function assertBookable($doctorId, Carbon $start, Carbon $end)
    {
        if ($block = self::blockFor($doctorId, $start, $end)) {
            throw ValidationException::withMessages([
                'start' => 'The doctor is not available then' . ($block->reason ? ' (' . $block->reason . ')' : '') . '.',
            ]);
        }

        $all = DoctorSlot::where('doctor_id', $doctorId)->where('active', true)->get();
        if ($all->isEmpty()) {
            return;
        }

        $day = $all->where('weekday', (int) $start->dayOfWeek);
        foreach ($day as $slot) {
            $from = Carbon::parse($start->toDateString() . ' ' . $slot->start_time);
            $to = Carbon::parse($start->toDateString() . ' ' . $slot->end_time);
            if ($start->gte($from) && $end->lte($to)) {
                return;
            }
        }

        $hours = $day->map(fn ($s) => $s->start_time . '-' . $s->end_time)->implode(', ');
        throw ValidationException::withMessages([
            'start' => $hours === ''
                ? 'The doctor has no clinic on ' . $start->format('l') . 's.'
                : 'Outside the doctor\'s clinic hours on ' . $start->format('l') . ' (' . $hours . ').',
        ]);
    }

    /** Free start times for a doctor on a date, from the weekly slots, minus bookings and blocks. */
    public static function freeSlots($doctorId, $date)
    {
        $day = Carbon::parse($date);
        $slots = DoctorSlot::where('doctor_id', $doctorId)->where('active', true)->where('weekday', (int) $day->dayOfWeek)->orderBy('start_time')->get();

        $booked = Appointment::where('doctor', $doctorId)->whereDate('date', $day->toDateString())
            ->whereNotIn('status', ['cancelled', 'no show'])->get(['s_time', 'e_time']);

        $free = [];
        foreach ($slots as $slot) {
            $t = Carbon::parse($day->toDateString() . ' ' . $slot->start_time);
            $stop = Carbon::parse($day->toDateString() . ' ' . $slot->end_time);
            while ($t->copy()->addMinutes($slot->slot_minutes)->lte($stop)) {
                $end = $t->copy()->addMinutes($slot->slot_minutes);
                $taken = $booked->first(fn ($b) => $b->s_time < $end->toDateTimeString() && $b->e_time > $t->toDateTimeString());
                if (! $taken && ! self::blockFor($doctorId, $t, $end)) {
                    $free[] = ['start' => $t->format('H:i'), 'minutes' => $slot->slot_minutes];
                }
                $t = $end;
            }
        }

        return $free;
    }

    private static function blockFor($doctorId, Carbon $start, Carbon $end)
    {
        foreach (SlotBlock::where('doctor_id', $doctorId)->whereDate('date', $start->toDateString())->get() as $b) {
            if (! $b->start_time || ! $b->end_time) {
                return $b;
            }
            $from = Carbon::parse($start->toDateString() . ' ' . $b->start_time);
            $to = Carbon::parse($start->toDateString() . ' ' . $b->end_time);
            if ($start->lt($to) && $end->gt($from)) {
                return $b;
            }
        }

        return null;
    }
}
