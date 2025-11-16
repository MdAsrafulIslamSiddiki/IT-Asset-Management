@extends('layouts.backendLayout')

@section('content')
    <main class="page" x-data="employeeManager()">
        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="alert success" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert error" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                {{ session('error') }}
            </div>
        @endif

        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 class="h1">Employee Management</h1>
                <p class="sub">Manage employee profiles and their asset assignments</p>
            </div>
            <button class="btn primary" @click="openCreateForm()">+ Add Employee</button>
        </div>

        <!-- Add/Edit Employee Form Panel -->
        <article class="card panel" x-show="formOpen" x-transition style="display: none;">
            <h3 x-text="editMode ? 'Edit Employee' : 'Add New Employee'"></h3>

            <form @submit.prevent="submitForm()">
                <div class="form-grid" style="margin-top: 10px">
                    <div class="form-row">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="input" x-model="formData.name" placeholder="" />
                        <span class="error-text" x-show="errors.name" x-text="errors.name"></span>
                    </div>

                    <div class="form-row">
                        <label for="iqama_id">Iqama ID</label>
                        <input type="number" name="iqama_id" class="input" x-model="formData.iqama_id" placeholder="" />
                        <span class="error-text" x-show="errors.iqama_id" x-text="errors.iqama_id"></span>
                    </div>

                    <div class="form-row">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="input" x-model="formData.email" placeholder="" />
                        <span class="error-text" x-show="errors.email" x-text="errors.email"></span>
                    </div>

                    <div class="form-row">
                        <label for="department">Department</label>
                        <input type="text" name="department" class="input" x-model="formData.department"
                            placeholder="" />
                        <span class="error-text" x-show="errors.department" x-text="errors.department"></span>
                    </div>

                    <div class="form-row">
                        <label for="position">Position</label>
                        <input type="text" name="position" class="input" x-model="formData.position" placeholder="" />
                        <span class="error-text" x-show="errors.position" x-text="errors.position"></span>
                    </div>

                    <div class="form-row">
                        <label for="join_date">Join Date</label>
                        <input type="date" name="join_date" class="input" x-model="formData.join_date"
                            placeholder="mm/dd/yyyy" />
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

        <div style="height: 8px"></div>

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
                        <div class="pill"><strong>{{ $employee->licenses_count }}</strong><br />Licenses</div>
                    </div>
                    <div class="foot">
                        <a class="eye" href="javascript:void(0)" @click="viewEmployee({{ $employee->id }})">👁 View
                            Profile</a>
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
                            <div class="kv">Iqama ID: <span x-text="viewingEmployee.iqama_id"></span></div>
                            <div class="kv">Email: <span x-text="viewingEmployee.email"></span></div>
                            <div class="kv">Department: <span x-text="viewingEmployee.department"></span></div>
                            <div class="kv">Join Date: <span x-text="viewingEmployee.join_date"></span></div>
                            <div class="kv">
                                Status:
                                <span class="badge" :class="viewingEmployee.status"
                                    x-text="viewingEmployee.status"></span>
                            </div>

                            <h4>Assigned Assets (<span x-text="viewingEmployee.assets?.length || 0"></span>)</h4>
                            <template x-if="viewingEmployee.assets && viewingEmployee.assets.length > 0">
                                <template x-for="asset in viewingEmployee.assets" :key="asset.id">
                                    <div class="card panel">
                                        <strong x-text="asset.name"></strong>
                                        <div class="kv">Serial: <span x-text="asset.serial_number"></span></div>
                                        <span class="badge" :class="asset.condition" x-text="asset.condition"></span>
                                    </div>
                                </template>
                            </template>
                        </div>

                        <div>
                            <h3>Quick Actions</h3>
                            <div style="display: grid; gap: 10px">
                                <button class="btn primary">Generate Clearance Paper</button>
                                <button class="btn" style="background: #22c55e; color: #fff; border-color: #22c55e;">
                                    Assign Asset
                                </button>
                                <button class="btn" style="background: #8b5cf6; color: #fff; border-color: #8b5cf6;">
                                    Assign License
                                </button>
                            </div>

                            <h4 style="margin-top: 16px">Assigned Licenses (<span
                                    x-text="viewingEmployee.licenses?.length || 0"></span>)</h4>
                            <template x-if="viewingEmployee.licenses && viewingEmployee.licenses.length > 0">
                                <div class="card panel" style="display: grid; gap: 8px">
                                    <template x-for="license in viewingEmployee.licenses" :key="license.id">
                                        <div>
                                            <strong x-text="license.name"></strong>
                                            <span class="badge active">active</span>
                                            <div class="kv">Expires: <span x-text="license.expiry_date"></span></div>
                                        </div>
                                    </template>
                                </div>
                            </template>
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
        function employeeManager() {
            return {
                formOpen: false,
                modalOpen: false,
                editMode: false,
                editingId: null,
                submitting: false,
                viewingEmployee: {},
                errors: {},
                formData: {
                    name: '',
                    iqama_id: '',
                    email: '',
                    department: '',
                    position: '',
                    join_date: ''
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

                    if (!this.formData.iqama_id || this.formData.iqama_id === '') {
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

                    // Get CSRF token from meta tag
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                ...this.formData,
                                _method: method
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.errors) {
                                this.errors = data.errors;
                                this.submitting = false;
                            } else if (data.success) {
                                window.location.reload();
                            } else {
                                throw new Error('Unexpected response');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Something went wrong. Please try again.');
                            this.submitting = false;
                        });
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
                                join_date: data.join_date
                            };
                            this.formOpen = true;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to load employee data');
                        });
                },

                viewEmployee(id) {
                    fetch(`/employees/${id}`)
                        .then(response => response.json())
                        .then(data => {
                            this.viewingEmployee = data;
                            this.modalOpen = true;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to load employee details');
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
