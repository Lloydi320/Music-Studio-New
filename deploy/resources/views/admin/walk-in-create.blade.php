@extends('layouts.admin')

@section('title', 'Walk-In Booking')

@section('content')
<style>
/* Walk-In Booking Page Styles */
.admin-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #2a2a2a;
    border: 1px solid #444;
    border-left: 4px solid #ffd700;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    margin: 1rem 0 1.25rem 0;
}
.header-content { display: flex; align-items: center; gap: 12px; }
.header-icon svg { display:block; }
.header-text h1 { font-size: 1.25rem; margin: 0; }
.header-text p { margin: 0; font-size: 0.9rem; color: #bbb; }

.btn-primary, .btn-secondary { display: inline-flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 8px; font-weight: 600; text-decoration: none; cursor: pointer; border: none; }
.btn-primary { background: #ffd700; color: #1a1a1a; }
.btn-primary:hover { background: #ffca2c; }
.btn-secondary { background: #333; color: #e0e0e0; border: 2px solid #3a3a3a; }
.btn-secondary:hover { background: #3a3a3a; }

.modern-card { background: #2a2a2a; border: 1px solid #444; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.35); margin-bottom: 1.25rem; }
.modern-card .card-header { display: flex; align-items: center; gap: 12px; padding: 1rem 1.25rem; border-bottom: 2px solid #ffd700; }
.modern-card .card-icon svg { display:block; }
.modern-card .card-title h2 { margin: 0; font-size: 1.2rem; color: #ffd700; }
.modern-card .card-title p { margin: 4px 0 0 0; color: #bbb; font-size: 0.9rem; }
.modern-card .card-content { padding: 1.25rem; }

.modern-form .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px; }
.modern-form .form-group { display:flex; flex-direction: column; }
.modern-form .form-label { font-weight: 600; color: #e0e0e0; margin-bottom: 6px; }
.modern-form .form-input, .modern-form .form-select { background: #1f1f1f; color: #e0e0e0; border: 1px solid #444; border-radius: 8px; padding: 10px 12px; font-size: 0.95rem; }
.modern-form .form-input:focus, .modern-form .form-select:focus { outline: none; border-color: #ffd700; box-shadow: 0 0 0 2px rgba(255,215,0,0.15); }
.modern-form .form-help { font-size: 0.8rem; color: #999; margin-top: 6px; }

.form-actions { display:flex; gap: 10px; }

/* Filter bar styling */
.filter-bar { background: #252525; border: 1px solid #3a3a3a; border-left: 4px solid #ffd700; border-radius: 10px; padding: 12px; margin-bottom: 12px; }
.filter-bar .form-row { grid-template-columns: 1.2fr 1.2fr 1fr auto; align-items: end; }
.filter-bar .btn-primary, .filter-bar .btn-secondary { height: 42px; }

.table-container { overflow-x: auto; }
.modern-table { width: 100%; border-collapse: collapse; }
.modern-table th { background: #1f1f1f; color: #ffd700; text-align: left; padding: 10px; border-bottom: 2px solid #ffd700; }
.modern-table td { padding: 10px; color: #e0e0e0; border-bottom: 1px solid #444; }
.modern-table tbody tr:hover { background: #333; }

.badge { display:inline-block; padding: 6px 10px; border-radius: 6px; font-weight: 600; font-size: 0.8rem; }
.status-accepted { background: #28a745; color: #fff; }
.status-pending { background: #f0ad4e; color: #1a1a1a; }
.status-rejected { background: #dc3545; color: #fff; }

@media (max-width: 900px) { .modern-form .form-row { grid-template-columns: 1fr; } }
</style>
<div class="admin-content">
    <!-- Header -->
    <div class="admin-header">
        <div class="header-content">
            <div class="header-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 12h18" stroke="#ffd700" stroke-width="2" stroke-linecap="round"/>
                    <path d="M12 3v18" stroke="#ffd700" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="header-text">
                <h1 style="color:#ffd700;">Walk-In Booking</h1>
                <p style="color:#cccccc;">Create studio, solo rehearsal, or music lesson walk-ins</p>
            </div>
        </div>
        
    </div>

    <!-- Create Form -->
    <div class="modern-card">
        <div class="card-header">
            <div class="card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2a10 10 0 100 20 10 10 0 000-20z" stroke="#ffd700" stroke-width="2"/>
                    <path d="M12 6v6l4 2" stroke="#ffd700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="card-title">
                <h2>New Walk-In Booking</h2>
                <p>Fill out details for the walk-in booking</p>
            </div>
        </div>

        <div class="card-content">
            @if ($errors->any())
                <div class="alert alert-error" style="margin-bottom: 1rem;">
                    <ul style="margin:0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('admin.walk-in.store') }}" method="POST" class="modern-form">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label for="service_type" class="form-label">Service Type</label>
                        <select id="service_type" name="service_type" class="form-select" required>
                            @foreach($serviceTypes as $key => $label)
                                <option value="{{ $key }}" {{ old('service_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="form-help">Choose band rehearsal, solo rehearsal, or music lesson</div>
                    </div>
                    <div class="form-group">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" id="date" name="date" class="form-input" value="{{ old('date', now()->toDateString()) }}" required>
                        <div class="form-help">Select booking date</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="start_time" class="form-label">Start Time</label>
                        <select id="start_time" name="start_time" class="form-select" required>
                            @php
                                $times = [];
                                $start = \Carbon\Carbon::createFromTime(8,0);
                                $end = \Carbon\Carbon::createFromTime(20,0);
                                while ($start <= $end) { $times[] = $start->format('h:i A'); $start->addHour(); }
                            @endphp
                            @foreach($times as $t)
                                <option value="{{ $t }}" {{ old('start_time') === $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                        <div class="form-help">Opening hours: 08:00 AM - 08:00 PM</div>
                        <div class="form-help" id="availability_message" style="color:#f0ad4e;"></div>
                    </div>
                    <div class="form-group">
                        <label for="duration" class="form-label">Duration (hours)</label>
                        <select id="duration" name="duration" class="form-select" required>
                            @for($i=1;$i<=8;$i++)
                                <option value="{{ $i }}" {{ old('duration') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        <div class="form-help">Max 8 hours per booking</div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="band_name" class="form-label">Band/Student Name</label>
                        <input type="text" id="band_name" name="band_name" class="form-input" value="{{ old('band_name') }}" placeholder="Optional">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" id="lesson_type_group" style="display:none;">
                        <label for="lesson_type" class="form-label">Music Lesson</label>
                        <select id="lesson_type" name="lesson_type" class="form-select">
                            @php
                                $lessonOptions = [
                                    'Voice Lesson',
                                    'Drum Lesson',
                                    'Guitar Lesson',
                                    'Ukulele Lesson',
                                    'Bass Guitar Lesson',
                                    'Keyboard Lesson',
                                ];
                            @endphp
                            @foreach($lessonOptions as $opt)
                                <option value="{{ $opt }}" {{ old('lesson_type') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        <div class="form-help">Select the instrument for the music lesson</div>
                    </div>
                </div>

                

                <div class="form-row" id="estimated_total_group">
                    <div class="form-group" style="flex:1 1 100%">
                        <label class="form-label">Estimated Total</label>
                        <div class="form-input" id="estimated_total" style="background:#2a2a2a; color:#ffd700;">₱0.00</div>
                        <div class="form-help">Band/Solo: package rates. Music lessons have no total here.</div>
                    </div>
                </div>

                

                <div class="form-actions" style="margin-top: 1rem; display:flex; gap:10px;">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-check"></i>
                        Create Walk-In
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Walk-In Booking History -->
    <div class="modern-card">
        <div class="card-header">
            <div class="card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 4h16v16H4z" stroke="#ffd700" stroke-width="2"/>
                    <path d="M4 9h16" stroke="#ffd700" stroke-width="2"/>
                </svg>
            </div>
            <div class="card-title">
                <h2>Walk-In Booking History</h2>
                <p>Latest 10 walk-in bookings</p>
            </div>
        </div>
        <div class="card-content">
            <!-- Manual Booking History Filters -->
            <form method="GET" action="{{ route('admin.walk-in.create') }}" class="modern-form filter-bar">
                <div class="form-row">
                    <div class="form-group">
                        <label for="from_date" class="form-label">From Date</label>
                        <input type="date" id="from_date" name="from_date" class="form-input" value="{{ $historyFilters['from_date'] ?? '' }}" />
                    </div>
                    <div class="form-group">
                        <label for="to_date" class="form-label">To Date</label>
                        <input type="date" id="to_date" name="to_date" class="form-input" value="{{ $historyFilters['to_date'] ?? '' }}" />
                    </div>
                    <div class="form-group">
                        <label for="filter_service_type" class="form-label">Service Type</label>
                        <select id="filter_service_type" name="filter_service_type" class="form-select">
                            <option value="all" {{ (($historyFilters['service_type'] ?? 'all') === 'all') ? 'selected' : '' }}>All</option>
                            <option value="studio_rental" {{ (($historyFilters['service_type'] ?? 'all') === 'studio_rental') ? 'selected' : '' }}>Band Rehearsal</option>
                            <option value="solo_rehearsal" {{ (($historyFilters['service_type'] ?? 'all') === 'solo_rehearsal') ? 'selected' : '' }}>Solo Rehearsal</option>
                            <option value="music_lesson" {{ (($historyFilters['service_type'] ?? 'all') === 'music_lesson') ? 'selected' : '' }}>Music Lesson</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" style="visibility:hidden;">Apply</label>
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Apply Filters</button>
                            <a href="{{ route('admin.walk-in.create') }}" class="btn-secondary">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
            <div class="table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Type</th>
                            <th>Band/Student</th>
                            <th>Reference</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentWalkIns as $b)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($b->date)->format('M d, Y') }}</td>
                                <td>{{ $b->time_slot }}</td>
                                <td>{{ $b->getServiceTypeLabel() }}</td>
                                <td>{{ $b->band_name ?? '—' }}</td>
                                <td><code>{{ $b->reference }}</code></td>
                                <td>
                                    @if($b->status === 'confirmed')
                                        <span class="badge status-accepted">Confirmed</span>
                                    @elseif($b->status === 'pending')
                                        <span class="badge status-pending">Pending</span>
                                    @else
                                        <span class="badge status-rejected">{{ ucfirst($b->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.bookings.show', $b->id) }}" class="btn-secondary action-btn">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center; color:#bbb;">No walk-in bookings yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Pricing matrix and total estimation
function computeBandSolo(type, duration) {
    if (type === 'studio_rental') { // Band
        const rate = duration === 1 ? 230 : 200;
        const total = duration === 1 ? 230 : 200 * duration;
        return { rate, total };
    }
    if (type === 'solo_rehearsal') { // Solo
        const rate = duration === 1 ? 200 : 180;
        const total = duration === 1 ? 200 : 180 * duration;
        return { rate, total };
    }
    return { rate: 0, total: 0 };
}

function refreshPricing() {
    const type = document.getElementById('service_type').value;
    const duration = parseInt(document.getElementById('duration').value || '0', 10);
    let total = 0;

    if (type === 'music_lesson') {
        // Music lesson: no pricing shown on walk-in booking
        total = 0;
    } else {
        // Band/Solo: apply matrix
        const { total: matrixTotal } = computeBandSolo(type, duration);
        total = matrixTotal;
    }

    document.getElementById('estimated_total').textContent = `₱${(total || 0).toFixed(2)}`;
}

async function updateStartTimeAvailability() {
    const date = document.getElementById('date').value;
    const duration = parseInt(document.getElementById('duration').value || '0', 10);
    const type = document.getElementById('service_type').value;
    const select = document.getElementById('start_time');
    const msgEl = document.getElementById('availability_message');

    if (!date || !duration || !type) return;
    msgEl.textContent = '';

    try {
        const url = `{{ route('admin.walk-in.availability') }}` + `?date=${encodeURIComponent(date)}&duration=${duration}&service_type=${encodeURIComponent(type)}`;
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error('Failed to fetch availability');
        const data = await res.json();
        const disabled = new Set(data.disabled_times || []);

        let firstEnabled = null;
        Array.from(select.options).forEach(opt => {
            const shouldDisable = disabled.has(opt.value);
            opt.disabled = shouldDisable;
            opt.style.color = shouldDisable ? '#777' : '';
            if (!shouldDisable && firstEnabled === null) firstEnabled = opt.value;
        });
        // If currently selected is disabled, auto-select first enabled
        if (select.selectedOptions.length && select.selectedOptions[0].disabled && firstEnabled) {
            select.value = firstEnabled;
        }

        if (data.message) {
            msgEl.textContent = data.message;
        }
    } catch (e) {
        // Fallback: disable past times when date is today
        const today = new Date();
        const selected = new Date(date);
        if (!isNaN(selected) && selected.toDateString() === today.toDateString()) {
            Array.from(select.options).forEach(opt => {
                const [time, meridian] = opt.value.split(' ');
                let [hour, minute] = time.split(':').map(n => parseInt(n, 10));
                if (meridian === 'PM' && hour !== 12) hour += 12;
                if (meridian === 'AM' && hour === 12) hour = 0;
                const optDate = new Date(today.getFullYear(), today.getMonth(), today.getDate(), hour, minute);
                opt.disabled = optDate < today;
                opt.style.color = opt.disabled ? '#777' : '';
            });
        }
    }
}

document.getElementById('service_type').addEventListener('change', () => { refreshPricing(); updateStartTimeAvailability(); });
document.getElementById('duration').addEventListener('change', () => { refreshPricing(); updateStartTimeAvailability(); });
document.getElementById('date').addEventListener('change', updateStartTimeAvailability);

// Initialize on load
refreshPricing();
updateStartTimeAvailability();

// Toggle fields based on service type (show lesson type for music lessons, hide estimated total)
function toggleLessonAndTotal() {
    const type = document.getElementById('service_type').value;
    const lessonGroup = document.getElementById('lesson_type_group');
    const totalGroup = document.getElementById('estimated_total_group');
    if (type === 'music_lesson') {
        if (lessonGroup) lessonGroup.style.display = '';
        if (totalGroup) totalGroup.style.display = 'none';
    } else {
        if (lessonGroup) lessonGroup.style.display = 'none';
        if (totalGroup) totalGroup.style.display = '';
    }
}

document.getElementById('service_type').addEventListener('change', toggleLessonAndTotal);
toggleLessonAndTotal();
</script>
@endsection