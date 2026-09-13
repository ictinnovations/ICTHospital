<?php
/**
 * ICTHospital - nurses, pharmacists, laboratory staff, receptionists and accountants.
 *
 * Five tables of the same shape, so one controller drives all of them from
 * config/hospital_staff.php rather than five near-copies that drift the first time a
 * column is added to one. The type arrives as a route segment and is looked up in that
 * map; anything not in the map is a 404 rather than a guessed table name.
 *
 * Doctors are not here. They carry a department, a qualification and an ICTCore
 * procedure id, and the clinical screens select from them, so they keep their own
 * controller.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Support\Permissions;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StaffController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, $type)
    {
        $meta = $this->guard($type, 'view');
        $term = trim((string) $request->input('q', ''));

        $staff = $meta['model']::query()
            ->when($term !== '', function ($query) use ($term) {
                $like = '%' . $term . '%';
                $query->where('name', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('email', 'like', $like);
            })
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('app.staff.index', compact('staff', 'term', 'type', 'meta'));
    }

    public function create($type)
    {
        $meta = $this->guard($type, 'add');

        return view('app.staff.form', [
            'person' => new $meta['model'](),
            'type' => $type,
            'meta' => $meta,
        ]);
    }

    public function store(Request $request, $type)
    {
        $meta = $this->guard($type, 'add');
        $data = $this->validated($request, $meta);
        $data['img_url'] = $this->storePhoto($request, $type) ?? '';

        $person = $meta['model']::create($data);

        return redirect('/staff/' . $type)
            ->with('success', $person->name . ' added.');
    }

    public function edit($type, $id)
    {
        $meta = $this->guard($type, 'update');

        return view('app.staff.form', [
            'person' => $meta['model']::findOrFail($id),
            'type' => $type,
            'meta' => $meta,
        ]);
    }

    public function update(Request $request, $type, $id)
    {
        $meta = $this->guard($type, 'update');
        $person = $meta['model']::findOrFail($id);
        $data = $this->validated($request, $meta, $person->id);

        $photo = $this->storePhoto($request, $type);
        if ($photo !== null) {
            $data['img_url'] = $photo;
        }

        $person->update($data);

        return redirect('/staff/' . $type)->with('success', $person->name . ' updated.');
    }

    /**
     * Staff rows are not referenced by the clinical tables the way doctors are, so
     * unlike the doctor register there is nothing here to orphan by deleting.
     */
    public function destroy($type, $id)
    {
        $meta = $this->guard($type, 'delete');
        $person = $meta['model']::findOrFail($id);
        $name = $person->name;
        $person->delete();

        return redirect('/staff/' . $type)->with('success', 'Removed ' . $name . '.');
    }

    /**
     * Resolve the type and check the permission for this action in one step.
     *
     * Returning a response from a private helper is not possible, so a denial is
     * thrown as an HTTP exception that Laravel turns into the redirect. The prefix
     * comes from config: nurses have their own catalogue entries, the other four
     * share the general Staff ones.
     */
    private function guard($type, $action)
    {
        $meta = $this->meta($type);

        if (! Permissions::allows($meta['permission'] . '_' . $action)) {
            throw new HttpResponseException(
                redirect('/no-permission')->with('denied', $meta['permission'] . '_' . $action)
            );
        }

        return $meta;
    }

    /** The config row for a type, or a 404. Never trust the segment as a table name. */
    private function meta($type)
    {
        $meta = config('hospital_staff.' . $type);

        if (! $meta) {
            throw new NotFoundHttpException('Unknown staff type: ' . $type);
        }

        return $meta;
    }

    /**
     * Same rule as the doctor register and the patient form: only the name is
     * required. Someone starting on Monday has a name and nothing else on file, and a
     * form that refuses that record gets worked around with junk data.
     */
    private function validated(Request $request, array $meta, $ignoreId = null)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:100',
            'photo' => 'nullable|image|max:2048',
        ]);

        unset($data['photo']);

        if (! empty($data['email'])) {
            $clash = $meta['model']::where('email', $data['email'])
                ->when($ignoreId, fn ($q) => $q->where('id', '<>', $ignoreId))
                ->exists();

            if ($clash) {
                throw ValidationException::withMessages([
                    'email' => 'Someone on this list already has that email address.',
                ]);
            }
        }

        foreach (['email', 'phone', 'address'] as $key) {
            $data[$key] = $data[$key] ?? '';
        }

        // Legacy NOT NULL filler columns. The CodeIgniter application wrote empty
        // strings into these; x, y and z are unused layout leftovers and only some
        // of the five tables have all three, so each is set only where it exists.
        $data['ion_user_id'] = '';
        $table = (new $meta['model']())->getTable();
        foreach (['x', 'y', 'z'] as $column) {
            if (Schema::hasColumn($table, $column)) {
                $data[$column] = '';
            }
        }

        return $data;
    }

    private function storePhoto(Request $request, $type)
    {
        if (! $request->hasFile('photo')) {
            return null;
        }

        return Storage::disk('public')->put('staff/' . $type, $request->file('photo'));
    }
}
