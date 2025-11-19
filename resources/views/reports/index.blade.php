@extends('layouts.backendLayout')
@section('title', 'Reports')

@section('content')
    <main class="page">
        <h1 class="h1">Reports & Clearance</h1>
        <p class="sub">Generate clearance papers and view system reports</p>

        @if (isset($error))
            <div class="alert alert-error"
                style="margin-bottom: 16px; padding: 12px; background: #fee; border: 1px solid #fcc; border-radius: 4px; color: #c33;">
                {{ $error }}
            </div>
        @endif

        <section class="stats">
            <article class="card stat">
                <h4>Active Employees</h4>
                <div class="big">{{ $activeEmployeesCount }}</div>
            </article>
            <article class="card stat">
                <h4>Total Assets</h4>
                <div class="big">{{ $totalAssetsCount }}</div>
            </article>
            <article class="card stat">
                <h4>Active Licenses</h4>
                <div class="big">{{ $activeLicensesCount }}</div>
            </article>
            <article class="card stat">
                <h4>Expiring Soon</h4>
                <div class="big">{{ $expiringLicensesCount }}</div>
            </article>
        </section>

        <section class="grid-2">
            <article class="card panel">
                <h3>Generate Clearance Paper</h3>
                <p class="sub">Generate clearance papers for departing employees to ensure all assets and licenses are
                    returned.</p>
                <div class="form-grid" style="grid-template-columns: 1fr auto; align-items: end;">
                    <div>
                        <label>Select Employee</label>
                        <select class="select" id="employeeSelect">
                            <option value="">Choose employee...</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">
                                    {{ $employee->name }} - {{ $employee->department }} ({{ $employee->status }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn primary" onclick="openClearanceModal()">Generate Clearance Paper</button>
                </div>
            </article>

            <article class="card panel">
                <h3>Expiring Licenses</h3>
                @if ($expiringLicenses->count() > 0)
                    <p class="sub">{{ $expiringLicenses->count() }} license(s) expiring in the next 30 days.</p>
                    <ul style="margin-top: 10px;">
                        @foreach ($expiringLicenses as $license)
                            <li style="margin-bottom: 8px;">
                                <strong>{{ $license->name }}</strong> - Expires: {{ $license->expiry_date }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="sub">No licenses expiring in the next 30 days.</p>
                @endif
            </article>
        </section>

        <article class="card panel" style="margin-top:16px">
            <h3>Asset Allocation Report</h3>
            @if (count($assetAllocationReport) > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>DEPARTMENT</th>
                            <th>EMPLOYEES</th>
                            <th>ASSETS</th>
                            <th>LICENSES</th>
                            <th>TOTAL VALUE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assetAllocationReport as $report)
                            <tr>
                                <td>{{ $report['department'] }}</td>
                                <td>{{ $report['employees'] }}</td>
                                <td>{{ $report['assets'] }}</td>
                                <td>{{ $report['licenses'] }}</td>
                                <td>{{ $report['total_value'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="sub">No data available.</p>
            @endif
        </article>

        <!-- Clearance Modal -->
        <div class="modal" id="clearanceModal" style="display: none;">
            <div class="dialog">
                <div class="head">
                    <strong>Generate Clearance Paper</strong>
                    <button class="btn ghost" onclick="closeClearanceModal()">✖</button>
                </div>
                <div class="body">
                    <div id="modalLoading" style="text-align: center; padding: 20px;">
                        Loading employee details...
                    </div>
                    <div id="modalContent" style="display: none;">
                        <h3 id="employeeName"></h3>
                        <div class="card panel" style="background:#fffbe6;border-color:#fde68a">
                            <strong>Important</strong>
                            <p class="sub">Please ensure all assets and licenses listed below have been returned and
                                verified before generating the clearance paper.</p>
                        </div>
                        <div class="grid-2" style="margin-top:10px">
                            <div class="card panel">
                                <h4>Employee Summary</h4>
                                <div class="kv">Iqama ID: <strong id="iqamaId"></strong></div>
                                <div class="kv">Department: <strong id="department"></strong></div>
                                <div class="kv">Assets: <strong id="assetsCount"></strong></div>
                                <div class="kv">Licenses: <strong id="licensesCount"></strong></div>
                                <div class="kv">Total Value: <strong id="totalValue"></strong></div>
                            </div>
                            <div class="card panel">
                                <h4>Items</h4>
                                <div class="kv" id="assetsList"></div>
                                <div class="kv" id="licensesList" style="margin-top: 8px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="foot">
                    <button class="btn ghost" onclick="closeClearanceModal()">Cancel</button>
                    <button class="btn primary" id="generateBtn" onclick="generateClearance()">⬇ Generate &
                        Download</button>
                </div>
            </div>
        </div>
    </main>

    <script>
        let selectedEmployeeId = null;

        function openClearanceModal() {
            const employeeSelect = document.getElementById('employeeSelect');
            selectedEmployeeId = employeeSelect.value;

            if (!selectedEmployeeId) {
                alert('Please select an employee first.');
                return;
            }

            document.getElementById('clearanceModal').style.display = 'flex';
            document.getElementById('modalLoading').style.display = 'block';
            document.getElementById('modalContent').style.display = 'none';

            fetch('{{ route('reports.employee-details') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        employee_id: selectedEmployeeId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('employeeName').textContent = 'For ' + data.data.name;
                        document.getElementById('iqamaId').textContent = data.data.iqama_id;
                        document.getElementById('department').textContent = data.data.department;
                        document.getElementById('assetsCount').textContent = data.data.assets_count;
                        document.getElementById('licensesCount').textContent = data.data.licenses_count;
                        document.getElementById('totalValue').textContent = data.data.total_value;
                        document.getElementById('assetsList').textContent = data.data.assets_list;
                        document.getElementById('licensesList').textContent = data.data.licenses_list;

                        document.getElementById('modalLoading').style.display = 'none';
                        document.getElementById('modalContent').style.display = 'block';
                    } else {
                        alert('Error: ' + data.message);
                        closeClearanceModal();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load employee details.');
                    closeClearanceModal();
                });
        }

        function closeClearanceModal() {
            document.getElementById('clearanceModal').style.display = 'none';
            selectedEmployeeId = null;
        }

        function generateClearance() {
            if (!selectedEmployeeId) {
                alert('No employee selected.');
                return;
            }

            const generateBtn = document.getElementById('generateBtn');
            generateBtn.disabled = true;
            generateBtn.textContent = 'Generating...';

            window.location.href = '{{ url('employees') }}/' + selectedEmployeeId + '/clearance';

            setTimeout(() => {
                generateBtn.disabled = false;
                generateBtn.textContent = '⬇ Generate & Download';
                closeClearanceModal();
            }, 2000);
        }

        document.getElementById('clearanceModal')?.addEventListener('click', function(e) {
            if (e.target.id === 'clearanceModal') {
                closeClearanceModal();
            }
        });
    </script>

@endsection
