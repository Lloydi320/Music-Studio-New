@extends('layouts.admin')

@section('title', 'Admin Users Management')

@section('content')
<div class="admin-content">
    <!-- Modern Header -->
    <div class="admin-header">
        <div class="header-content">
            <div class="header-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="#4285f4"/>
                    <path d="M12 14C7.58172 14 4 17.5817 4 22H20C20 17.5817 16.4183 14 12 14Z" fill="#4285f4"/>
                </svg>
            </div>
            <div class="header-text">
                <h1>User Management</h1>
                <p>Manage administrator access and permissions</p>
            </div>
        </div>
        
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <!-- Grant Admin Access Section -->
    @php
        $currentUser = Auth::user();
        $currentAdminUser = DB::table('admin_users')->where('email', $currentUser->email)->first();
        $isSuperAdmin = $currentAdminUser && $currentAdminUser->role === 'super_admin';
    @endphp

    @if($isSuperAdmin)
    <div class="modern-card">
        <div class="card-header">
            <div class="card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M16 21V19C16 17.9391 15.5786 16.9217 14.8284 16.1716C14.0783 15.4214 13.0609 15 12 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="8.5" cy="7" r="4" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="20" y1="8" x2="20" y2="14" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="17" y1="11" x2="23" y2="11" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="card-title">
                <h2>Grant Admin Access</h2>
                <p>Add a new administrator to the system</p>
            </div>
            <button class="toggle-btn" onclick="toggleSection('grant-admin')">
                <span id="grant-admin-icon">−</span>
            </button>
        </div>
        
        <div id="grant-admin-content">
        <form action="{{ route('admin.makeAdmin') }}" method="POST" class="modern-form">
            @csrf
            <div class="form-group">
                <label for="email" class="form-label">User Email</label>
                <input type="email" id="email" name="email" class="form-input" required 
                       placeholder="Enter user's email address">
                <div class="form-help">Enter the email address of the user you want to make an admin</div>
            </div>
            
            <button type="submit" class="btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="M16 21V19C16 17.9391 15.5786 16.9217 14.8284 16.1716C14.0783 15.4214 13.0609 15 12 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="8.5" cy="7" r="4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="20" y1="8" x2="20" y2="14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="17" y1="11" x2="23" y2="11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Grant Admin Access
            </button>
        </form>
        </div>
    </div>
    @endif

    <!-- Current Administrators -->
    <div class="modern-card">
        <div class="card-header">
            <div class="card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M17 21V19C17 17.9391 16.5786 16.9217 15.8284 16.1716C15.0783 15.4214 14.0609 15 13 15H5C3.93913 15 2.92172 15.4214 2.17157 16.1716C1.42143 16.9217 1 17.9391 1 19V21" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="9" cy="7" r="4" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M23 21V19C23 18.1645 22.7155 17.3541 22.2094 16.6977C21.7033 16.0414 20.9983 15.5759 20.2 15.3726" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16 3.13C16.8604 3.35031 17.623 3.85071 18.1676 4.55232C18.7122 5.25392 19.0078 6.11683 19.0078 7.005C19.0078 7.89317 18.7122 8.75608 18.1676 9.45768C17.623 10.1593 16.8604 10.6597 16 10.88" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="card-title">
                <h2>Current Administrators</h2>
                <p>List of all current administrators</p>
            </div>
            <button class="toggle-btn" onclick="toggleSection('current-admins')">
                <span id="current-admins-icon">−</span>
            </button>
        </div>
        
        <div id="current-admins-content">
        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created By</th>
                        @if($isSuperAdmin)
                        <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                        $adminUsers = DB::table('admin_users')->orderBy('role', 'desc')->orderBy('name')->get();
                    @endphp
                    
                    @foreach($adminUsers as $admin)
                    <tr>
                        <td>{{ $admin->name }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>
                            <span class="role-badge role-{{ $admin->role }}">
                                {{ ucfirst(str_replace('_', ' ', $admin->role)) }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $admin->is_active ? 'active' : 'inactive' }}">
                                {{ $admin->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $admin->created_by ?? 'System' }}</td>
                        @if($isSuperAdmin)
                        <td>
                            @if($admin->role !== 'super_admin' && $admin->email !== $currentUser->email)
                            <form action="{{ route('admin.removeAdmin') }}" method="POST" 
                                  style="display: inline-block;"
                                  onsubmit="return confirm('Are you sure you want to remove admin privileges from {{ $admin->name }}?')">
                                @csrf
                                <input type="hidden" name="admin_email" value="{{ $admin->email }}">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-user-minus"></i> Remove Admin
                                </button>
                            </form>
                            @else
                            <span class="text-muted">Protected</span>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        </div>
    </div>

    <!-- User Profile Cards -->
    <div class="modern-card">
        <div class="card-header">
            <div class="card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="7" r="4" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="card-title">
                <h2>User Profiles</h2>
                <p>Detailed information about system users</p>
            </div>
            <button class="toggle-btn" onclick="toggleSection('user-profiles')">
                <span id="user-profiles-icon">−</span>
            </button>
        </div>
        
        <div id="user-profiles-content">
        <div class="user-profiles-grid">
            @php
                $allUsers = \App\Models\User::orderBy('name')->get();
            @endphp
            
            @foreach($allUsers as $user)
            <div class="user-profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <div class="avatar-circle">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        @if($user->is_admin)
                        <div class="admin-badge">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                                <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#34a853" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        @endif
                    </div>
                    <div class="profile-info">
                        <h3>{{ $user->name }}</h3>
                        <p class="user-role">{{ $user->is_admin ? 'Admin' : 'User' }}</p>
                        <p class="user-location">{{ $user->email }}</p>
                    </div>
                </div>
                
                <div class="profile-details">
                    <div class="detail-section">
                        <h4>Personal Information</h4>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">First Name</span>
                                <span class="detail-value">{{ explode(' ', $user->name)[0] ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Last Name</span>
                                <span class="detail-value">{{ explode(' ', $user->name)[1] ?? 'N/A' }}</span>
                            </div>
                            
                            <div class="detail-item">
                                <span class="detail-label">Email Address</span>
                                <span class="detail-value">{{ $user->email }}</span>
                            </div>
                            
                            <div class="detail-item">
                                <span class="detail-label">User Role</span>
                                <span class="detail-value">{{ $user->is_admin ? 'Admin' : 'User' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h4>Account Information</h4>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">Member Since</span>
                                <span class="detail-value">{{ $user->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Last Login</span>
                                <span class="detail-value">{{ $user->updated_at->format('M d, Y') }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Status</span>
                                <span class="detail-value status-active">Active</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="profile-actions">
                    @php
                        // Determine if this profile belongs to an administrator
                        $adminRecord = DB::table('admin_users')->where('email', $user->email)->first();
                        $isAdminAccount = $user->is_admin || $adminRecord;
                        $canDeleteAdmin = $isSuperAdmin && (!$adminRecord || $adminRecord->role !== 'super_admin');
                        $canDelete = (!$isAdminAccount && $currentUser->is_admin) || ($isAdminAccount && $canDeleteAdmin);
                    @endphp
                    @if($canDelete && $user->id !== $currentUser->id)
                    <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger btn-sm user-delete-btn" data-user-name="{{ $user->name }}">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                <path d="M3 6h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M8 6V4h8v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M19 6l-1 14H6L5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            Delete
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        </div>
    </div>

    <!-- Regular Users with Admin Access -->
    <div class="modern-card">
        <div class="card-header">
            <div class="card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="7" r="4" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="card-title">
                <h2>Users with Admin Access</h2>
                <p>Regular users who have been granted admin privileges</p>
            </div>
            <button class="toggle-btn" onclick="toggleSection('admin-users')">
                <span id="admin-users-icon">−</span>
            </button>
        </div>
        
        <div id="admin-users-content">
        <div class="table-container">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Joined</th>
                        @if($isSuperAdmin)
                        <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                        $regularAdmins = \App\Models\User::where('is_admin', true)->orderBy('name')->get();
                    @endphp
                    
                    @foreach($regularAdmins as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        @if($isSuperAdmin)
                        <td>
                            @php
                                $userAdminRecord = DB::table('admin_users')->where('email', $user->email)->first();
                                $isProtected = $userAdminRecord && $userAdminRecord->role === 'super_admin';
                            @endphp
                            
                            @if(!$isProtected && $user->id !== $currentUser->id)
                            <form action="{{ route('admin.removeAdmin') }}" method="POST" 
                                  style="display: inline-block;"
                                  onsubmit="return confirm('Are you sure you want to remove admin privileges from {{ $user->name }}?')">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-user-minus"></i> Remove Admin
                                </button>
                            </form>
                            @else
                            <span class="text-muted">Protected</span>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        </div>
    </div>

    @if(!$isSuperAdmin)
    <div class="modern-card">
        <div class="alert-info">
            <div class="alert-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="#FFD700" stroke-width="2"/>
                    <path d="M12 16V12" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 8H12.01" stroke="#FFD700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="alert-content">
                <strong>Access Restricted</strong>
                <p>Only super administrators can add or remove admin users.</p>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
/* Modern Dark Theme Admin Users Styling */
.admin-content {
    max-width: 100%;
    margin: 0;
    padding: 24px;
    background: #1a1a1a;
    min-height: 100vh;
    color: #e0e0e0;
}

/* Header Styling */
.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    padding: 24px;
    background: #2a2a2a;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    border: 1px solid #3a3a3a;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 16px;
}

.header-icon {
    width: 48px;
    height: 48px;
    background: #3a3a3a;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.header-text h1 {
    margin: 0;
    font-size: 28px;
    font-weight: 500;
    color: #FFD700;
}

.header-text p {
    margin: 4px 0 0 0;
    color: #b0b0b0;
    font-size: 14px;
}

.header-actions {
    display: flex;
    gap: 12px;
}

.theme-toggle {
    width: 40px;
    height: 40px;
    border: none;
    background: #3a3a3a;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    color: #FFD700;
}

.theme-toggle:hover {
    background: #FFD700;
    color: #1a1a1a;
}

/* User Profile Cards Styles */
.user-profiles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 24px;
    margin-top: 24px;
}

.user-profile-card {
    background: #2a2a2a;
    border-radius: 12px;
    border: 1px solid #3a3a3a;
    overflow: hidden;
    transition: all 0.3s ease;
}

.user-profile-card:hover {
    box-shadow: 0 4px 20px rgba(255, 215, 0, 0.2);
    transform: translateY(-2px);
    border-color: #FFD700;
}

.profile-header {
    padding: 24px;
    background: linear-gradient(135deg, #3a3a3a 0%, #2a2a2a 100%);
    display: flex;
    align-items: center;
    gap: 16px;
}

.profile-avatar {
    position: relative;
}

.avatar-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #FFD700;
    color: #1a1a1a;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: 600;
}

.admin-badge {
    position: absolute;
    bottom: -2px;
    right: -2px;
    background: white;
    border-radius: 50%;
    padding: 4px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.profile-info h3 {
    margin: 0 0 4px 0;
    font-size: 20px;
    font-weight: 600;
    color: #e0e0e0;
}

.user-role {
    margin: 0 0 4px 0;
    font-size: 14px;
    font-weight: 500;
    color: #FFD700;
}

.user-location {
    margin: 0;
    font-size: 14px;
    color: #b0b0b0;
}

.profile-details {
    padding: 24px;
}

.detail-section {
    margin-bottom: 24px;
}

.detail-section:last-child {
    margin-bottom: 0;
}

.detail-section h4 {
    margin: 0 0 16px 0;
    font-size: 16px;
    font-weight: 600;
    color: #FFD700;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.detail-label {
    font-size: 12px;
    font-weight: 500;
    color: #b0b0b0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    font-size: 14px;
    color: #e0e0e0;
    font-weight: 500;
}

.detail-value.status-active {
    color: #34a853 !important;
}

.profile-actions {
    padding: 16px 24px;
    background: #3a3a3a;
    border-top: 1px solid #4a4a4a;
    display: flex;
    justify-content: flex-end;
}

.edit-btn {
    background: #FFD700;
    color: #1a1a1a;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.edit-btn:hover {
    background: #FFA500;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
}

/* Modern Card Styling */
.modern-card {
    background: #2a2a2a;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    border: 1px solid #3a3a3a;
    margin-bottom: 24px;
    overflow: hidden;
}

.card-header {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 24px;
    border-bottom: 1px solid #3a3a3a;
}

.card-icon {
    width: 40px;
    height: 40px;
    background: #3a3a3a;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-title h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 500;
    color: #FFD700;
}

.card-title p {
    margin: 4px 0 0 0;
    color: #b0b0b0;
    font-size: 14px;
}

/* Modern Form Styling */
.modern-form {
    padding: 24px;
}

.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #FFD700;
    font-size: 14px;
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #3a3a3a;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
    background: #1a1a1a;
    color: #e0e0e0;
}

.form-input:focus {
    outline: none;
    border-color: #FFD700;
    box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
}

.form-help {
    margin-top: 6px;
    font-size: 12px;
    color: #b0b0b0;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: #FFD700;
    color: #1a1a1a;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary:hover {
    background: #FFA500;
    box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
}

/* Table Styling */
.table-container {
    overflow-x: auto;
}

.modern-table {
    width: 100%;
    border-collapse: collapse;
}

.modern-table th,
.modern-table td {
    padding: 16px;
    text-align: left;
    border-bottom: 1px solid #3a3a3a;
}

.modern-table th {
    background: #3a3a3a;
    font-weight: 500;
    color: #FFD700;
    font-size: 14px;
}

.modern-table tbody tr:hover {
    background: #3a3a3a;
}

.modern-table td {
    font-size: 14px;
    color: #e0e0e0;
}

/* Badge Styling */
.role-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.role-super_admin {
    background: #fef7cd;
    color: #7c2d12;
    border: 1px solid #fed7aa;
}

.role-admin {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #bfdbfe;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 500;
}

.status-active {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.status-inactive {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

/* Button Styling */
.btn-sm {
    padding: 8px 16px;
    font-size: 12px;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}

.btn-danger {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: #ea4335;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-danger:hover {
    background: #d33b2c;
    box-shadow: 0 2px 8px rgba(234, 67, 53, 0.3);
}

.btn-danger:focus {
    outline: 2px solid #fca5a5;
    outline-offset: 2px;
}

.btn-danger:disabled {
    background: #a1a1a1;
    cursor: not-allowed;
    opacity: 0.7;
    box-shadow: none;
}

/* Loading state for Delete button */
.btn-danger.loading {
    position: relative;
    pointer-events: none;
}

.btn-danger .btn-spinner {
    display: none;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255,255,255,0.5);
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
    margin-right: 8px;
    vertical-align: middle;
}

.btn-danger.loading .btn-spinner {
    display: inline-block;
}

@keyframes spin { to { transform: rotate(360deg); } }

.text-muted {
    color: #6b7280;
    font-style: italic;
    font-size: 12px;
}

/* Alert Styling */
.alert-success {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #1a3a1a;
    border: 1px solid #34a853;
    border-radius: 8px;
    margin-bottom: 24px;
    color: #4ade80;
}

.alert-error {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #3a1a1a;
    border: 1px solid #ea4335;
    border-radius: 8px;
    margin-bottom: 24px;
    color: #f87171;
}

.alert-info {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 20px;
    background: #1a2a3a;
    border: 1px solid #FFD700;
    border-radius: 8px;
    color: #FFD700;
}

.alert-icon {
    flex-shrink: 0;
    margin-top: 2px;
}

.alert-content strong {
    display: block;
    margin-bottom: 4px;
    font-weight: 500;
}

.alert-content p {
    margin: 0;
    font-size: 14px;
    line-height: 1.4;
}

/* Modal Styles */
.edit-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    animation: fadeIn 0.3s ease;
}

/* Confirmation modal overlay */
.confirm-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    animation: fadeIn 0.3s ease;
}

.modal-content {
    background: #2a2a2a;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    animation: slideIn 0.3s ease;
    border: 1px solid #3a3a3a;
}

.modal-header {
    padding: 24px 24px 0 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: #FFD700;
}

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    color: #b0b0b0;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.close-btn:hover {
    background: #3a3a3a;
    color: #FFD700;
}

.modal-body {
    padding: 24px;
}

.modal-body p {
    margin: 0 0 16px 0;
    color: #b0b0b0;
    line-height: 1.5;
}

.modal-body p:last-child {
    margin-bottom: 0;
}

.modal-footer {
    padding: 0 24px 24px 24px;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.btn-secondary {
    background: #3a3a3a;
    color: #e0e0e0;
    border: 1px solid #4a4a4a;
    border-radius: 8px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: #4a4a4a;
    border-color: #FFD700;
    color: #FFD700;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .admin-content {
        padding: 16px;
    }
    
    .admin-header {
        flex-direction: column;
        gap: 16px;
        text-align: center;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .card-header {
        flex-direction: column;
        text-align: center;
        gap: 12px;
    }
    
    .modern-table {
        font-size: 12px;
    }
    
    .modern-table th,
    .modern-table td {
        padding: 12px 8px;
    }
    
    .user-profiles-grid {
        grid-template-columns: 1fr;
    }
    
    .detail-grid {
        grid-template-columns: 1fr;
    }
    
    .modal-content {
        width: 95%;
        margin: 20px;
    }
}

/* Toggle Button Styles */
.toggle-btn {
    background: linear-gradient(135deg, #ffd700, #ffed4e);
    border: none;
    border-radius: 8px;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(255, 215, 0, 0.3);
    color: #1a1a1a;
    font-weight: bold;
    font-size: 16px;
}

.toggle-btn:hover {
    background: linear-gradient(135deg, #ffed4e, #ffd700);
    box-shadow: 0 4px 12px rgba(255, 215, 0, 0.4);
    transform: translateY(-1px);
}

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 24px;
    border-bottom: 1px solid #333;
}

.section-content {
    transition: all 0.3s ease;
}
</style>

<script>
function toggleTheme() {
    // Theme toggle functionality
    const body = document.body;
    body.classList.toggle('dark-theme');
    
    // Save theme preference
    const isDark = body.classList.contains('dark-theme');
    localStorage.setItem('admin-theme', isDark ? 'dark' : 'light');
}

function editUser(userId) {
    // Create a modal or redirect to edit page
    const modal = document.createElement('div');
    modal.className = 'edit-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit User</h3>
                <button onclick="closeModal()" class="close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <p>Edit functionality for user ID: ${userId}</p>
                <p>This would typically open a form to edit user details.</p>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal()" class="btn-secondary">Cancel</button>
                <button onclick="saveUser(${userId})" class="btn-primary">Save Changes</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

function closeModal() {
    const modal = document.querySelector('.edit-modal');
    if (modal) {
        modal.remove();
    }
}

function saveUser(userId) {
    // Implement save functionality
    alert(`Saving changes for user ${userId}`);
    closeModal();
}

function toggleSection(sectionId) {
    const content = document.getElementById(sectionId + '-content');
    const icon = document.getElementById(sectionId + '-icon');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.textContent = '−';
    } else {
        content.style.display = 'none';
        icon.textContent = '+';
    }
}

// Confirm delete modal logic
function openDeleteModal(message, onConfirm) {
    const overlay = document.getElementById('confirm-delete-overlay');
    if (!overlay) {
        if (typeof onConfirm === 'function') onConfirm();
        return;
    }
    const msg = overlay.querySelector('#confirm-delete-message');
    const okBtn = overlay.querySelector('#confirm-delete-ok');
    const okLabel = okBtn.querySelector('.btn-label');
    const cancelBtns = overlay.querySelectorAll('#confirm-delete-cancel, #confirm-delete-cancel-footer');
    const cancelFooter = overlay.querySelector('#confirm-delete-cancel-footer');
    msg.textContent = message;
    overlay.style.display = 'flex';
    overlay.focus();
    if (cancelFooter) cancelFooter.focus(); // default focus on Cancel for safety
    okBtn.onclick = function() {
        // show loading state to prevent double submits
        okBtn.disabled = true;
        okBtn.setAttribute('aria-busy', 'true');
        okBtn.classList.add('loading');
        if (okLabel) {
            okBtn.dataset.originalText = okLabel.textContent;
            okLabel.textContent = 'Deleting...';
        } else {
            okBtn.dataset.originalText = okBtn.textContent;
            okBtn.textContent = 'Deleting...';
        }
        if (typeof onConfirm === 'function') onConfirm();
    };
    cancelBtns.forEach(function(btn) {
        btn.onclick = function() {
            overlay.style.display = 'none';
            okBtn.disabled = false;
            okBtn.removeAttribute('aria-busy');
            okBtn.classList.remove('loading');
            if (okLabel && okBtn.dataset.originalText) {
                okLabel.textContent = okBtn.dataset.originalText;
            }
        };
    });

    // Keyboard support: Esc to cancel, Enter activates focused button
    overlay.onkeydown = function(e) {
        if (e.key === 'Escape') {
            e.preventDefault();
            cancelFooter && cancelFooter.click();
        } else if (e.key === 'Enter' && document.activeElement === okBtn) {
            e.preventDefault();
            okBtn.click();
        }
    };
}

// Load saved theme and wire delete buttons
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('admin-theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
    }

    // Hook delete buttons
    document.querySelectorAll('.user-delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = btn.closest('form');
            const name = btn.getAttribute('data-user-name') || 'this user';
            openDeleteModal(`Are you sure you want to delete ${name}? This action cannot be undone.`, function() {
                form.submit();
            });
        });
    });

    // Close when clicking outside modal
    const overlay = document.getElementById('confirm-delete-overlay');
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                overlay.style.display = 'none';
            }
        });
    }
});
</script>

<!-- Confirm Delete Modal -->
<div id="confirm-delete-overlay" class="confirm-overlay" role="dialog" aria-modal="true" aria-labelledby="confirm-delete-title" aria-describedby="confirm-delete-message" tabindex="-1">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="confirm-delete-title">Confirm Deletion</h3>
            <button id="confirm-delete-cancel" class="close-btn" aria-label="Close">&times;</button>
        </div>
        <div class="modal-body">
            <p id="confirm-delete-message">Are you sure you want to delete this user? This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <button id="confirm-delete-cancel-footer" class="btn-secondary">Cancel</button>
            <button id="confirm-delete-ok" class="btn-danger" type="button">
                <span class="btn-spinner" aria-hidden="true"></span>
                <span class="btn-label">Delete</span>
            </button>
        </div>
    </div>
</div>
@endsection