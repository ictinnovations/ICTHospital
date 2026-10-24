{{--
  ICTHospital - patient registration and edit form.

  One form for both, because the fields are identical and two near-copies drift
  apart the moment a column is added.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
@stop
@section('content')

    @php($editing = (bool) $patient->id)

    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-user"></i>
                        {{ $editing ? 'Edit patient ' . $patient->patient_id : 'Register patient' }}
                    </h2>
                    <div class="box-icon">
                        <a href="{{ url('/patients') }}" class="btn btn-default btn-sm">Back to list</a>
                    </div>
                </div>
                <div class="box-content">

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>Check the form.</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form role="form" method="post" enctype="multipart/form-data"
                          action="{{ $editing ? url('/patients/' . $patient->id) : url('/patients') }}">
                        {{ csrf_field() }}
                        @if ($editing)
                            <input type="hidden" name="_method" value="PUT">
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required
                                           value="{{ old('name', $patient->name) }}">
                                </div>
                                <div class="form-group">
                                    <label>Father or guardian name</label>
                                    <input type="text" name="father_name" class="form-control"
                                           value="{{ old('father_name', $patient->father_name) }}">
                                </div>
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-control"
                                           value="{{ old('phone', $patient->phone) }}">
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control"
                                           value="{{ old('email', $patient->email) }}">
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="text" name="address" class="form-control"
                                           value="{{ old('address', $patient->address) }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date of birth</label>
                                    <input type="date" name="birthdate" class="form-control"
                                           value="{{ old('birthdate', $patient->birthdate) }}">
                                    <span class="help-block">Age is worked out from this, so it never goes stale.</span>
                                </div>
                                <div class="form-group">
                                    <label>Sex</label>
                                    <select name="sex" class="form-control">
                                        <option value="">Not recorded</option>
                                        @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('sex', $patient->sex) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Blood group</label>
                                    <select name="bloodgroup" class="form-control">
                                        <option value="">Not recorded</option>
                                        @foreach ($bloodGroups as $group)
                                            <option value="{{ $group }}"
                                                {{ old('bloodgroup', $patient->bloodgroup) === $group ? 'selected' : '' }}>{{ $group }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Assigned doctor</label>
                                    <select name="doctor" class="form-control">
                                        <option value="">Not assigned</option>
                                        @foreach ($doctors as $name)
                                            <option value="{{ $name }}"
                                                {{ old('doctor', $patient->doctor) === $name ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Photo</label>
                                    <input type="file" name="photo" accept="image/*">
                                    <input type="hidden" name="photo_data" id="photo_data">
                                    <div style="margin-top:6px">
                                        <button type="button" class="btn btn-default btn-sm" id="cam-start"><i class="glyphicon glyphicon-camera"></i> Take photo with webcam</button>
                                        <button type="button" class="btn btn-primary btn-sm" id="cam-snap" style="display:none">Capture</button>
                                    </div>
                                    <video id="cam-video" autoplay playsinline style="display:none;max-width:240px;margin-top:6px;border:1px solid #ddd"></video>
                                    <img id="cam-preview" alt="" style="display:none;max-width:240px;margin-top:6px;border:1px solid #ddd">
                                    @if ($patient->img_url)
                                        <span class="help-block">A photo is already on file. A new one replaces it.</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <fieldset style="margin:10px 0 20px;padding-top:10px;border-top:1px solid #eee">
                            <legend style="font-size:16px;border:0;margin-bottom:6px">Registration details</legend>
                            <div class="row">
                                <div class="col-md-3 form-group">
                                    <label>Patient type</label>
                                    <select name="patient_type_id" class="form-control">
                                        @foreach ($patientTypes as $t)
                                            <option value="{{ $t->id }}" {{ (string) old('patient_type_id', $patient->patient_type_id ?: \App\Models\PatientType::defaultId()) === (string) $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block">Decides which price list the patient's invoices use.</span>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>CNIC</label>
                                    <input type="text" name="cnic" class="form-control" placeholder="12345-1234567-1" value="{{ old('cnic', $patient->cnic) }}">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>City</label>
                                    <input type="text" name="city" class="form-control" value="{{ old('city', $patient->city) }}">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>Alternate contact</label>
                                    <input type="text" name="alt_phone" class="form-control" value="{{ old('alt_phone', $patient->alt_phone) }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 form-group">
                                    <label>Guardian relation</label>
                                    <select name="guardian_relation" class="form-control">
                                        <option value="">Not recorded</option>
                                        @foreach (['Father', 'Mother', 'Husband', 'Wife', 'Son', 'Daughter', 'Brother', 'Sister', 'Other'] as $rel)
                                            <option value="{{ $rel }}" {{ old('guardian_relation', $patient->guardian_relation) === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>Guardian MR number</label>
                                    <input type="text" name="guardian_mrn" class="form-control" placeholder="If the guardian is a patient too" value="{{ old('guardian_mrn', $patient->guardian_mrn) }}">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>Old MR number</label>
                                    <input type="text" name="old_mrn" class="form-control" placeholder="From a previous system" value="{{ old('old_mrn', $patient->old_mrn) }}">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label>Employee or student no.</label>
                                    <input type="text" name="employee_no" class="form-control" placeholder="For staff and panel patients" value="{{ old('employee_no', $patient->employee_no) }}">
                                </div>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" name="mlc" value="1" {{ old('mlc', $patient->mlc) ? 'checked' : '' }}> Medico-legal case (MLC)</label>
                            </div>
                        </fieldset>

                        <button type="submit" class="btn btn-primary">
                            {{ $editing ? 'Save changes' : 'Register patient' }}
                        </button>
                        <a href="{{ url('/patients') }}" class="btn btn-default">Cancel</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
<script>
// Webcam photo at the desk. Needs HTTPS (or localhost) for the browser to allow the camera.
(function () {
    var start = document.getElementById('cam-start'), snap = document.getElementById('cam-snap');
    var video = document.getElementById('cam-video'), preview = document.getElementById('cam-preview');
    var stream = null;
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) { start.style.display = 'none'; return; }
    start.addEventListener('click', function () {
        navigator.mediaDevices.getUserMedia({ video: true }).then(function (s) {
            stream = s; video.srcObject = s; video.style.display = 'block'; snap.style.display = 'inline-block';
        }).catch(function () { alert('The browser did not allow the camera. Upload a photo instead.'); });
    });
    snap.addEventListener('click', function () {
        var c = document.createElement('canvas');
        c.width = video.videoWidth || 640; c.height = video.videoHeight || 480;
        c.getContext('2d').drawImage(video, 0, 0, c.width, c.height);
        var data = c.toDataURL('image/jpeg', 0.85);
        document.getElementById('photo_data').value = data;
        preview.src = data; preview.style.display = 'block';
        video.style.display = 'none'; snap.style.display = 'none';
        if (stream) { stream.getTracks().forEach(function (t) { t.stop(); }); }
    });
})();
</script>
@stop
