@extends('layouts.backendLayout')
@section('title', 'Search')

@section('content')
    <main class="page">
        <h1 class="h1">Asset Search</h1>
        <p class="sub">Search for employee assets and licenses by Iqama ID or email address</p>

        <article class="card panel">
            <form method="GET" action="{{ route('search.index') }}">
                <div style="display: flex; gap: 12px; align-items: end;">
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Search by Iqama ID or Email
                            Address</label>
                        <input class="input" name="search" placeholder="Enter Iqama ID or Email..."
                            value="{{ $searchQuery ?? '' }}" style="width: 100%;">
                    </div>
                    <button type="submit" class="btn primary">🔎 Search</button>
                    <a href="{{ route('search.index') }}" class="btn ghost">Clear</a>
                </div>
            </form>
        </article>

        @if ($searchQuery && !$employee)
            <article class="card panel"
                style="
                    margin-top: 16px;
                    background: #f3f6ff;
                    border-color: #e3eaff;
                ">
                <h3 style="margin: 0 0 8px">Search Instructions</h3>
                <ul style="margin: 0 0 0 18px; color: #1d4ed8">
                    <li>
                        Enter the complete Iqama ID number (e.g., 2234567890)
                    </li>
                    <li>
                        Or enter the employee's email address (e.g.,
                        john.doe@company.com)
                    </li>
                    <li>
                        The search will show all assets and licenses assigned to
                        the employee
                    </li>
                    <li>Partial matches are supported for email addresses</li>
                </ul>
            </article>
        @endif

        @if ($employee)
            <article class="card panel" style="margin-top:16px">
                <div style="display:flex;gap:16px;align-items:center">
                    <div class="avatar-lg">{{ strtoupper(substr($employee->name, 0, 1)) }}</div>
                    <div style="flex:1">
                        <h3 style="margin:0">{{ $employee->name }}</h3>
                        <p class="sub" style="margin:0">
                            {{ $employee->job_title }} · {{ $employee->department }} •
                            Iqama: {{ $employee->iqama_id }} •
                            Email: {{ $employee->email }}
                            <span class="badge {{ $employee->status }}">{{ $employee->status }}</span>
                        </p>
                    </div>
                </div>

                <div class="grid-2" style="margin-top:16px">
                    <!-- Assigned Assets -->
                    <div class="card panel">
                        <h3>Assigned Assets ({{ $assets->count() }})</h3>
                        @forelse($assets as $asset)
                            <div class="card panel" style="margin-top:8px; display: flex; align-items: center; gap: 12px;">
                                <div style="flex: 1;">
                                    <strong>{{ $asset->name }}</strong>
                                    <div class="kv">Serial: {{ $asset->serial_number }}</div>
                                    <div class="kv">Assigned: {{ $asset->assigned_date }}</div>
                                    <span class="badge {{ $asset->condition }}">{{ $asset->condition }}</span>
                                </div>
                                <div style="font-weight:800; color: #16a34a;">${{ number_format($asset->value, 0) }}</div>
                            </div>
                        @empty
                            <p style="color: #6b7280; font-size: 14px; margin-top: 8px;">No assets assigned</p>
                        @endforelse
                    </div>

                    <!-- Assigned Licenses -->
                    <div class="card panel">
                        <h3>Assigned Licenses ({{ $licenses->count() }})</h3>
                        @forelse($licenses as $license)
                            <div class="card panel" style="margin-top:8px;">
                                <div
                                    style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 4px;">
                                    <strong>{{ $license->name }}</strong>
                                    <span class="badge {{ $license->status }}">{{ $license->status }}</span>
                                </div>
                                <div class="kv">Assigned: {{ $license->assigned_date }}</div>
                                <div class="kv">Expires: {{ $license->expiry_date }}</div>
                            </div>
                        @empty
                            <p style="color: #6b7280; font-size: 14px; margin-top: 8px;">No licenses assigned</p>
                        @endforelse
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card panel" style="margin-top:16px">
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;text-align:center">
                        <div>
                            <div class="sub">Total Assets</div>
                            <div style="font-weight:800;font-size:20px">{{ $statistics['total_assets'] }}</div>
                        </div>
                        <div>
                            <div class="sub">Total Licenses</div>
                            <div style="font-weight:800;font-size:20px">{{ $statistics['total_licenses'] }}</div>
                        </div>
                        <div>
                            <div class="sub">Asset Value</div>
                            <div style="font-weight:800;font-size:20px;color:#16a34a">
                                ${{ number_format($statistics['asset_value'], 0) }}
                            </div>
                        </div>
                        <div>
                            <div class="sub">Annual License Cost</div>
                            <div style="font-weight:800;font-size:20px;color:#ef4444">
                                ${{ number_format($statistics['license_cost'], 0) }}
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        @endif

        @if (!$searchQuery)
            <article class="card panel"
                style="
                    margin-top: 16px;
                    background: #f3f6ff;
                    border-color: #e3eaff;
                ">
                <h3 style="margin: 0 0 8px">Search Instructions</h3>
                <ul style="margin: 0 0 0 18px; color: #1d4ed8">
                    <li>
                        Enter the complete Iqama ID number (e.g., 2234567890)
                    </li>
                    <li>
                        Or enter the employee's email address (e.g.,
                        john.doe@company.com)
                    </li>
                    <li>
                        The search will show all assets and licenses assigned to
                        the employee
                    </li>
                    <li>Partial matches are supported for email addresses</li>
                </ul>
            </article>
        @endif
    </main>
@endsection
