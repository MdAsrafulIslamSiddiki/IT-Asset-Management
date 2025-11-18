@extends('layouts.backendLayout')

@section('content')
    <main class="page" x-data="licenseManager()">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert success" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert error" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                {{ session('error') }}
            </div>
        @endif

        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h1 class="h1">Licenses</h1>
                <p class="sub">Track subscriptions and seats</p>
            </div>
            <button class="btn primary" @click="openCreateForm()">+ Add License</button>
        </div>

        <!-- Add/Edit License Form -->
        <article class="card panel" x-show="formOpen" x-transition style="display: none; margin-bottom: 16px;">
            <h3 x-text="editMode ? 'Edit License' : 'Add New License'"></h3>

            <form @submit.prevent="submitForm()">
                <div class="form-grid" style="margin-top: 10px">
                    <div class="form-row">
                        <label>Software Name</label>
                        <input type="text" class="input" x-model="formData.name" />
                        <span class="error-text" x-show="errors.name" x-text="errors.name"></span>
                    </div>

                    <div class="form-row">
                        <label>Vendor/Publisher</label>
                        <input type="text" class="input" x-model="formData.vendor" />
                        <span class="error-text" x-show="errors.vendor" x-text="errors.vendor"></span>
                    </div>

                    <div class="form-row">
                        <label>License Key</label>
                        <input type="text" class="input" x-model="formData.license_key" />
                        <span class="error-text" x-show="errors.license_key" x-text="errors.license_key"></span>
                    </div>

                    <div class="form-row">
                        <label>License Type</label>
                        <select class="input" x-model="formData.license_type">
                            <option value="">Select Type</option>
                            <option value="per-user">Per User</option>
                            <option value="per-device">Per Device</option>
                            <option value="site-license">Site License</option>
                        </select>
                        <span class="error-text" x-show="errors.license_type" x-text="errors.license_type"></span>
                    </div>

                    <div class="form-row">
                        <label>Total Quantity</label>
                        <input type="number" class="input" x-model="formData.total_quantity" min="1" />
                        <span class="error-text" x-show="errors.total_quantity" x-text="errors.total_quantity"></span>
                    </div>

                    <div class="form-row">
                        <label>Purchase Date</label>
                        <input type="date" class="input" x-model="formData.purchase_date" placeholder="mm/dd/yyyy" />
                        <span class="error-text" x-show="errors.purchase_date" x-text="errors.purchase_date"></span>
                    </div>

                    <div class="form-row">
                        <label>Expiry Date</label>
                        <input type="date" class="input" x-model="formData.expiry_date" placeholder="mm/dd/yyyy" />
                        <span class="error-text" x-show="errors.expiry_date" x-text="errors.expiry_date"></span>
                    </div>

                    <div class="form-row">
                        <label>Cost Per License ($)</label>
                        <input type="number" class="input" x-model="formData.cost_per_license" step="0.01" min="0" />
                        <span class="error-text" x-show="errors.cost_per_license" x-text="errors.cost_per_license"></span>
                    </div>

                    <div class="form-row" style="grid-column: 1/-1;">
                        <label>Notes</label>
                        <textarea class="textarea" x-model="formData.notes"></textarea>
                        <span class="error-text" x-show="errors.notes" x-text="errors.notes"></span>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 16px;">
                    <button type="button" class="btn ghost" @click="closeForm()">Cancel</button>
                    <button type="submit" class="btn primary" :disabled="submitting">
                        <span x-show="!submitting" x-text="editMode ? 'Update License' : 'Add License'"></span>
                        <span x-show="submitting">Processing...</span>
                    </button>
                </div>
            </form>
        </article>

        <!-- Statistics Cards -->
        <section class="grid-2" style="margin-bottom: 16px;">
            <article class="card panel">
                <h3>Seat Utilization</h3>
                <div style="display:grid;gap:8px">
                    @foreach($licenses->take(4) as $license)
                        <div>
                            {{ $license->name }} – {{ $license->used_quantity }}/{{ $license->total_quantity }}
                            <div style="background: #e5e7eb; height: 4px; border-radius: 2px; margin-top: 4px;">
                                <div style="background: #3b82f6; height: 100%; width: {{ ($license->total_quantity > 0) ? ($license->used_quantity / $license->total_quantity * 100) : 0 }}%; border-radius: 2px;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="card panel">
                <h3>Upcoming Expirations</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>License</th>
                            <th>Expires</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($licenses->sortBy('expiry_date')->take(5) as $license)
                            <tr>
                                <td>{{ $license->name }}</td>
                                <td>{{ $license->expiry_date }}</td>
                                <td><span class="badge {{ $license->status }}">{{ $license->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </article>
        </section>

        <!-- License Cards -->
        <h3 style="margin-bottom: 12px;">All Licenses</h3>
        <section class="grid-3">
            @forelse($licenses as $license)
                <article class="card panel">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                        <strong>{{ $license->name }}</strong>
                        <span class="badge {{ $license->status }}">{{ $license->status }}</span>
                    </div>
                    <div class="kv">Vendor: {{ $license->vendor }}</div>
                    <div class="kv">Type: {{ ucwords(str_replace('-', ' ', $license->license_type)) }}</div>
                    <div class="kv">Seats: {{ $license->used_quantity }}/{{ $license->total_quantity }} used</div>
                    <div class="kv">Cost: ${{ number_format($license->cost_per_license, 2) }} per license</div>
                    <div class="kv">Total: ${{ number_format($license->total_cost, 2) }}</div>
                    <div class="kv">Expires: {{ $license->expiry_date }}</div>
                    <button class="btn ghost" style="margin-top: 8px;" @click="viewLicense({{ $license->id }})">🗂 Manage</button>
                </article>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                    <p>No licenses found. Click "Add License" to create one.</p>
                </div>
            @endforelse
        </section>

        <!-- License Detail Modal -->
        <div class="modal" x-show="modalOpen" x-transition style="display: none;">
            <div class="dialog">
                <div class="head">
                    <strong x-text="viewingLicense.name"></strong>
                    <button class="btn ghost" @click="closeModal()">✖</button>
                </div>
                <div class="body">
                    <div class="cols">
                        <div>
                            <h3>License Information</h3>
                            <div class="kv">License Code: <strong x-text="viewingLicense.license_code"></strong></div>
                            <div class="kv">Vendor: <strong x-text="viewingLicense.vendor"></strong></div>
                            <div class="kv">License Key: <strong x-text="viewingLicense.license_key"></strong></div>
                            <div class="kv">Type: <strong x-text="viewingLicense.license_type"></strong></div>
                            <div class="kv">Total Seats: <strong x-text="viewingLicense.total_quantity"></strong></div>
                            <div class="kv">Used Seats: <strong x-text="viewingLicense.used_quantity"></strong></div>
                            <div class="kv">Available: <strong x-text="viewingLicense.available_quantity"></strong></div>
                            <div class="kv">Purchase Date: <strong x-text="viewingLicense.purchase_date"></strong></div>
                            <div class="kv">Expiry Date: <strong x-text="viewingLicense.expiry_date"></strong></div>
                            <div class="kv">Cost per License: <strong>$<span x-text="viewingLicense.cost_per_license"></span></strong></div>
                            <div class="kv">Total Cost: <strong>$<span x-text="viewingLicense.total_cost"></span></strong></div>
                            <div class="kv">
                                Status:
                                <span class="badge" :class="viewingLicense.status" x-text="viewingLicense.status"></span>
                            </div>
                            <div class="kv" x-show="viewingLicense.notes">
                                Notes: <span x-text="viewingLicense.notes"></span>
                            </div>

                            <h4 style="margin-top: 16px;">Assigned To (<span x-text="viewingLicense.employees?.length || 0"></span>)</h4>
                            <template x-if="viewingLicense.employees && viewingLicense.employees.length > 0">
                                <div style="display: grid; gap: 8px;">
                                    <template x-for="emp in viewingLicense.employees" :key="emp.id">
                                        <div class="card panel" style="display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <strong x-text="emp.name"></strong>
                                                <div class="kv" x-text="emp.department"></div>
                                                <div class="kv">Assigned: <span x-text="emp.assigned_date"></span></div>
                                            </div>
                                            <button class="btn ghost" @click="revokeLicense(emp.id)">Revoke</button>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!viewingLicense.employees || viewingLicense.employees.length === 0">
                                <p style="color: #666; font-size: 14px;">No assignments</p>
                            </template>
                        </div>

                        <div>
                            <h3>Assign License</h3>
                            <template x-if="viewingLicense.available_quantity > 0">
                                <div>
                                    <label style="display: block; margin-bottom: 8px;">Select Employee:</label>
                                    <select class="input" x-model="assignEmployeeId" style="width: 100%; margin-bottom: 8px;">
                                        <option value="">Select Employee</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}">{{ $emp->name }} - {{ $emp->department }}</option>
                                        @endforeach
                                    </select>
                                    <label style="display: block; margin-bottom: 8px;">Expiry Date (Optional):</label>
                                    <input type="text" class="input" x-model="assignExpiryDate" placeholder="mm/dd/yyyy" style="width: 100%; margin-bottom: 8px;" />
                                    <button class="btn primary" @click="assignLicenseToEmployee()" style="width: 100%;">
                                        Assign License
                                    </button>
                                </div>
                            </template>
                            <template x-if="viewingLicense.available_quantity <= 0">
                                <p style="color: #ef4444; font-weight: 600;">No available seats</p>
                            </template>

                            <h4 style="margin-top: 20px;">Quick Actions</h4>
                            <div style="display: grid; gap: 10px;">
                                <button class="btn ghost" @click="editLicenseFromModal()">✏️ Edit License</button>
                                <button class="btn" style="background: #ef4444; color: #fff; border-color: #ef4444;"
                                        @click="deleteLicenseFromModal()">
                                    🗑️ Delete License
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="foot">
                    <button class="btn ghost" @click="closeModal()">Close</button>
                </div>
            </div>
        </div>
    </main>

    <script>
        function licenseManager() {
            return {
                formOpen: false,
                modalOpen: false,
                editMode: false,
                editingId: null,
                submitting: false,
                viewingLicense: {},
                assignEmployeeId: '',
                assignExpiryDate: '',
                errors: {},
                formData: {
                    name: '',
                    vendor: '',
                    license_key: '',
                    license_type: '',
                    total_quantity: 1,
                    purchase_date: '',
                    expiry_date: '',
                    cost_per_license: 0,
                    notes: ''
                },

                openCreateForm() {
                    this.editMode = false;
                    this.editingId = null;
                    this.resetForm();
                    this.formOpen = true;
                },

                validateForm() {
                    this.errors = {};
                    let isValid = true;

                    if (!this.formData.name || this.formData.name.trim() === '') {
                        this.errors.name = 'Software name is required';
                        isValid = false;
                    }

                    if (!this.formData.vendor || this.formData.vendor.trim() === '') {
                        this.errors.vendor = 'Vendor is required';
                        isValid = false;
                    }

                    if (!this.formData.license_key || this.formData.license_key.trim() === '') {
                        this.errors.license_key = 'License key is required';
                        isValid = false;
                    }

                    if (!this.formData.license_type) {
                        this.errors.license_type = 'License type is required';
                        isValid = false;
                    }

                    if (!this.formData.total_quantity || this.formData.total_quantity < 1) {
                        this.errors.total_quantity = 'Total quantity must be at least 1';
                        isValid = false;
                    }

                    if (!this.formData.purchase_date || this.formData.purchase_date === '') {
                        this.errors.purchase_date = 'Purchase date is required';
                        isValid = false;
                    }

                    if (!this.formData.expiry_date || this.formData.expiry_date === '') {
                        this.errors.expiry_date = 'Expiry date is required';
                        isValid = false;
                    }

                    if (!this.formData.cost_per_license || this.formData.cost_per_license < 0) {
                        this.errors.cost_per_license = 'Cost per license is required';
                        isValid = false;
                    }

                    return isValid;
                },

                submitForm() {
                    if (!this.validateForm()) {
                        return;
                    }

                    this.submitting = true;
                    const url = this.editMode ? `/licenses/${this.editingId}` : '/licenses';
                    const method = this.editMode ? 'PUT' : 'POST';

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            ...this.formData,
                            _method: method
                        })
                    })
                    .then(async response => {
                        const data = await response.json();

                        if (response.status === 422) {
                            this.errors = {};
                            if (data.errors) {
                                Object.keys(data.errors).forEach(key => {
                                    this.errors[key] = data.errors[key][0];
                                });
                            }
                            this.submitting = false;
                            return;
                        }

                        if (response.ok) {
                            window.location.reload();
                        } else {
                            throw new Error(data.message || 'Something went wrong');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Something went wrong. Please try again.');
                        this.submitting = false;
                    });
                },

                editLicense(id) {
                    fetch(`/licenses/${id}/edit`)
                        .then(response => response.json())
                        .then(data => {
                            this.editMode = true;
                            this.editingId = id;
                            this.errors = {};
                            this.formData = data;
                            this.formOpen = true;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to load license data');
                        });
                },

                viewLicense(id) {
                    fetch(`/licenses/${id}`)
                        .then(response => response.json())
                        .then(data => {
                            this.viewingLicense = data;
                            this.assignEmployeeId = '';
                            this.assignExpiryDate = '';
                            this.modalOpen = true;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to load license details');
                        });
                },

                editLicenseFromModal() {
                    const id = this.viewingLicense.id;
                    this.closeModal();
                    setTimeout(() => {
                        this.editLicense(id);
                    }, 300);
                },

                deleteLicenseFromModal() {
                    if (!confirm(`Are you sure you want to delete ${this.viewingLicense.name}?`)) {
                        return;
                    }

                    fetch(`/licenses/${this.viewingLicense.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert(data.message || 'Failed to delete license');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to delete license');
                    });
                },

                assignLicenseToEmployee() {
                    if (!this.assignEmployeeId) {
                        alert('Please select an employee');
                        return;
                    }

                    fetch(`/licenses/${this.viewingLicense.id}/assign`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            employee_id: this.assignEmployeeId,
                            expiry_date: this.assignExpiryDate
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            window.location.reload();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to assign license');
                    });
                },

                revokeLicense(employeeId) {
                    if (!confirm('Are you sure you want to revoke this license?')) {
                        return;
                    }

                    fetch(`/licenses/${this.viewingLicense.id}/revoke`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            employee_id: employeeId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            window.location.reload();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to revoke license');
                    });
                },

                closeModal() {
                    this.modalOpen = false;
                    this.viewingLicense = {};
                },

                closeForm() {
                    this.formOpen = false;
                    this.resetForm();
                },

                resetForm() {
                    this.errors = {};
                    this.submitting = false;
                    this.formData = {
                        name: '',
                        vendor: '',
                        license_key: '',
                        license_type: '',
                        total_quantity: 1,
                        purchase_date: '',
                        expiry_date: '',
                        cost_per_license: 0,
                        notes: ''
                    };
                }
            }
        }
    </script>
@endsection
