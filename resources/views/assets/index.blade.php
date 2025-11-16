@extends('layouts.backendLayout')

@section('content')
    <main class="page" x-data="assetManager()">
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
        <div style="display:flex;justify-content:space-between;align-items:center">
            <div>
                <h1 class="h1">Asset Management</h1>
                <p class="sub">Track and manage company assets</p>
            </div>
            <div><button class="btn primary" @click="openCreateForm()">+ Add Asset</button></div>
        </div>

        <!-- Search/Filter Bar -->
        <div class="card panel" style="margin-bottom:16px">
            <div class="search-row">
                <input class="input" placeholder="Search assets..." x-model="searchQuery" @input="searchAssets()">
                <select class="select" style="width:200px" x-model="statusFilter" @change="filterAssets()">
                    <option value="">All Status</option>
                    <option value="available">Available</option>
                    <option value="assigned">Assigned</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="retired">Retired</option>
                </select>
            </div>
        </div>

        <!-- Add/Edit Asset Form -->
        <article class="card panel" x-show="formOpen" x-transition style="display: none;">
            <h3 x-text="editMode ? 'Edit Asset' : 'Add New Asset'"></h3>

            <form :action="editMode ? `/asset-management/${editingId}` : '/asset-management'" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="form-grid" style="margin-top:10px">
                    <div class="form-row">
                        <label>Asset Name</label>
                        <input class="input" name="name" x-model="formData.name" required>
                    </div>

                    <div class="form-row">
                        <label>Type</label>
                        <select class="select" name="type" x-model="formData.type" required>
                            <option value="">Select Type</option>
                            <option value="Laptop">Laptop</option>
                            <option value="Phone">Phone</option>
                            <option value="Monitor">Monitor</option>
                            <option value="Tablet">Tablet</option>
                            <option value="Printer">Printer</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <label>Serial Number</label>
                        <input class="input" name="serial_number" x-model="formData.serial_number" required>
                    </div>

                    <div class="form-row">
                        <label>Brand</label>
                        <input class="input" name="brand" x-model="formData.brand" required>
                    </div>

                    <div class="form-row">
                        <label>Model</label>
                        <input class="input" name="model" x-model="formData.model" required>
                    </div>

                    <div class="form-row">
                        <label>Purchase Date</label>
                        <input class="input" name="purchase_date" x-model="formData.purchase_date" placeholder="mm/dd/yyyy" required>
                    </div>

                    <div class="form-row">
                        <label>Warranty Expiry</label>
                        <input class="input" name="warranty_expiry" x-model="formData.warranty_expiry" placeholder="mm/dd/yyyy" required>
                    </div>

                    <div class="form-row">
                        <label>Value ($)</label>
                        <input class="input" name="value" type="number" step="0.01" x-model="formData.value" required>
                    </div>

                    <div class="form-row">
                        <label>Condition</label>
                        <select class="select" name="condition" x-model="formData.condition" required>
                            <option value="excellent">Excellent</option>
                            <option value="good">Good</option>
                            <option value="fair">Fair</option>
                            <option value="poor">Poor</option>
                        </select>
                    </div>

                    <div class="form-row" style="grid-column:1/-1">
                        <label>Notes</label>
                        <textarea class="textarea" name="notes" x-model="formData.notes"></textarea>
                    </div>
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:16px">
                    <button type="button" class="btn ghost" @click="closeForm()">Cancel</button>
                    <button type="submit" class="btn primary" x-text="editMode ? 'Update Asset' : 'Add Asset'"></button>
                </div>
            </form>
        </article>

        <div style="height:16px"></div>

        <!-- Asset Cards -->
        <section class="grid-3">
            @forelse($assets as $asset)
                <article class="card asset">
                    <div class="title">
                        {{ $asset->name }}
                        <span class="badge {{ $asset->status }}">{{ $asset->status }}</span>
                    </div>
                    <div class="kv">Serial Number: {{ $asset->serial_number }}</div>
                    <div class="kv">Value: ${{ number_format($asset->value, 2) }}</div>
                    <div class="kv">Condition: <span class="badge {{ $asset->condition }}">{{ $asset->condition }}</span></div>
                    <div class="kv">Warranty: {{ $asset->warranty_expiry }}</div>
                    <div class="note">
                        @if($asset->employee)
                            Assigned to: {{ $asset->employee->name }}
                        @else
                            <span style="color: #666;">Unassigned</span>
                        @endif
                    </div>
                    <button class="btn ghost" @click="viewAsset({{ $asset->id }})">🗂 Manage</button>
                </article>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                    <p>No assets found. Click "Add Asset" to create one.</p>
                </div>
            @endforelse
        </section>

        <!-- Asset Detail Modal -->
        <div class="modal" x-show="modalOpen" x-transition style="display: none;">
            <div class="dialog">
                <div class="head">
                    <strong x-text="viewingAsset.name"></strong>
                    <button class="btn ghost" @click="closeModal()">✖</button>
                </div>
                <div class="body">
                    <div class="cols">
                        <div>
                            <h3>Asset Information</h3>
                            <div class="kv">Asset Code: <strong x-text="viewingAsset.asset_code"></strong></div>
                            <div class="kv">Type: <strong x-text="viewingAsset.type"></strong></div>
                            <div class="kv">Serial Number: <strong x-text="viewingAsset.serial_number"></strong></div>
                            <div class="kv">Brand: <strong x-text="viewingAsset.brand"></strong></div>
                            <div class="kv">Model: <strong x-text="viewingAsset.model"></strong></div>
                            <div class="kv">Purchase Date: <strong x-text="viewingAsset.purchase_date"></strong></div>
                            <div class="kv">Warranty Expiry: <strong x-text="viewingAsset.warranty_expiry"></strong></div>
                            <div class="kv">Value: <strong>$<span x-text="viewingAsset.value"></span></strong></div>
                            <div class="kv">
                                Condition:
                                <span class="badge" :class="viewingAsset.condition" x-text="viewingAsset.condition"></span>
                            </div>
                            <div class="kv">
                                Status:
                                <span class="badge" :class="viewingAsset.status" x-text="viewingAsset.status"></span>
                            </div>
                            <div class="kv" x-show="viewingAsset.notes">
                                Notes: <span x-text="viewingAsset.notes"></span>
                            </div>
                        </div>

                        <div>
                            <h3>Assignment</h3>
                            <template x-if="viewingAsset.employee">
                                <div class="card panel">
                                    <strong>Assigned to:</strong>
                                    <div class="kv" x-text="viewingAsset.employee.name"></div>
                                    <button class="btn ghost" @click="unassignAsset()" style="margin-top: 8px;">
                                        Unassign Asset
                                    </button>
                                </div>
                            </template>

                            <template x-if="!viewingAsset.employee">
                                <div>
                                    <p style="color: #666; margin-bottom: 12px;">This asset is not assigned to anyone.</p>
                                    <label style="display: block; margin-bottom: 8px;">Assign to Employee:</label>
                                    <select class="select" x-model="assignEmployeeId" style="width: 100%; margin-bottom: 8px;">
                                        <option value="">Select Employee</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn primary" @click="assignAssetToEmployee()" style="width: 100%;">
                                        Assign Asset
                                    </button>
                                </div>
                            </template>

                            <h4 style="margin-top: 20px;">Quick Actions</h4>
                            <div style="display: grid; gap: 10px;">
                                <button class="btn ghost" @click="editAssetFromModal()">✏️ Edit Asset</button>
                                <button class="btn" style="background: #ef4444; color: #fff; border-color: #ef4444;"
                                        @click="deleteAssetFromModal()">
                                    🗑️ Delete Asset
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
        function assetManager() {
            return {
                formOpen: false,
                modalOpen: false,
                editMode: false,
                editingId: null,
                viewingAsset: {},
                searchQuery: '',
                statusFilter: '',
                assignEmployeeId: '',
                formData: {
                    name: '',
                    type: '',
                    serial_number: '',
                    brand: '',
                    model: '',
                    purchase_date: '',
                    warranty_expiry: '',
                    value: 0,
                    condition: 'good',
                    notes: ''
                },

                openCreateForm() {
                    this.editMode = false;
                    this.editingId = null;
                    this.resetForm();
                    this.formOpen = true;
                },

                editAsset(id) {
                    fetch(`/asset-management/${id}/edit`)
                        .then(response => response.json())
                        .then(data => {
                            this.editMode = true;
                            this.editingId = id;
                            this.formData = data;
                            this.formOpen = true;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to load asset data');
                        });
                },

                viewAsset(id) {
                    fetch(`/asset-management/${id}`)
                        .then(response => response.json())
                        .then(data => {
                            this.viewingAsset = data;
                            this.assignEmployeeId = '';
                            this.modalOpen = true;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to load asset details');
                        });
                },

                editAssetFromModal() {
                    this.closeModal();
                    setTimeout(() => {
                        this.editAsset(this.viewingAsset.id);
                    }, 300);
                },

                deleteAssetFromModal() {
                    if (!confirm(`Are you sure you want to delete ${this.viewingAsset.name}?`)) {
                        return;
                    }

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/asset-management/${this.viewingAsset.id}`;

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';

                    form.appendChild(csrfInput);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                },

                assignAssetToEmployee() {
                    if (!this.assignEmployeeId) {
                        alert('Please select an employee');
                        return;
                    }

                    fetch(`/asset-management/${this.viewingAsset.id}/assign`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            employee_id: this.assignEmployeeId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            alert(data.message);
                            location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to assign asset');
                    });
                },

                unassignAsset() {
                    if (!confirm('Are you sure you want to unassign this asset?')) {
                        return;
                    }

                    fetch(`/asset-management/${this.viewingAsset.id}/unassign`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            alert(data.message);
                            location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to unassign asset');
                    });
                },

                closeModal() {
                    this.modalOpen = false;
                    this.viewingAsset = {};
                },

                closeForm() {
                    this.formOpen = false;
                    this.resetForm();
                },

                resetForm() {
                    this.formData = {
                        name: '',
                        type: '',
                        serial_number: '',
                        brand: '',
                        model: '',
                        purchase_date: '',
                        warranty_expiry: '',
                        value: 0,
                        condition: 'good',
                        notes: ''
                    };
                },

                searchAssets() {
                    // Implement search logic
                    console.log('Searching:', this.searchQuery);
                },

                filterAssets() {
                    // Implement filter logic
                    console.log('Filtering:', this.statusFilter);
                }
            }
        }
    </script>
@endsection
