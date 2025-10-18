@extends('layouts.admin')

@section('title', 'QR Configs')

<!-- Cropper.js CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">

@section('content')
<div class="admin-content">
    <div class="page-header">
        <h2 class="page-title">🧾 QR Configuration</h2>
        <div class="header-actions">
            <span class="welcome-text">Manage rehearsal and instrument rental QR codes</span>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card" style="background:#2a2a2a;border:1px solid #444;">
                <div class="card-header" style="background:#333;color:#e0e0e0;border-bottom:1px solid #444;">
                    <strong>Rehearsal QR Configs</strong>
                </div>
                <div class="card-body">
                    <p class="text-muted">Upload or update QR image and reservation fee per duration.</p>

                    <form id="rehearsalForm" method="POST" action="{{ route('admin.qr.rehearsal.store') }}" enctype="multipart/form-data" class="mb-3">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Duration (hours)</label>
                            <select name="duration_minutes" class="form-select" required>
                                @foreach(range(1,8) as $h)
                                    <option value="{{ $h * 60 }}">{{ $h }} {{ $h === 1 ? 'hour' : 'hours' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reservation Fee (PHP)</label>
                            <input type="number" name="reservation_fee_php" class="form-control" step="0.01" min="0" placeholder="e.g. 300" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">QR Image (PNG/JPG)</label>
                            <input type="file" id="rehearsalQrImage" name="qr_image" class="form-control" accept="image/png,image/jpeg" required onchange="previewRehearsalImage(this)">
                            <div id="rehearsalPreviewContainer" style="display:none; margin-top:10px;">
                                <div class="text-muted" style="margin-bottom:8px;">Crop your image (4:3, adjustable from all sides):</div>
                                <div style="max-width:100%; max-height:400px; overflow:hidden; border:1px solid #444; border-radius:4px;">
                                    <img id="rehearsalImagePreview" style="max-width:100%; display:block;">
                                </div>
                                <div style="margin-top:10px; text-align:right;">
                                    <button type="button" class="btn btn-sm btn-warning" onclick="resetRehearsalCrop()">Reset Crop</button>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">Save Rehearsal QR</button>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Duration</th>
                                    <th>Fee (PHP)</th>
                                    <th>QR Image</th>
                                    <th>Enabled</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rehearsalConfigs as $cfg)
                                    <tr>
                                        <td>{{ (int)($cfg->duration_minutes / 60) }} {{ (int)($cfg->duration_minutes / 60) === 1 ? 'hour' : 'hours' }}</td>
                                        <td>₱{{ number_format($cfg->reservation_fee_php, 2) }}</td>
                                        <td style="width:160px;">
                                            @if($cfg->qr_image_path)
                                                <img src="{{ asset('storage/' . $cfg->qr_image_path) }}" alt="QR" style="max-width:150px; border:1px solid #444; border-radius:4px;">
                                            @else
                                                <span class="text-muted">No image</span>
                                            @endif
                                        </td>
                                        <td>{{ $cfg->enabled ? 'Yes' : 'No' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No rehearsal QR configs yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card" style="background:#2a2a2a;border:1px solid #444;">
                <div class="card-header" style="background:#333;color:#e0e0e0;border-bottom:1px solid #444;">
                    <strong>Instrument Rental QR Configs</strong>
                </div>
                <div class="card-body">
                    <p class="text-muted">Upload or update QR image and reservation fee per rental type.</p>

                    <form id="rentalForm" method="POST" action="{{ route('admin.qr.rental.store') }}" enctype="multipart/form-data" class="mb-3">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Rental Type</label>
                            <select name="rental_type" class="form-select" required>
                                <option value="instruments">Instruments</option>
                                <option value="full_package">Full Package</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reservation Fee (PHP)</label>
                            <input type="number" name="reservation_fee_php" class="form-control" step="0.01" min="0" placeholder="e.g. 300 or 500" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">QR Image (PNG/JPG)</label>
                            <input type="file" id="rentalQrImage" name="qr_image" class="form-control" accept="image/png,image/jpeg" required onchange="previewRentalImage(this)">
                            <div id="rentalPreviewContainer" style="display:none; margin-top:10px;">
                                <div class="text-muted" style="margin-bottom:8px;">Crop your image (4:3, adjustable from all sides):</div>
                                <div style="max-width:100%; max-height:400px; overflow:hidden; border:1px solid #444; border-radius:4px;">
                                    <img id="rentalImagePreview" style="max-width:100%; display:block;">
                                </div>
                                <div style="margin-top:10px; text-align:right;">
                                    <button type="button" class="btn btn-sm btn-warning" onclick="resetRentalCrop()">Reset Crop</button>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">Save Rental QR</button>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Fee (PHP)</th>
                                    <th>QR Image</th>
                                    <th>Enabled</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rentalConfigs as $cfg)
                                    <tr>
                                        <td>{{ ucfirst(str_replace('_',' ', $cfg->rental_type)) }}</td>
                                        <td>₱{{ number_format($cfg->reservation_fee_php, 2) }}</td>
                                        <td style="width:160px;">
                                            @if($cfg->qr_image_path)
                                                <img src="{{ asset('storage/' . $cfg->qr_image_path) }}" alt="QR" style="max-width:150px; border:1px solid #444; border-radius:4px;">
                                            @else
                                                <span class="text-muted">No image</span>
                                            @endif
                                        </td>
                                        <td>{{ $cfg->enabled ? 'Yes' : 'No' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No rental QR configs yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <p class="mt-3 text-muted" style="font-size:0.9em;">Accepted: PNG/JPG up to 5 MB. Images saved to public storage and served via <code>/storage</code> symlink.</p>
</div>
<!-- Cropper.js JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script>
let rehearsalCropper = null;
let rentalCropper = null;

function previewRehearsalImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('rehearsalImagePreview');
            preview.src = e.target.result;
            document.getElementById('rehearsalPreviewContainer').style.display = 'block';
            if (rehearsalCropper) rehearsalCropper.destroy();
            rehearsalCropper = new Cropper(preview, {
                aspectRatio: 4 / 3,
                viewMode: 1,
                autoCropArea: 0.9,
                responsive: true,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                movable: true,
                zoomable: true,
                scalable: true,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
                dragMode: 'crop'
            });
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewRentalImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('rentalImagePreview');
            preview.src = e.target.result;
            document.getElementById('rentalPreviewContainer').style.display = 'block';
            if (rentalCropper) rentalCropper.destroy();
            rentalCropper = new Cropper(preview, {
                aspectRatio: 4 / 3,
                viewMode: 1,
                autoCropArea: 0.9,
                responsive: true,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                movable: true,
                zoomable: true,
                scalable: true,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
                dragMode: 'crop'
            });
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function resetRehearsalCrop() { if (rehearsalCropper) rehearsalCropper.reset(); }
function resetRentalCrop() { if (rentalCropper) rentalCropper.reset(); }

function getCroppedImage(cropperInstance, width, height) {
    return new Promise((resolve) => {
        if (!cropperInstance) return resolve(null);
        cropperInstance.getCroppedCanvas({
            width,
            height,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        }).toBlob((blob) => {
            resolve(blob);
        }, 'image/jpeg', 0.92);
    });
}

// Intercept form submissions to upload cropped image
document.addEventListener('DOMContentLoaded', function() {
    const rehearsalForm = document.getElementById('rehearsalForm');
    const rentalForm = document.getElementById('rentalForm');

    if (rehearsalForm) {
        rehearsalForm.addEventListener('submit', async function(e) {
            if (rehearsalCropper) {
                e.preventDefault();
                const croppedBlob = await getCroppedImage(rehearsalCropper, 1200, 900); // 4:3
                if (croppedBlob) {
                    const formData = new FormData(this);
                    formData.delete('qr_image');
                    formData.append('qr_image', croppedBlob, 'cropped-qr.jpg');
                    fetch(this.action, { method: 'POST', body: formData })
                        .then(response => {
                            if (response.redirected) {
                                window.location.href = response.url;
                            } else if (response.ok) {
                                window.location.reload();
                            } else {
                                alert('Error uploading image. Please try again.');
                            }
                        })
                        .catch(err => {
                            console.error('Upload error:', err);
                            alert('Error uploading image. Please try again.');
                        });
                }
            }
        });
    }

    if (rentalForm) {
        rentalForm.addEventListener('submit', async function(e) {
            if (rentalCropper) {
                e.preventDefault();
                const croppedBlob = await getCroppedImage(rentalCropper, 1200, 900); // 4:3
                if (croppedBlob) {
                    const formData = new FormData(this);
                    formData.delete('qr_image');
                    formData.append('qr_image', croppedBlob, 'cropped-qr.jpg');
                    fetch(this.action, { method: 'POST', body: formData })
                        .then(response => {
                            if (response.redirected) {
                                window.location.href = response.url;
                            } else if (response.ok) {
                                window.location.reload();
                            } else {
                                alert('Error uploading image. Please try again.');
                            }
                        })
                        .catch(err => {
                            console.error('Upload error:', err);
                            alert('Error uploading image. Please try again.');
                        });
                }
            }
        });
    }
});
</script>

@endsection