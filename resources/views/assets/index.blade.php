@extends('layouts.backendLayout')
@section('title', 'Assets')

@section('content')
    <main class="page" x-data="assetManager()">
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
                <h1 class="h1">Asset Management</h1>
                <p class="sub">Track and manage company assets</p>
            </div>
            <button class="btn primary" @click="openCreateForm()">+ Add Asset</button>
        </div>

        <!-- Search & Filter Section -->
        <div class="card panel" style="margin-bottom: 16px">
            <div class="search-row">
                <input class="input" placeholder="Search assets..." x-model="searchQuery" @input="filteredAssets" />
                <select class="select" style="width: 200px" x-model="filterStatus" @change="filteredAssets">
                    <option value="">All Status</option>
                    <option value="assigned">Assigned</option>
                    <option value="available">Available</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="retired">Retired</option>
                </select>
            </div>
        </div>

        <!-- Add/Edit Asset Form -->
        <article class="card panel" x-show="formOpen" x-transition style="display: none; margin-bottom: 16px;">
            <h3 x-text="editMode ? 'Edit Asset' : 'Add New Asset'"></h3>

            <form @submit.prevent="submitForm()">
                <div class="form-grid" style="margin-top: 10px">
                    <div class="form-row">
                        <label>Asset Name</label>
                        <input type="text" class="input" x-model="formData.name" />
                        <span class="error-text" x-show="errors.name" x-text="errors.name"></span>
                    </div>

                    <div class="form-row">
                        <label>Type</label>
                        <select class="input" x-model="formData.type">
                            <option value="">Select Type</option>
                            <option value="Laptop">Laptop</option>
                            <option value="Phone">Phone</option>
                            <option value="Monitor">Monitor</option>
                            <option value="Tablet">Tablet</option>
                            <option value="Printer">Printer</option>
                            <option value="Keyboard">Keyboard</option>
                            <option value="Mouse">Mouse</option>
                            <option value="Headset">Headset</option>
                            <option value="Other">Other</option>
                        </select>
                        <span class="error-text" x-show="errors.type" x-text="errors.type"></span>
                    </div>

                    <div class="form-row">
                        <label>Serial Number</label>
                        <input type="text" class="input" x-model="formData.serial_number" />
                        <span class="error-text" x-show="errors.serial_number" x-text="errors.serial_number"></span>
                    </div>

                    <div class="form-row">
                        <label>Brand</label>
                        <input type="text" class="input" x-model="formData.brand" />
                        <span class="error-text" x-show="errors.brand" x-text="errors.brand"></span>
                    </div>

                    <div class="form-row">
                        <label>Model</label>
                        <input type="text" class="input" x-model="formData.model" />
                        <span class="error-text" x-show="errors.model" x-text="errors.model"></span>
                    </div>

                    <div class="form-row">
                        <label>Purchase Date</label>
                        <input type="date" class="input" x-model="formData.purchase_date" />
                        <span class="error-text" x-show="errors.purchase_date" x-text="errors.purchase_date"></span>
                    </div>

                    <div class="form-row">
                        <label>Warranty Expiry</label>
                        <input type="date" class="input" x-model="formData.warranty_expiry" />
                        <span class="error-text" x-show="errors.warranty_expiry" x-text="errors.warranty_expiry"></span>
                    </div>

                    <div class="form-row">
                        <label>Value ($)</label>
                        <input type="number" class="input" x-model="formData.value" step="0.01" min="0" />
                        <span class="error-text" x-show="errors.value" x-text="errors.value"></span>
                    </div>

                    <div class="form-row">
                        <label>Condition</label>
                        <select class="input" x-model="formData.condition">
                            <option value="excellent">Excellent</option>
                            <option value="good">Good</option>
                            <option value="fair">Fair</option>
                            <option value="poor">Poor</option>
                        </select>
                        <span class="error-text" x-show="errors.condition" x-text="errors.condition"></span>
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
                        <span x-show="!submitting" x-text="editMode ? 'Update Asset' : 'Add Asset'"></span>
                        <span x-show="submitting">Processing...</span>
                    </button>
                </div>
            </form>
        </article>

        <!-- Asset Cards -->
        <h3 style="margin-bottom: 12px;">All Assets</h3>
        <section class="grid-3">
            @forelse($assets as $asset)
                <article class="card asset" data-name="{{ $asset->name }}"
                    data-code="{{ $asset->asset_code }}" data-type="{{ $asset->type }}"
                    data-serial="{{ $asset->serial_number }}" data-brand="{{ $asset->brand }}"
                    data-status="{{ $asset->status }}">
                    <div class="title">
                        {{ $asset->name }}
                        <span class="badge {{ $asset->status }}">{{ $asset->status }}</span>
                    </div>
                    <div class="kv">Serial Number: {{ $asset->asset_code }}</div>
                    <div class="kv">Value: ${{ number_format($asset->value, 2) }}</div>
                    <div class="kv">
                        Condition:
                        <span class="badge {{ $asset->condition }}">{{ $asset->condition }}</span>
                    </div>
                    <div class="kv">Warranty: {{ $asset->warranty_expiry }}</div>
                    <div class="note">
                        @if ($asset->currentEmployee->first())
                        Assigned to: <strong>{{ $asset->currentEmployee->first()->name }}</strong><br />{{ $asset->currentEmployee->first()->email }}
                        @else
                            <span style="color: #666;">Unassigned</span>
                        @endif
                    </div>
                    <button class="btn ghost" @click="viewAsset({{ $asset->id }})">
                        🗂 Manage
                    </button>
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
                            <div class="kv">Warranty Expiry: <strong x-text="viewingAsset.warranty_expiry"></strong>
                            </div>
                            <div class="kv">
                                Warranty Status:
                                <span class="badge" :class="viewingAsset.is_warranty_expired ? 'expired' : 'active'"
                                    x-text="viewingAsset.is_warranty_expired ? 'Expired' : 'Active'"></span>
                            </div>
                            <div class="kv">Original Value: <strong>$<span
                                        x-text="viewingAsset.value"></span></strong></div>
                            <div class="kv">Depreciated Value: <strong>$<span
                                        x-text="viewingAsset.depreciated_value"></span></strong></div>
                            <div class="kv">
                                Condition:
                                <span class="badge" :class="viewingAsset.condition"
                                    x-text="viewingAsset.condition"></span>
                            </div>
                            <div class="kv">
                                Status:
                                <span class="badge" :class="viewingAsset.status" x-text="viewingAsset.status"></span>
                            </div>
                            <div class="kv" x-show="viewingAsset.notes">
                                Notes: <span x-text="viewingAsset.notes"></span>
                            </div>

                            <h4 style="margin-top: 16px;">Assignment History</h4>
                            <template x-if="viewingAsset.assignment_history && viewingAsset.assignment_history.length > 0">
                                <div style="display: grid; gap: 8px; margin-top: 8px;">
                                    <template x-for="history in viewingAsset.assignment_history"
                                        :key="history.employee_id">
                                        <div class="card panel" style="padding: 8px;">
                                            <strong x-text="history.employee_name"></strong>
                                            <div class="kv" style="font-size: 12px;">
                                                <span x-text="history.department"></span>
                                            </div>
                                            <div class="kv" style="font-size: 12px;">
                                                Assigned: <span x-text="history.assigned_date"></span>
                                            </div>
                                            <div class="kv" style="font-size: 12px;" x-show="history.return_date">
                                                Returned: <span x-text="history.return_date"></span>
                                            </div>
                                            <span class="badge" :class="history.status" x-text="history.status"
                                                style="font-size: 11px;"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template
                                x-if="!viewingAsset.assignment_history || viewingAsset.assignment_history.length === 0">
                                <p style="color: #666; font-size: 14px;">No assignment history</p>
                            </template>
                        </div>

                        <div>
                            <h3>Assignment Management</h3>
                            <template x-if="viewingAsset.current_employee">
                                <div class="card panel">
                                    <strong>Currently Assigned To:</strong>
                                    <div class="kv" x-text="viewingAsset.current_employee.name"></div>
                                    <div class="kv" x-text="viewingAsset.current_employee.department"></div>
                                    <div class="kv" style="font-size: 12px;">
                                        Assigned: <span x-text="viewingAsset.current_employee.assigned_date"></span>
                                    </div>
                                    <div class="kv" style="font-size: 12px;"
                                        x-show="viewingAsset.current_employee.assignment_notes">
                                        Notes: <span x-text="viewingAsset.current_employee.assignment_notes"></span>
                                    </div>

                                    <label
                                        style="display: block; margin-top: 12px; margin-bottom: 4px; font-size: 12px;">Return
                                        Notes (Optional):</label>
                                    <textarea class="textarea" x-model="returnNotes" style="font-size: 12px;" rows="2"></textarea>

                                    <button class="btn ghost" @click="unassignAsset()"
                                        style="margin-top: 8px; width: 100%;">
                                        Unassign Asset
                                    </button>
                                </div>
                            </template>

                            <template x-if="!viewingAsset.current_employee && viewingAsset.status !== 'retired'">
                                <div>
                                    <p style="color: #666; margin-bottom: 12px; font-size: 14px;">This asset is not
                                        assigned.</p>
                                    <label style="display: block; margin-bottom: 8px;">Assign to Employee:</label>
                                    <select class="input" x-model="assignEmployeeId"
                                        style="width: 100%; margin-bottom: 8px;">
                                        <option value="">Select Employee</option>
                                        @foreach ($employees as $emp)
                                            <option value="{{ $emp->id }}">{{ $emp->name }} -
                                                {{ $emp->department }}</option>
                                        @endforeach
                                    </select>

                                    <label style="display: block; margin-bottom: 4px; font-size: 12px;">Assignment Notes
                                        (Optional):</label>
                                    <textarea class="textarea" x-model="assignmentNotes" style="font-size: 12px; margin-bottom: 8px;" rows="2"></textarea>

                                    <button class="btn primary" @click="assignAssetToEmployee()" style="width: 100%;">
                                        Assign Asset
                                    </button>
                                </div>
                            </template>

                            <h4 style="margin-top: 20px;">Status Management</h4>
                            <template x-if="viewingAsset.status !== 'assigned'">
                                <div style="display: grid; gap: 8px; margin-top: 8px;">
                                    <button class="btn ghost" @click="updateAssetStatus('available')"
                                        x-show="viewingAsset.status !== 'available'">
                                        Mark as Available
                                    </button>
                                    <button class="btn ghost" @click="updateAssetStatus('maintenance')"
                                        x-show="viewingAsset.status !== 'maintenance'">
                                        Mark as Maintenance
                                    </button>
                                    <button class="btn ghost" @click="updateAssetStatus('retired')"
                                        x-show="viewingAsset.status !== 'retired'">
                                        Mark as Retired
                                    </button>
                                </div>
                            </template>
                            <template x-if="viewingAsset.status === 'assigned'">
                                <p style="color: #666; font-size: 12px; margin-top: 8px;">
                                    Unassign asset first to change status
                                </p>
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
                submitting: false,
                viewingAsset: {},
                assignEmployeeId: '',
                assignmentNotes: '',
                returnNotes: '',
                errors: {},
                searchQuery: '',
                filterStatus: '',

                // Flash message properties
                flashMessage: '',
                flashType: '',

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

                init() {
                    this.checkFlashMessage();
                },

                showFlash(message, type = 'success') {
                    this.flashMessage = message;
                    this.flashType = type;

                    setTimeout(() => {
                        this.flashMessage = '';
                    }, 4000);
                },

                checkFlashMessage() {
                    const flash = localStorage.getItem('flashMessage');
                    if (flash) {
                        const data = JSON.parse(flash);
                        this.showFlash(data.message, data.type);
                        localStorage.removeItem('flashMessage');
                    }
                },

                setFlashMessage(message, type = 'success') {
                    localStorage.setItem('flashMessage', JSON.stringify({
                        message: message,
                        type: type
                    }));
                },

                get filteredAssets() {
                    const cards = Array.from(document.querySelectorAll('.asset'));

                    cards.forEach(card => {
                        const name = card.dataset.name.toLowerCase();
                        const code = card.dataset.code.toLowerCase();
                        const type = card.dataset.type.toLowerCase();
                        const serial = card.dataset.serial.toLowerCase();
                        const brand = card.dataset.brand.toLowerCase();
                        const status = card.dataset.status.toLowerCase();

                        const search = this.searchQuery.toLowerCase();
                        const statusFilter = this.filterStatus.toLowerCase();

                        const matchesSearch = !search ||
                            name.includes(search) ||
                            code.includes(search) ||
                            type.includes(search) ||
                            serial.includes(search) ||
                            brand.includes(search);

                        const matchesStatus = !statusFilter || statusFilter === 'all' || status ===
                            statusFilter;

                        if (matchesSearch && matchesStatus) {
                            card.style.display = '';
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    const visibleCards = cards.filter(card => card.style.display !== 'none');
                    const noResultsDiv = document.querySelector('.no-results');

                    if (visibleCards.length === 0) {
                        if (!noResultsDiv) {
                            const section = document.querySelector('.grid-3');
                            const div = document.createElement('div');
                            div.className = 'no-results';
                            div.style.cssText = 'grid-column: 1/-1; text-align: center; padding: 40px;';
                            div.innerHTML = '<p>No assets found matching your criteria.</p>';
                            section.appendChild(div);
                        }
                    } else {
                        if (noResultsDiv) {
                            noResultsDiv.remove();
                        }
                    }
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
                        this.errors.name = 'Asset name is required';
                        isValid = false;
                    }

                    if (!this.formData.type) {
                        this.errors.type = 'Asset type is required';
                        isValid = false;
                    }

                    if (!this.formData.serial_number || this.formData.serial_number.trim() === '') {
                        this.errors.serial_number = 'Serial number is required';
                        isValid = false;
                    }

                    if (!this.formData.brand || this.formData.brand.trim() === '') {
                        this.errors.brand = 'Brand is required';
                        isValid = false;
                    }

                    if (!this.formData.model || this.formData.model.trim() === '') {
                        this.errors.model = 'Model is required';
                        isValid = false;
                    }

                    if (!this.formData.purchase_date || this.formData.purchase_date.trim() === '') {
                        this.errors.purchase_date = 'Purchase date is required';
                        isValid = false;
                    }

                    if (!this.formData.warranty_expiry || this.formData.warranty_expiry.trim() === '') {
                        this.errors.warranty_expiry = 'Warranty expiry is required';
                        isValid = false;
                    }

                    if (!this.formData.value || this.formData.value < 0) {
                        this.errors.value = 'Valid asset value is required';
                        isValid = false;
                    }

                    if (!this.formData.condition) {
                        this.errors.condition = 'Asset condition is required';
                        isValid = false;
                    }

                    return isValid;
                },

                submitForm() {
                    if (!this.validateForm()) {
                        return;
                    }

                    this.submitting = true;

                    const url = this.editMode ? `/asset-management/${this.editingId}` : '/asset-management';
                    const method = this.editMode ? 'PUT' : 'POST';

                    const formData = {
                        ...this.formData,
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
                            this.setFlashMessage(data.message, 'success');
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

                editAsset(id) {
                    fetch(`/asset-management/${id}/edit`)
                        .then(response => response.json())
                        .then(data => {
                            this.editMode = true;
                            this.editingId = id;
                            this.errors = {};

                            if (data.purchase_date) {
                                data.purchase_date = this.convertToDateInput(data.purchase_date);
                            }
                            if (data.warranty_expiry) {
                                data.warranty_expiry = this.convertToDateInput(data.warranty_expiry);
                            }

                            this.formData = data;
                            this.formOpen = true;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            this.showFlash('Failed to load asset data', 'error');
                        });
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

                viewAsset(id) {
                    fetch(`/asset-management/${id}`)
                        .then(response => response.json())
                        .then(data => {
                            this.viewingAsset = data;
                            this.assignEmployeeId = '';
                            this.assignmentNotes = '';
                            this.returnNotes = '';
                            this.modalOpen = true;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            this.showFlash('Failed to load asset details', 'error');
                        });
                },

                editAssetFromModal() {
                    const id = this.viewingAsset.id;
                    this.closeModal();
                    setTimeout(() => {
                        this.editAsset(id);
                    }, 300);
                },

                deleteAssetFromModal() {
                    if (!confirm(`Are you sure you want to delete ${this.viewingAsset.name}?`)) {
                        return;
                    }

                    fetch(`/asset-management/${this.viewingAsset.id}`, {
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
                            this.showFlash(data.message || 'Failed to delete asset', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.showFlash('Failed to delete asset', 'error');
                    });
                },

                assignAssetToEmployee() {
                    if (!this.assignEmployeeId) {
                        this.showFlash('Please select an employee', 'error');
                        return;
                    }

                    fetch(`/asset-management/${this.viewingAsset.id}/assign`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            employee_id: this.assignEmployeeId,
                            assignment_notes: this.assignmentNotes
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.setFlashMessage(data.message, 'success');
                            window.location.reload();
                        } else {
                            this.showFlash(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.showFlash('Failed to assign asset', 'error');
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
                        },
                        body: JSON.stringify({
                            return_notes: this.returnNotes
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.setFlashMessage(data.message, 'success');
                            window.location.reload();
                        } else {
                            this.showFlash(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.showFlash('Failed to unassign asset', 'error');
                    });
                },

                updateAssetStatus(status) {
                    if (!confirm(`Are you sure you want to mark this asset as ${status}?`)) {
                        return;
                    }

                    fetch(`/asset-management/${this.viewingAsset.id}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            status: status
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.setFlashMessage(data.message, 'success');
                            window.location.reload();
                        } else {
                            this.showFlash(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.showFlash('Failed to update status', 'error');
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
                    this.errors = {};
                    this.submitting = false;
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
                }
            }
        }
    </script>


@endsection
