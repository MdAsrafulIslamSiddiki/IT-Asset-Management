@extends('layouts.backendLayout')


@section('content')

<main class="page">
  <h1 class="h1">Reports & Clearance</h1>
  <p class="sub">Generate clearance papers and view system reports</p>
  <section class="stats">
    <article class="card stat"><h4>Active Employees</h4><div class="big">3</div></article>
    <article class="card stat"><h4>Total Assets</h4><div class="big">5</div></article>
    <article class="card stat"><h4>Active Licenses</h4><div class="big">4</div></article>
    <article class="card stat"><h4>Expiring Soon</h4><div class="big">0</div></article>
  </section>
  <section class="grid-2">
    <article class="card panel">
      <h3>Generate Clearance Paper</h3>
      <p class="sub">Generate clearance papers for departing employees to ensure all assets and licenses are returned.</p>
      <div class="form-grid" style="grid-template-columns:1fr auto;align-items:end">
        <div class="form-row"><label>Select Employee</label><select class="select"><option>Choose employee...</option><option>Sarah Johnson - Marketing (active)</option></select></div>
        <button class="btn primary" onclick="openModal('clearanceModal')">Generate Clearance Paper</button>
      </div>
    </article>
    <article class="card panel">
      <h3>Expiring Licenses</h3>
      <p class="sub">No licenses expiring in the next 30 days.</p>
    </article>
  </section>
  <article class="card panel" style="margin-top:16px">
    <h3>Asset Allocation Report</h3>
    <table class="table"><thead><tr><th>DEPARTMENT</th><th>EMPLOYEES</th><th>ASSETS</th><th>LICENSES</th><th>TOTAL VALUE</th></tr></thead>
      <tbody>
        <tr><td>IT</td><td>1</td><td>1</td><td>3</td><td>$2,500</td></tr>
        <tr><td>Marketing</td><td>1</td><td>1</td><td>3</td><td>$1,800</td></tr>
        <tr><td>Finance</td><td>1</td><td>1</td><td>3</td><td>$1,200</td></tr>
        <tr><td>HR</td><td>1</td><td>0</td><td>1</td><td>$0</td></tr>
      </tbody>
    </table>
  </article>

  <!-- Clearance Modal -->
  <div class="modal" id="clearanceModal">
    <div class="dialog">
      <div class="head"><strong>Generate Clearance Paper</strong><button class="btn ghost" onclick="closeModal('clearanceModal')">✖</button></div>
      <div class="body">
        <h3>For Sarah Johnson</h3>
        <div class="card panel" style="background:#fffbe6;border-color:#fde68a"><strong>Important</strong><p class="sub">Please ensure all assets and licenses listed above have been returned and verified before generating the clearance paper.</p></div>
        <div class="grid-2" style="margin-top:10px">
          <div class="card panel">
            <h4>Employee Summary</h4>
            <div class="kv">Iqama ID: <strong>2234567891</strong></div>
            <div class="kv">Department: <strong>Marketing</strong></div>
            <div class="kv">Assets: <strong>1</strong></div>
            <div class="kv">Licenses: <strong>3</strong></div>
            <div class="kv">Total Value: <strong>$6,300</strong></div>
          </div>
          <div class="card panel">
            <h4>Items</h4>
            <div class="kv">Dell XPS 13 – Serial DXP2023002</div>
            <div class="kv">Microsoft O365, Adobe CS, Slack Pro</div>
          </div>
        </div>
      </div>
      <div class="foot">
        <button class="btn ghost" onclick="closeModal('clearanceModal')">Cancel</button>
        <button class="btn primary">⬇ Generate & Download</button>
      </div>
    </div>
  </div>
</main>

@endsection
