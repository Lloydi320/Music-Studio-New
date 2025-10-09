@extends('layouts.admin')

@section('title', 'Carousel Management')

@section('content')
<!-- Cropper.js CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">

<style>
    .carousel-management {
        padding: 20px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid #e0e0e0;
    }

    .page-title {
        font-size: 2rem;
        color: #333;
        margin: 0;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        color: white;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .carousel-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .carousel-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .carousel-card:hover {
        transform: translateY(-5px);
    }

    .carousel-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .carousel-content {
        padding: 20px;
    }

    .carousel-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }

    .carousel-description {
        color: #666;
        margin-bottom: 15px;
        line-height: 1.5;
    }

    .carousel-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        font-size: 0.9rem;
        color: #888;
    }

    .carousel-actions {
        display: flex;
        gap: 10px;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 0.85rem;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-warning {
        background-color: #f39c12;
        color: white;
    }

    .btn-danger {
        background-color: #e74c3c;
        color: white;
    }

    .btn-warning:hover, .btn-danger:hover {
        opacity: 0.8;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: white;
        margin: 5% auto;
        padding: 30px;
        border-radius: 12px;
        width: 90%;
        max-width: 600px;
        max-height: 80vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e0e0e0;
    }

    .modal-title {
        font-size: 1.5rem;
        color: #333;
        margin: 0;
    }

    .close {
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        color: #aaa;
        z-index: 1001;
        position: relative;
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }

    .close:hover {
        color: #333;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #667eea;
    }

    .form-check {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-active {
        background-color: #d4edda;
        color: #155724;
    }

    .status-inactive {
        background-color: #f8d7da;
        color: #721c24;
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        font-weight: 500;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 20px;
    }

    .empty-state-text {
        font-size: 1.2rem;
        margin-bottom: 10px;
    }

    .empty-state-subtext {
        color: #888;
    }
</style>

<div class="carousel-management">
    <div class="page-header">
        <h1 class="page-title">🎵 Music Lesson Teachers</h1>
        <button class="btn-primary" onclick="openModal('addModal')">
            ➕ Add New Teacher
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($carouselItems->count() > 0)
        <div class="carousel-grid">
            @foreach($carouselItems as $item)
                <div class="carousel-card">
                    <img src="{{ asset('images/carousel/' . $item->image_path) }}" alt="{{ $item->title }}" class="carousel-image">
                    <div class="carousel-content">
                        <h3 class="carousel-title">{{ $item->title }}</h3>
                        <p class="carousel-description">{{ Str::limit($item->description, 100) }}</p>
                        <div class="carousel-meta">
                            <span class="status-badge {{ $item->is_active ? 'status-active' : 'status-inactive' }}">
                                {{ $item->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="carousel-actions">
                            <button class="btn-sm btn-warning" onclick="editItem({{ $item->id }}, {{ json_encode($item->title) }}, {{ json_encode($item->description) }}, {{ json_encode($item->expertise ?? '') }}, '{{ $item->order_position }}', {{ $item->is_active ? 'true' : 'false' }})">
                                ✏️ Edit
                            </button>
                            <button class="btn-sm btn-danger" onclick="deleteItem({{ $item->id }})">
                                🗑️ Delete
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">🎵</div>
            <div class="empty-state-text">No teachers found</div>
            <div class="empty-state-subtext">Add your first music lesson teacher to get started</div>
        </div>
    @endif
</div>

<!-- Add Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Add New Music Lesson Teacher</h2>
            <span class="close" onclick="document.getElementById('addModal').style.display='none'">&times;</span>
        </div>
        <form action="{{ route('admin.carousel.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label" style="color: #FFD700; font-weight: 600; margin-bottom: 8px; display: block;">Teacher Name</label>
                <input type="text" name="title" class="form-control" placeholder="Enter teacher's name" required>
            </div>
            <div class="form-group">
                <label class="form-label" style="color: #FFD700; font-weight: 600; margin-bottom: 8px; display: block;">Description</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Enter teacher's bio and specialties" required></textarea>
            </div>
            <div class="form-group">
                <label class="form-label" style="color: #FFD700; font-weight: 600; margin-bottom: 8px; display: block;">Expertise</label>
                <textarea name="expertise" class="form-control" rows="3" placeholder="Enter teacher's areas of expertise (e.g., Piano, Guitar, Vocals, Music Theory)" required></textarea>
            </div>
            <div class="form-group">
                <label class="form-label" style="color: #FFD700; font-weight: 600; margin-bottom: 8px; display: block;">Teacher Photo</label>
                <input type="file" name="image" id="imageInput" class="form-control" accept="image/*" required onchange="previewImage(this)">
                <div id="imagePreviewContainer" style="display: none; margin-top: 15px;">
                    <div style="margin-bottom: 10px;">
                        <label style="color: #FFD700; font-weight: 600;">Crop your image (drag to adjust):</label>
                    </div>
                    <div style="max-width: 100%; max-height: 400px; overflow: hidden;">
                        <img id="imagePreview" style="max-width: 100%; display: block;">
                    </div>
                    <div style="margin-top: 10px; text-align: center;">
                        <button type="button" class="btn-sm btn-warning" onclick="resetCrop()">Reset Crop</button>
                    </div>
                </div>
                <canvas id="croppedCanvas" style="display: none;"></canvas>
            </div>
            <!-- Hidden field for order position with auto-increment -->
            <input type="hidden" name="order_position" value="{{ $carouselItems->count() + 1 }}">
            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" name="is_active" id="is_active_add" checked>
                    <label for="is_active_add" class="form-label" style="color: #FFD700; font-weight: 600; margin-left: 8px;">Active</label>
                </div>
            </div>
            <div class="carousel-actions">
                <button type="submit" class="btn-primary">Add Teacher</button>
                <button type="button" class="btn-sm btn-danger" onclick="document.getElementById('addModal').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Edit Music Lesson Teacher</h2>
            <span class="close" onclick="document.getElementById('editModal').style.display='none'">&times;</span>
        </div>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label" style="color: #FFD700; font-weight: 600; margin-bottom: 8px; display: block;">Teacher Name</label>
                <input type="text" name="title" id="edit_title" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label" style="color: #FFD700; font-weight: 600; margin-bottom: 8px; display: block;">Description</label>
                <textarea name="description" id="edit_description" class="form-control" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label class="form-label" style="color: #FFD700; font-weight: 600; margin-bottom: 8px; display: block;">Expertise</label>
                <textarea name="expertise" id="edit_expertise" class="form-control" rows="3" placeholder="Enter teacher's areas of expertise (e.g., Piano, Guitar, Vocals, Music Theory)" required></textarea>
            </div>
            <div class="form-group">
                <label class="form-label" style="color: #FFD700; font-weight: 600; margin-bottom: 8px; display: block;">Teacher Photo (leave empty to keep current)</label>
                <input type="file" name="image" id="editImageInput" class="form-control" accept="image/*" onchange="previewEditImage(this)">
                <div id="editImagePreviewContainer" style="display: none; margin-top: 15px;">
                    <div style="margin-bottom: 10px;">
                        <label style="color: #FFD700; font-weight: 600;">Crop your image (drag to adjust):</label>
                    </div>
                    <div style="max-width: 100%; max-height: 400px; overflow: hidden;">
                        <img id="editImagePreview" style="max-width: 100%; display: block;">
                    </div>
                    <div style="margin-top: 10px; text-align: center;">
                        <button type="button" class="btn-sm btn-warning" onclick="resetEditCrop()">Reset Crop</button>
                    </div>
                </div>
                <canvas id="editCroppedCanvas" style="display: none;"></canvas>
            </div>
            <!-- Hidden field for order position -->
            <input type="hidden" name="order_position" id="edit_order_position">
            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" name="is_active" id="is_active_edit">
                    <label for="is_active_edit" class="form-label" style="color: #FFD700; font-weight: 600; margin-left: 8px;">Active</label>
                </div>
            </div>
            <div class="carousel-actions">
                <button type="submit" class="btn-primary">Update Teacher</button>
                <button type="button" class="btn-sm btn-danger" onclick="document.getElementById('editModal').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Cropper.js JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

<script>
// Image cropping variables
let cropper = null;
let editCropper = null;

function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
    // Clean up croppers when closing modals
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    if (editCropper) {
        editCropper.destroy();
        editCropper = null;
    }
    // Reset preview containers
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const editImagePreviewContainer = document.getElementById('editImagePreviewContainer');
    if (imagePreviewContainer) {
        imagePreviewContainer.style.display = 'none';
    }
    if (editImagePreviewContainer) {
        editImagePreviewContainer.style.display = 'none';
    }
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('imagePreview');
            preview.src = e.target.result;
            document.getElementById('imagePreviewContainer').style.display = 'block';
            
            // Initialize cropper
            if (cropper) {
                cropper.destroy();
            }
            cropper = new Cropper(preview, {
                aspectRatio: 16 / 9, // Fixed aspect ratio for carousel
                viewMode: 1,
                autoCropArea: 1,
                responsive: true,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewEditImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('editImagePreview');
            preview.src = e.target.result;
            document.getElementById('editImagePreviewContainer').style.display = 'block';
            
            // Initialize cropper
            if (editCropper) {
                editCropper.destroy();
            }
            editCropper = new Cropper(preview, {
                aspectRatio: 16 / 9, // Fixed aspect ratio for carousel
                viewMode: 1,
                autoCropArea: 1,
                responsive: true,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function resetCrop() {
    if (cropper) {
        cropper.reset();
    }
}

function resetEditCrop() {
    if (editCropper) {
        editCropper.reset();
    }
}

// Convert cropped image to blob and replace file input
function getCroppedImage(cropperInstance, inputId) {
    return new Promise((resolve) => {
        if (cropperInstance) {
            cropperInstance.getCroppedCanvas({
                width: 800,
                height: 450, // 16:9 aspect ratio
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            }).toBlob((blob) => {
                resolve(blob);
            }, 'image/jpeg', 0.9);
        } else {
            resolve(null);
        }
    });
}

// Handle form submission with cropped image
document.addEventListener('DOMContentLoaded', function() {
    // Handle add form submission
    const addForm = document.querySelector('#addModal form');
    if (addForm) {
        addForm.addEventListener('submit', async function(e) {
            if (cropper) {
                e.preventDefault();
                const croppedBlob = await getCroppedImage(cropper, 'imageInput');
                if (croppedBlob) {
                    const formData = new FormData(this);
                    formData.delete('image'); // Remove original file
                    formData.append('image', croppedBlob, 'cropped-image.jpg');
                    
                    // Submit form with cropped image
                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    }).then(response => {
                        if (response.ok) {
                            window.location.reload();
                        } else {
                            alert('Error uploading image. Please try again.');
                        }
                    }).catch(error => {
                        console.error('Error:', error);
                        alert('Error uploading image. Please try again.');
                    });
                }
            }
        });
    }
    
    // Handle edit form submission
    const editForm = document.getElementById('editForm');
    if (editForm) {
        editForm.addEventListener('submit', async function(e) {
            if (editCropper) {
                e.preventDefault();
                const croppedBlob = await getCroppedImage(editCropper, 'editImageInput');
                if (croppedBlob) {
                    const formData = new FormData(this);
                    formData.delete('image'); // Remove original file
                    formData.append('image', croppedBlob, 'cropped-image.jpg');
                    
                    // Submit form with cropped image
                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    }).then(response => {
                        if (response.ok) {
                            window.location.reload();
                        } else {
                            alert('Error uploading image. Please try again.');
                        }
                    }).catch(error => {
                        console.error('Error:', error);
                        alert('Error uploading image. Please try again.');
                    });
                }
            }
        });
    }
});

function editItem(id, title, description, expertise, orderPosition, isActive) {
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_expertise').value = expertise || '';
    document.getElementById('edit_order_position').value = orderPosition;
    document.getElementById('is_active_edit').checked = isActive;
    document.getElementById('editForm').action = `/admin/carousel/${id}`;
    openModal('editModal');
}

function deleteItem(id) {
    if (confirm('Are you sure you want to delete this carousel item?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/carousel/${id}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for modal closing
    window.onclick = function(event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
                // Clean up croppers when closing modals
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                if (editCropper) {
                    editCropper.destroy();
                    editCropper = null;
                }
            }
        });
    }

    // Add keyboard event listener for Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const openModals = document.querySelectorAll('.modal[style*="display: block"], .modal[style*="display:block"]');
            openModals.forEach(modal => {
                modal.style.display = 'none';
                // Clean up croppers when closing modals
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                if (editCropper) {
                    editCropper.destroy();
                    editCropper = null;
                }
            });
        }
    });
});
</script>
@endsection