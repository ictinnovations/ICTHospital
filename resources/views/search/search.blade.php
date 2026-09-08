{{--
  ICTHospital — global search
  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')

@section("style")
<link href="{{ URL::asset('/css/custom.min.css')}}" rel='stylesheet'>
<link href="{{ URL::asset('/font-awesome/css/font-awesome.min.css')}}" rel='stylesheet'>
<style>
  .ich-card{background:#fff;border:1px solid #e6e9ef;border-radius:6px;padding:18px;margin-bottom:20px;}
  .ich-sec{font-size:16px;font-weight:600;margin:0 0 14px;color:#1b2a4a;}
  .ich-empty{color:#9aa2b1;font-style:italic;padding:12px 0;}
</style>
@endsection

@section('content')
<div class="row" style="margin-top:18px;">
  <div class="col-md-12">
    <div class="ich-card">
      <div class="ich-sec">Search patients and doctors</div>
      <div class="input-group">
        <input type="text" id="ich-q" class="form-control" placeholder="Name, phone, patient ID, department..." autocomplete="off">
        <span class="input-group-btn">
          <button class="btn btn-primary" type="button" id="ich-go">Search</button>
        </span>
      </div>
      <p class="ich-empty" id="ich-hint">Type at least two characters.</p>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="ich-card">
      <div class="ich-sec">Patients</div>
      <div id="ich-patients"><div class="ich-empty">No search yet.</div></div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="ich-card">
      <div class="ich-sec">Doctors</div>
      <div id="ich-doctors"><div class="ich-empty">No search yet.</div></div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
(function () {
  var box = document.getElementById('ich-q');
  var hint = document.getElementById('ich-hint');

  function rows(list, cols) {
    if (!list || !list.length) return '<div class="ich-empty">No matches.</div>';
    var head = '<tr>' + cols.map(function (c) { return '<th>' + c.label + '</th>'; }).join('') + '</tr>';
    var body = list.map(function (r) {
      return '<tr>' + cols.map(function (c) {
        var v = r[c.key];
        return '<td>' + (v === null || v === undefined || v === '' ? '-' : String(v).replace(/[<>&]/g, '')) + '</td>';
      }).join('') + '</tr>';
    }).join('');
    return '<table class="table table-striped" style="margin-bottom:0;"><thead>' + head + '</thead><tbody>' + body + '</tbody></table>';
  }

  function run() {
    var term = box.value.trim();
    if (term.length < 2) { hint.textContent = 'Type at least two characters.'; return; }
    hint.textContent = 'Searching...';

    fetch('{{ url("/search") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: 'search=' + encodeURIComponent(term)
    })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        hint.textContent = (d.count || 0) + ' result(s) for "' + d.term + '"';
        document.getElementById('ich-patients').innerHTML = rows(d.patients, [
          { key: 'name', label: 'Name' }, { key: 'patient_id', label: 'Patient ID' },
          { key: 'phone', label: 'Phone' }, { key: 'sex', label: 'Sex' }, { key: 'age', label: 'Age' }
        ]);
        document.getElementById('ich-doctors').innerHTML = rows(d.doctors, [
          { key: 'name', label: 'Name' }, { key: 'phone', label: 'Phone' }, { key: 'email', label: 'Email' }
        ]);
      })
      .catch(function () { hint.textContent = 'Search failed. Please try again.'; });
  }

  document.getElementById('ich-go').addEventListener('click', run);
  box.addEventListener('keyup', function (e) { if (e.key === 'Enter') run(); });
})();
</script>
@endsection
