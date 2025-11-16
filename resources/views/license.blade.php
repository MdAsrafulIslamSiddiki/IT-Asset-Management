@extends('layouts.backendLayout')


@section('content')

<main class="page">
  <h1 class="h1">Licenses</h1>
  <p class="sub">Track subscriptions and seats</p>
  <section class="grid-2">
    <article class="card panel">
      <h3>Seat Utilization</h3>
      <div style="display:grid;gap:8px">
        <div>Microsoft Office 365 – 35/50</div>
        <div>Adobe Creative Suite – 8/10</div>
        <div>Slack Pro – 45/100</div>
        <div>Windows 11 Pro – 20/25</div>
      </div>
    </article>
    <article class="card panel">
      <h3>Upcoming Expirations</h3>
      <table class="table"><thead><tr><th>License</th><th>Owner</th><th>Expires</th><th>Status</th></tr></thead>
        <tbody>
          <tr><td>Adobe Creative Suite</td><td>Design Team</td><td>2025-11-02</td><td><span class="badge active">OK</span></td></tr>
          <tr><td>Windows 11 Pro</td><td>IT</td><td>2025-10-29</td><td><span class="badge active">OK</span></td></tr>
        </tbody>
      </table>
    </article>
  </section>
</main>

@endsection
