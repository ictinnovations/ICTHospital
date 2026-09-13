<?php
/**
 * ICTHospital - patient medical history.
 *
 * The table came across from the CodeIgniter system with no controller and no view,
 * so the history a clinician most wants before seeing someone was unreachable. Entries
 * are notes against a patient: an allergy, a past operation, a chronic condition, with
 * an optional scan or report attached.
 *
 * The patient name, address and phone are copied onto each entry. That looks like
 * duplication and it is deliberate, because the legacy schema does it and old records
 * still read correctly when a patient later moves house or changes their number. The
 * live patient record is still the one the screens link to.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\MedicalHistory;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicalHistoryController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $patientId = $request->input('patient');
        $term = trim((string) $request->input('q', ''));

        $entries = MedicalHistory::query()
            ->when($patientId, fn ($q) => $q->where('patient_id', $patientId))
            ->when($term !== '', function ($query) use ($term) {
                $like = '%' . $term . '%';
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('patient_name', 'like', $like);
            })
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('app.history.index', [
            'entries' => $entries,
            'term' => $term,
            'patientId' => $patientId,
            'patient' => $patientId ? Patient::find($patientId) : null,
            'patients' => Patient::orderBy('name')->get(['id', 'name', 'patient_id']),
        ]);
    }

    public function create(Request $request)
    {
        return view('app.history.form', [
            'entry' => new MedicalHistory([
                'patient_id' => $request->input('patient'),
                'date' => now()->toDateString(),
            ]),
            'patients' => Patient::orderBy('name')->get(['id', 'name', 'patient_id']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['img_url'] = $this->storeAttachment($request) ?? '';
        $data['registration_time'] = now();

        $entry = MedicalHistory::create($data);

        return redirect('/history?patient=' . $entry->patient_id)
            ->with('success', 'Added "' . $entry->title . '" to the history.');
    }

    public function edit($id)
    {
        return view('app.history.form', [
            'entry' => MedicalHistory::findOrFail($id),
            'patients' => Patient::orderBy('name')->get(['id', 'name', 'patient_id']),
        ]);
    }

    public function update(Request $request, $id)
    {
        $entry = MedicalHistory::findOrFail($id);
        $data = $this->validated($request);

        $attachment = $this->storeAttachment($request);
        if ($attachment !== null) {
            $data['img_url'] = $attachment;
        }

        $entry->update($data);

        return redirect('/history?patient=' . $entry->patient_id)->with('success', 'History entry updated.');
    }

    public function destroy($id)
    {
        $entry = MedicalHistory::findOrFail($id);
        $patientId = $entry->patient_id;
        $title = $entry->title;
        $entry->delete();

        return redirect('/history?patient=' . $patientId)->with('success', 'Removed "' . $title . '".');
    }

    /**
     * A history entry needs a patient, a title and something written down. Everything
     * else is optional, including the attachment and the date.
     */
    private function validated(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|integer|exists:patient,id',
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:10000',
            'date' => 'nullable|date',
            'attachment' => 'nullable|image|max:4096',
        ]);

        unset($data['attachment']);

        // validate() omits absent nullable keys, so this must not index directly.
        $data['date'] = $data['date'] ?? now()->toDateString();

        // The patient's details as they were when the note was written. The legacy
        // schema carries these and old entries should keep reading correctly after
        // someone moves house or changes their number.
        $patient = Patient::find($data['patient_id']);
        $data['patient_name'] = $patient->name ?? '';
        $data['patient_address'] = $patient->address ?? '';
        $data['patient_phone'] = $patient->phone ?? '';

        return $data;
    }

    private function storeAttachment(Request $request)
    {
        if (! $request->hasFile('attachment')) {
            return null;
        }

        return Storage::disk('public')->put('history', $request->file('attachment'));
    }
}
