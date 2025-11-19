╔═══════════════════════════════════════════════════════════════╗
║              EMPLOYEE CLEARANCE CERTIFICATE                   ║
║                Official Separation Document                   ║
╚═══════════════════════════════════════════════════════════════╝

Date of Issue: {{ date('F d, Y') }}
Time of Issue: {{ date('h:i A') }}
Document ID: CLR-{{ $employee->id }}-{{ date('Ymd-His') }}

═══════════════════════════════════════════════════════════════

EMPLOYEE INFORMATION:
───────────────────────────────────────────────────────────────
Full Name:         {{ $employee->name }}
Iqama ID:          {{ $employee->iqama_id }}
Email Address:     {{ $employee->email }}
Department:        {{ $employee->department }}
Position:          {{ $employee->position }}
Join Date:         {{ $employee->join_date }}
Employment Status: {{ strtoupper($employee->status) }}

═══════════════════════════════════════════════════════════════

ASSIGNED ASSETS ({{ $employee->assets->count() }}):
───────────────────────────────────────────────────────────────
@if($employee->assets->count() > 0)
@foreach($employee->assets as $index => $asset)

{{ $index + 1 }}. {{ $asset->name }}
   Asset Code:      {{ $asset->asset_code }}
   Type:            {{ $asset->type }}
   Serial Number:   {{ $asset->serial_number }}
   Brand:           {{ $asset->brand }}
   Model:           {{ $asset->model }}
   Condition:       {{ strtoupper($asset->condition) }}
   Assigned Date:   {{ $asset->pivot->assigned_date ?? 'N/A' }}
@if($asset->pivot->assignment_notes ?? false)
   Notes:           {{ $asset->pivot->assignment_notes }}
@endif
@endforeach
@else

   ✓ No assets currently assigned
@endif

═══════════════════════════════════════════════════════════════

ASSIGNED LICENSES ({{ $employee->licenses->count() }}):
───────────────────────────────────────────────────────────────
@if($employee->licenses->count() > 0)
@foreach($employee->licenses as $index => $license)

{{ $index + 1 }}. {{ $license->name ?? 'Unknown License' }}
   License Type:    {{ $license->type ?? 'N/A' }}
   Assigned Date:   {{ $license->pivot->assigned_date ?? 'N/A' }}
   Expiry Date:     {{ $license->pivot->expiry_date ?? 'N/A' }}
   Status:          {{ strtoupper($license->pivot->status ?? 'active') }}
@endforeach
@else

   ✓ No licenses currently assigned
@endif

═══════════════════════════════════════════════════════════════

CLEARANCE STATUS CHECKLIST:
───────────────────────────────────────────────────────────────

☐ All company assets returned and verified
☐ All access cards and badges surrendered
☐ Company documents and files returned
☐ Email and system access revoked
☐ Outstanding expenses settled
☐ Exit interview completed
☐ Final settlement processed
☐ Company property inspection done
☐ Knowledge transfer completed
☐ Handover documentation submitted

═══════════════════════════════════════════════════════════════

DEPARTMENT CLEARANCES:
───────────────────────────────────────────────────────────────

☐ HUMAN RESOURCES DEPARTMENT
   Cleared By: _________________________  Date: ___________
   Signature:  _________________________

☐ INFORMATION TECHNOLOGY DEPARTMENT
   Cleared By: _________________________  Date: ___________
   Signature:  _________________________

☐ FINANCE & ACCOUNTS DEPARTMENT
   Cleared By: _________________________  Date: ___________
   Signature:  _________________________

☐ ADMINISTRATION DEPARTMENT
   Cleared By: _________________________  Date: ___________
   Signature:  _________________________

☐ DIRECT SUPERVISOR/MANAGER
   Cleared By: _________________________  Date: ___________
   Signature:  _________________________

☐ DEPARTMENT HEAD
   Cleared By: _________________________  Date: ___________
   Signature:  _________________________

═══════════════════════════════════════════════════════════════

CERTIFICATION:
───────────────────────────────────────────────────────────────

This clearance certificate confirms that the above-named employee
has completed all necessary procedures for separation from the
company and has cleared all obligations.


Authorized Signature: _____________________  Date: ___________

Company Seal:


═══════════════════════════════════════════════════════════════
             This is an official computer-generated document
           Generated on: {{ date('F d, Y \a\t h:i A') }}
═══════════════════════════════════════════════════════════════
