@extends('layouts.backendLayout')
@section('title', 'Employees')

@section('content')
    <main class="page" x-data="employeeManager()">
        <!-- Dynamic Flash Messages (Alpine.js controlled) -->
        <div x-show="flashMessage"
             x-transition
             class="alert"
             :class="flashType"
             x-text="flashMessage"
             style="display: none; margin-bottom: 16px; padding: 12px 16px; border-radius: 8px; font-weight: 500;">
        </div>

        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h1 class="h1">Employee Management</h1>
                <p class="sub">Manage employee profiles and their asset assignments</p>
            </div>
            <button class="btn primary" @click="openCreateForm()">+ Add Employee</button>
        </div>

        <!-- Add/Edit Employee Form Panel -->
        <article class="card panel" x-show="formOpen" x-transition style="display: none; margin-bottom: 16px;">
            <h3 x-text="editMode ? 'Edit Employee' : 'Add New Employee'"></h3>

            <form @submit.prevent="submitForm()">
                <div class="form-grid" style="margin-top: 10px">
                    <div class="form-row">
                        <label>Name</label>
                        <input type="text" class="input" x-model="formData.name" placeholder="Enter full name" />
                        <span class="error-text" x-show="errors.name" x-text="errors.name"></span>
                    </div>

                    <div class="form-row">
                        <label>Iqama ID</label>
                        <input type="text" class="input" x-model="formData.iqama_id" placeholder="Enter Iqama ID" />
                        <span class="error-text" x-show="errors.iqama_id" x-text="errors.iqama_id"></span>
                    </div>

                    <div class="form-row">
                        <label>Email</label>
                        <input type="email" class="input" x-model="formData.email" placeholder="employee@company.com" />
                        <span class="error-text" x-show="errors.email" x-text="errors.email"></span>
                    </div>

                    <div class="form-row">
                        <label>Department</label>
                        <input type="text" class="input" x-model="formData.department" placeholder="e.g., IT, HR, Finance" />
                        <span class="error-text" x-show="errors.department" x-text="errors.department"></span>
                    </div>

                    <div class="form-row">
                        <label>Position</label>
                        <input type="text" class="input" x-model="formData.position" placeholder="e.g., Manager, Developer" />
                        <span class="error-text" x-show="errors.position" x-text="errors.position"></span>
                    </div>

                    <div class="form-row">
                        <label>Join Date</label>
                        <input type="date" class="input" x-model="formData.join_date" />
                        <span class="error-text" x-show="errors.join_date" x-text="errors.join_date"></span>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 16px;">
                    <button type="button" class="btn ghost" @click="closeForm()">Cancel</button>
                    <button type="submit" class="btn primary" :disabled="submitting">
                        <span x-show="!submitting" x-text="editMode ? 'Update Employee' : 'Add Employee'"></span>
                        <span x-show="submitting">Processing...</span>
                    </button>
                </div>
            </form>
        </article>

        <!-- Employee Cards -->
        <section class="grid-3">
            @forelse($employees as $employee)
                <article class="card emp">
                    <div class="top">
                        <div class="avatar-lg">{{ $employee->initials }}</div>
                        <div>
                            <div class="name">{{ $employee->name }}</div>
                            <span class="badge {{ $employee->status }}">{{ $employee->status }}</span>
                        </div>
                    </div>
                    <div class="kv">📞 {{ $employee->iqama_id }}</div>
                    <div class="kv">✉️ {{ $employee->email }}</div>
                    <div class="kv">🏢 {{ $employee->department }}</div>
                    <div class="kv">📅 Joined {{ $employee->join_date }}</div>
                    <div class="pills">
                        <div class="pill"><strong>{{ $employee->assets_count }}</strong><br />Assets</div>
                        <div class="pill"><strong>{{ $employee->licenses_count ?? 0 }}</strong><br />Licenses</div>
                    </div>
                    <div class="foot">
                        <a class="eye" href="javascript:void(0)" @click="viewEmployee({{ $employee->id }})">👁 View Profile</a>
                    </div>
                </article>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                    <p>No employees found. Click "Add Employee" to create one.</p>
                </div>
            @endforelse
        </section>

        <!-- Employee Detail Modal -->
        <div class="modal" x-show="modalOpen" x-transition style="display: none;">
            <div class="dialog">
                <div class="head">
                    <strong x-text="viewingEmployee.name"></strong>
                    <button class="btn ghost" @click="closeModal()">✖</button>
                </div>
                <div class="body">
                    <div class="cols">
                        <div>
                            <h3>Employee Information</h3>
                            <div class="kv">Iqama ID: <strong x-text="viewingEmployee.iqama_id"></strong></div>
                            <div class="kv">Email: <strong x-text="viewingEmployee.email"></strong></div>
                            <div class="kv">Department: <strong x-text="viewingEmployee.department"></strong></div>
                            <div class="kv">Position: <strong x-text="viewingEmployee.position"></strong></div>
                            <div class="kv">Join Date: <strong x-text="viewingEmployee.join_date"></strong></div>
                            <div class="kv">
                                Status:
                                <span class="badge" :class="viewingEmployee.status" x-text="viewingEmployee.status"></span>
                            </div>

                            <h4 style="margin-top: 16px;">Assigned Assets (<span x-text="viewingEmployee.assets?.length || 0"></span>)</h4>
                            <template x-if="viewingEmployee.assets && viewingEmployee.assets.length > 0">
                                <div style="display: grid; gap: 8px; margin-top: 8px;">
                                    <template x-for="asset in viewingEmployee.assets" :key="asset.id">
                                        <div class="card panel" style="padding: 8px;">
                                            <strong x-text="asset.name"></strong>
                                            <div class="kv" style="font-size: 12px;">Serial: <span x-text="asset.serial_number"></span></div>
                                            <span class="badge" :class="asset.condition" x-text="asset.condition" style="font-size: 11px;"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!viewingEmployee.assets || viewingEmployee.assets.length === 0">
                                <p style="color: #666; font-size: 14px;">No assets assigned</p>
                            </template>
                        </div>

                        <div>
                            <h3>Quick Actions</h3>
                            <div style="display: grid; gap: 10px; margin-bottom: 16px;">
                                <button class="btn ghost" @click="editEmployeeFromModal()">
                                    ✏️ Edit Employee
                                </button>
                                <button class="btn" style="background: #ef4444; color: #fff; border-color: #ef4444;"
                                        @click="deleteEmployeeFromModal()">
                                    🗑️ Delete Employee
                                </button>
                                <button class="btn primary" @click="generateClearancePaper()">
                                    📄 Generate Clearance Paper
                                </button>
                                <button class="btn" style="background: #22c55e; color: #fff; border-color: #22c55e;"
                                        @click="openAssignAssetModal()">
                                    💼 Assign Asset
                                </button>
                                <button class="btn" style="background: #8b5cf6; color: #fff; border-color: #8b5cf6;"
                                        @click="openAssignLicenseModal()">
                                    🔑 Assign License
                                </button>
                            </div>

                            <h4>Assigned Licenses (<span x-text="viewingEmployee.licenses?.length || 0"></span>)</h4>
                            <template x-if="viewingEmployee.licenses && viewingEmployee.licenses.length > 0">
                                <div style="display: grid; gap: 8px; margin-top: 8px;">
                                    <template x-for="license in viewingEmployee.licenses" :key="license.id">
                                        <div class="card panel" style="padding: 8px;">
                                            <strong x-text="license.name"></strong>
                                            <span class="badge active" style="font-size: 11px;">active</span>
                                            <div class="kv" style="font-size: 12px;">Expires: <span x-text="license.expiry_date"></span></div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!viewingEmployee.licenses || viewingEmployee.licenses.length === 0">
                                <p style="color: #666; font-size: 14px;">No licenses assigned</p>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="foot">
                    <button class="btn ghost" @click="closeModal()">Close</button>
                </div>
            </div>
        </div>

        <!-- Assign Asset Modal -->
        <div class="modal" x-show="assignAssetModalOpen" x-transition style="display: none;">
            <div class="dialog" style="max-width: 500px;">
                <div class="head">
                    <strong>Assign Asset to <span x-text="viewingEmployee.name"></span></strong>
                    <button class="btn ghost" @click="closeAssignAssetModal()">✖</button>
                </div>
                <div class="body">
                    <div class="form-row">
                        <label>Select Asset</label>
                        <select class="input" x-model="assignAssetData.asset_id">
                            <option value="">Choose an asset...</option>
                            @foreach($availableAssets ?? [] as $asset)
                                <option value="{{ $asset->id }}">
                                    {{ $asset->name }} ({{ $asset->asset_code }}) - {{ $asset->type }}
                                </option>
                            @endforeach
                        </select>
                        <span class="error-text" x-show="assignAssetErrors.asset_id" x-text="assignAssetErrors.asset_id"></span>
                    </div>

                    <div class="form-row">
                        <label>Assignment Notes (Optional)</label>
                        <textarea class="textarea" x-model="assignAssetData.notes" rows="3" placeholder="Any special notes..."></textarea>
                    </div>
                </div>
                <div class="foot">
                    <button class="btn ghost" @click="closeAssignAssetModal()">Cancel</button>
                    <button class="btn primary" @click="submitAssignAsset()" :disabled="assigningAsset">
                        <span x-show="!assigningAsset">Assign Asset</span>
                        <span x-show="assigningAsset">Assigning...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Assign License Modal -->
        <div class="modal" x-show="assignLicenseModalOpen" x-transition style="display: none;">
            <div class="dialog" style="max-width: 500px;">
                <div class="head">
                    <strong>Assign License to <span x-text="viewingEmployee.name"></span></strong>
                    <button class="btn ghost" @click="closeAssignLicenseModal()">✖</button>
                </div>
                <div class="body">
                    <div class="form-row">
                        <label>Select License</label>
                        <select class="input" x-model="assignLicenseData.license_id">
                            <option value="">Choose a license...</option>
                            @foreach($availableLicenses ?? [] as $license)
                                <option value="{{ $license->id }}">
                                    {{ $license->name }} - {{ $license->type }}
                                </option>
                            @endforeach
                        </select>
                        <span class="error-text" x-show="assignLicenseErrors.license_id" x-text="assignLicenseErrors.license_id"></span>
                    </div>

                    <div class="form-row">
                        <label>Expiry Date</label>
                        <input type="date" class="input" x-model="assignLicenseData.expiry_date" />
                        <span class="error-text" x-show="assignLicenseErrors.expiry_date" x-text="assignLicenseErrors.expiry_date"></span>
                    </div>
                </div>
                <div class="foot">
                    <button class="btn ghost" @click="closeAssignLicenseModal()">Cancel</button>
                    <button class="btn primary" @click="submitAssignLicense()" :disabled="assigningLicense">
                        <span x-show="!assigningLicense">Assign License</span>
                        <span x-show="assigningLicense">Assigning...</span>
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script>
        function employeeManager() {
            return {
                formOpen: false,
                modalOpen: false,
                assignAssetModalOpen: false,
                assignLicenseModalOpen: false,
                editMode: false,
                editingId: null,
                submitting: false,
                assigningAsset: false,
                assigningLicense: false,
                viewingEmployee: {},
                errors: {},
                assignAssetErrors: {},
                assignLicenseErrors: {},

                // Flash message properties
                flashMessage: '',
                flashType: '',

                formData: {
                    name: '',
                    iqama_id: '',
                    email: '',
                    department: '',
                    position: '',
                    join_date: ''
                },
                assignAssetData: {
                    asset_id: '',
                    notes: ''
                },
                assignLicenseData: {
                    license_id: '',
                    expiry_date: ''
                },

                // Initialize component - check for flash messages from localStorage
                init() {
                    this.checkFlashMessage();
                },

                // Show flash message
                showFlash(message, type = 'success') {
                    this.flashMessage = message;
                    this.flashType = type;

                    // Auto hide after 4 seconds
                    setTimeout(() => {
                        this.flashMessage = '';
                    }, 4000);
                },

                // Check localStorage for flash message after page reload
                checkFlashMessage() {
                    const flash = localStorage.getItem('flashMessage');
                    if (flash) {
                        const data = JSON.parse(flash);
                        this.showFlash(data.message, data.type);
                        localStorage.removeItem('flashMessage');
                    }
                },

                // Set flash message in localStorage (before page reload)
                setFlashMessage(message, type = 'success') {
                    localStorage.setItem('flashMessage', JSON.stringify({
                        message: message,
                        type: type
                    }));
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
                        this.errors.name = 'Name is required';
                        isValid = false;
                    }

                    if (!this.formData.iqama_id || this.formData.iqama_id.trim() === '') {
                        this.errors.iqama_id = 'Iqama ID is required';
                        isValid = false;
                    }

                    if (!this.formData.email || this.formData.email.trim() === '') {
                        this.errors.email = 'Email is required';
                        isValid = false;
                    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.formData.email)) {
                        this.errors.email = 'Please enter a valid email';
                        isValid = false;
                    }

                    if (!this.formData.department || this.formData.department.trim() === '') {
                        this.errors.department = 'Department is required';
                        isValid = false;
                    }

                    if (!this.formData.position || this.formData.position.trim() === '') {
                        this.errors.position = 'Position is required';
                        isValid = false;
                    }

                    if (!this.formData.join_date || this.formData.join_date.trim() === '') {
                        this.errors.join_date = 'Join date is required';
                        isValid = false;
                    }

                    return isValid;
                },

                submitForm() {
                    if (!this.validateForm()) {
                        return;
                    }

                    this.submitting = true;
                    const url = this.editMode ? `/employees/${this.editingId}` : '/employees';
                    const method = this.editMode ? 'PUT' : 'POST';

                    const formData = {
                        ...this.formData,
                        join_date: this.convertToStorageFormat(this.formData.join_date),
                        _method: method
                    };

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(formData)
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

                        if (response.ok && data.success) {
                            // Set flash message in localStorage
                            this.setFlashMessage(data.message, 'success');
                            // Reload page to show updated list
                            window.location.reload();
                        } else {
                            throw new Error(data.message || 'Something went wrong');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.showFlash(error.message || 'Something went wrong', 'error');
                        this.submitting = false;
                    });
                },

                convertToStorageFormat(date) {
                    try {
                        if (/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(date)) {
                            return date;
                        }
                        const d = new Date(date);
                        return `${d.getMonth() + 1}/${d.getDate()}/${d.getFullYear()}`;
                    } catch (e) {
                        return date;
                    }
                },

                convertToDateInput(dateStr) {
                    try {
                        const parts = dateStr.split('/');
                        if (parts.length === 3) {
                            const month = parts[0].padStart(2, '0');
                            const day = parts[1].padStart(2, '0');
                            const year = parts[2];
                            return `${year}-${month}-${day}`;
                        }
                    } catch (e) {
                        console.error('Date conversion error:', e);
                    }
                    return dateStr;
                },

                editEmployee(id) {
                    fetch(`/employees/${id}/edit`)
                        .then(response => response.json())
                        .then(data => {
                            this.editMode = true;
                            this.editingId = id;
                            this.errors = {};

                            this.formData = {
                                name: data.name,
                                iqama_id: data.iqama_id,
                                email: data.email,
                                department: data.department,
                                position: data.position,
                                join_date: this.convertToDateInput(data.join_date)
                            };
                            this.formOpen = true;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            this.showFlash('Failed to load employee data', 'error');
                        });
                },

                viewEmployee(id) {
                    fetch(`/employees/${id}`)
                        .then(response => {
                            if (!response.ok) throw new Error('Failed to fetch');
                            return response.json();
                        })
                        .then(data => {
                            this.viewingEmployee = data;
                            this.modalOpen = true;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            this.showFlash('Failed to load employee details', 'error');
                        });
                },

                editEmployeeFromModal() {
                    const id = this.viewingEmployee.id;
                    this.closeModal();
                    setTimeout(() => this.editEmployee(id), 300);
                },

                deleteEmployeeFromModal() {
                    if (!confirm(`Are you sure you want to delete ${this.viewingEmployee.name}?\n\nThis employee has ${this.viewingEmployee.assets?.length || 0} assigned assets. Make sure to unassign them first.`)) {
                        return;
                    }

                    fetch(`/employees/${this.viewingEmployee.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.setFlashMessage(data.message, 'success');
                            window.location.reload();
                        } else {
                            this.showFlash(data.message || 'Failed to delete employee', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.showFlash('Failed to delete employee', 'error');
                    });
                },

                generateClearancePaper() {
                    const url = `/employees/${this.viewingEmployee.id}/clearance`;
                    window.location.href = url;

                    setTimeout(() => {
                        this.showFlash('Clearance paper download started!', 'success');
                    }, 500);
                },

                openAssignAssetModal() {
                    this.assignAssetData = { asset_id: '', notes: '' };
                    this.assignAssetErrors = {};
                    this.assignAssetModalOpen = true;
                },

                closeAssignAssetModal() {
                    this.assignAssetModalOpen = false;
                    this.assignAssetData = { asset_id: '', notes: '' };
                    this.assignAssetErrors = {};
                },

                submitAssignAsset() {
                    if (!this.assignAssetData.asset_id) {
                        this.assignAssetErrors.asset_id = 'Please select an asset';
                        return;
                    }

                    this.assigningAsset = true;

                    fetch(`/employees/${this.viewingEmployee.id}/assign-asset`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.assignAssetData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.assigningAsset = false;
                        if (data.success) {
                            this.setFlashMessage(data.message, 'success');
                            window.location.reload();
                        } else {
                            this.showFlash(data.message || 'Failed to assign asset', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.showFlash('Failed to assign asset', 'error');
                        this.assigningAsset = false;
                    });
                },

                openAssignLicenseModal() {
                    this.assignLicenseData = { license_id: '', expiry_date: '' };
                    this.assignLicenseErrors = {};
                    this.assignLicenseModalOpen = true;
                },

                closeAssignLicenseModal() {
                    this.assignLicenseModalOpen = false;
                    this.assignLicenseData = { license_id: '', expiry_date: '' };
                    this.assignLicenseErrors = {};
                },

                submitAssignLicense() {
                    this.assignLicenseErrors = {};
                    let isValid = true;

                    if (!this.assignLicenseData.license_id) {
                        this.assignLicenseErrors.license_id = 'Please select a license';
                        isValid = false;
                    }

                    if (!this.assignLicenseData.expiry_date) {
                        this.assignLicenseErrors.expiry_date = 'Expiry date is required';
                        isValid = false;
                    }

                    if (!isValid) return;

                    this.assigningLicense = true;

                    fetch(`/employees/${this.viewingEmployee.id}/assign-license`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            ...this.assignLicenseData,
                            expiry_date: this.convertToStorageFormat(this.assignLicenseData.expiry_date)
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.assigningLicense = false;
                        if (data.success) {
                            this.setFlashMessage(data.message, 'success');
                            window.location.reload();
                        } else {
                            this.showFlash(data.message || 'Failed to assign license', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.showFlash('Failed to assign license', 'error');
                        this.assigningLicense = false;
                    });
                },

                closeModal() {
                    this.modalOpen = false;
                    this.viewingEmployee = {};
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
                        iqama_id: '',
                        email: '',
                        department: '',
                        position: '',
                        join_date: ''
                    };
                }
            }
        }
    </script>

@endsection
