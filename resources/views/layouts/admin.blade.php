<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Music Studio') - Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f5;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        
        /* Top Header */
        .top-header {
            background: #e9ecef;
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1001;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .logo {
            width: 40px;
            height: 40px;
            background: #ffc107;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #000;
        }
        
        .brand-text {
            font-weight: bold;
            color: #333;
            font-size: 1.1rem;
        }
        
        .user-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .notification-icon {
            color: #6c757d;
            font-size: 1.2rem;
        }
        
        .admin-text {
            font-weight: 500;
            color: #333;
        }
        
        /* Sidebar */
        .sidebar {
            width: 200px;
            background: #495057;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 70px;
            z-index: 1000;
            padding-top: 0;
        }
        
        .sidebar-header {
            padding: 1rem;
            border-bottom: 1px solid #6c757d;
        }
        
        .sidebar-title {
            color: #fff;
            font-size: 0.85rem;
            font-weight: 500;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-nav li {
            border-bottom: 1px solid #6c757d;
        }
        
        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #fff;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: #ffc107;
            color: #000;
        }
        
        .sidebar-nav i {
            width: 20px;
            margin-right: 0.75rem;
            font-size: 0.9rem;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 200px;
            margin-top: 70px;
            padding: 0;
            min-height: calc(100vh - 70px);
            background: #f5f5f5;
        }
        
        /* Content Styles */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        
        .search-section {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .search-input {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
        }
        
        .search-btn {
            background: #ffc107;
            border: 1px solid #ffc107;
            color: #000;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .search-btn:hover {
            background: #ffca2c;
            border-color: #ffca2c;
        }
        
        /* Filters */
        .filters-section {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .filter-select {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
            background: #fff;
        }
        
        /* Table Styles */
        .bookings-table {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .table {
            margin: 0;
        }
        
        .table thead th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
            padding: 1rem 0.75rem;
            font-size: 0.9rem;
        }
        
        .table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
        }
        
        .table tbody tr:hover {
            background: #f8f9fa;
        }
        
        /* Status Badges */
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-accepted {
            background: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        /* Dashboard Container */
        .dashboard-container {
            padding: 20px;
            background: #f8f9fa;
            min-height: 100vh;
        }

        /* Booking Info */
        .booking-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .client-name {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .service-type {
            font-size: 12px;
            color: #666;
        }

        .attachments {
            font-size: 11px;
            color: #007bff;
            text-decoration: underline;
            cursor: pointer;
        }

        .attachments:hover {
            color: #0056b3;
        }

        /* Date Time Info */
        .datetime-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .date {
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }

        .time {
            font-size: 12px;
            color: #666;
        }

        /* Action Buttons */
        .btn-action {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-rejected {
            background: #6c757d;
            color: white;
            cursor: not-allowed;
        }

        /* Utility Classes */
        .text-center {
            text-align: center;
        }

        .py-4 {
            padding: 20px 0;
        }

        .empty-state {
            color: #666;
            font-style: italic;
        }
        
        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .search-section {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .search-input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 250px;
            font-size: 14px;
        }

        .search-btn {
            padding: 8px 16px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .search-btn:hover {
            background: #0056b3;
        }

        /* Filters Section */
        .filters-section {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .filter-select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: white;
            font-size: 14px;
            cursor: pointer;
        }

        /* Bookings Table */
        .bookings-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-accept {
            background: #28a745;
            color: white;
        }

        .btn-accept:hover {
            background: #218838;
        }

        .btn-reject {
            background: #dc3545;
            color: white;
        }

        .btn-reject:hover {
            background: #c82333;
        }

        .btn-reschedule {
            background: #ffc107;
            color: #212529;
        }

        .btn-reschedule:hover {
            background: #e0a800;
        }

        /* Form Elements */
        .form-check-input {
            margin: 0;
            cursor: pointer;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }

            .search-section {
                justify-content: center;
            }

            .search-input {
                width: 200px;
            }

            .filters-section {
                flex-wrap: wrap;
            }

            .table {
                font-size: 12px;
            }

            .table th,
            .table td {
                padding: 8px;
            }

            .action-buttons {
                flex-direction: column;
            }
        }

        /* Action Buttons */
        .action-btn {
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            margin-right: 0.25rem;
        }
        
        .btn-reject {
            background: #dc3545;
            color: #fff;
        }
        
        .btn-accept {
            background: #28a745;
            color: #fff;
        }
        
        .btn-reschedule {
            background: #ffc107;
            color: #000;
        }
        
        .action-btn:hover {
            opacity: 0.9;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
        
        /* Custom Checkbox */
        .form-check-input {
            width: 1rem;
            height: 1rem;
        }
        
        /* Booking Details */
        .booking-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
        }
        
        .booking-details {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }
        
        .booking-adjustments {
            font-size: 0.8rem;
            color: #ffc107;
            cursor: pointer;
        }
        
        .booking-adjustments:hover {
            text-decoration: underline;
        }
        
        /* Additional Bookings Table Styles */
        .search-section {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .filters-section {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .status-accepted {
            background: #d1edff;
            color: #0c5460;
            border: 1px solid #b8daff;
        }
        
        .action-btn {
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-reject:hover {
            background-color: #c82333;
            color: white;
        }
        
        .btn-accept:hover {
            background-color: #218838;
            color: white;
        }
        
        /* New Admin Content Styles */
        .admin-content {
            background: #fff;
            min-height: calc(100vh - 70px);
        }
        
        .page-header {
            background: #fff;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .search-input {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
            width: 250px;
        }
        
        .search-btn {
            background: #ffc107;
            border: 1px solid #ffc107;
            color: #000;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
        }
        
        .search-btn:hover {
            background: #ffca2c;
            border-color: #ffca2c;
        }
        
        .filters-row {
            padding: 1rem 2rem;
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            gap: 0.75rem;
        }
        
        .filter-select {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
            background: #fff;
            min-width: 120px;
        }
        
        /* Table Styles */
        .bookings-table-container {
            background: #fff;
            margin: 0;
        }
        
        .bookings-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        
        .bookings-table thead th {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
            color: #495057;
            padding: 1rem;
            font-size: 0.9rem;
            text-align: left;
        }
        
        .bookings-table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f1f1;
        }
        
        .bookings-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .checkbox-col {
            width: 50px;
        }
        
        .name-col {
            width: 25%;
        }
        
        .status-col {
            width: 15%;
        }
        
        .email-col {
            width: 20%;
        }
        
        .datetime-col {
            width: 20%;
        }
        
        .actions-col {
            width: 20%;
        }
        
        .table-checkbox {
            width: 1rem;
            height: 1rem;
        }
        
        .booking-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
        }
        
        .booking-details {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }
        
        .booking-adjustments {
            font-size: 0.8rem;
            color: #ffc107;
            cursor: pointer;
        }
        
        .booking-adjustments:hover {
            text-decoration: underline;
        }
        
        .time-slot {
            color: #6c757d;
            font-size: 0.85rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-reject {
            background: #dc3545;
            color: #fff;
            border: none;
            padding: 0.375rem 0.75rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
        }
        
        .btn-accept {
            background: #28a745;
            color: #fff;
            border: none;
            padding: 0.375rem 0.75rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
        }
        
        .btn-reschedule {
            background: #ffc107;
            color: #000;
            border: none;
            padding: 0.375rem 0.75rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
        }
        
        .status-rejected-text {
            background: #dc3545;
            color: #fff;
            padding: 0.375rem 0.75rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .btn-reject:hover {
            background: #c82333;
        }
        
        .btn-accept:hover {
            background: #218838;
        }
        
        .btn-reschedule:hover {
            background: #e0a800;
        }
        
        /* Dashboard Specific Styles */
        .welcome-text {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            padding: 2rem;
            background: #fff;
        }
        
        .dashboard-stat-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: box-shadow 0.2s ease;
        }
        
        .dashboard-stat-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .stat-number.pending {
            color: #ffc107;
        }
        
        .stat-number.confirmed {
            color: #28a745;
        }
        
        .stat-number.cancelled {
            color: #dc3545;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        .dashboard-section {
            background: #fff;
            margin: 0;
            padding: 2rem;
            border-top: 1px solid #e9ecef;
        }
        
        .dashboard-section h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.5rem;
        }
        
        .dashboard-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .dashboard-action-btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .dashboard-action-btn:hover {
            background: #f8f9fa;
            border-color: #dee2e6;
            text-decoration: none;
            color: #333;
        }
        
        .dashboard-action-btn i {
            font-size: 1.25rem;
        }
        
        /* Analytics Specific Styles */
        .stat-icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-growth {
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 0.25rem;
        }
        
        .stat-growth.positive {
            color: #28a745;
        }
        
        .stat-growth.negative {
            color: #dc3545;
        }
        
        /* Calendar Specific Styles */
        .calendar-status-card {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.5rem;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .calendar-status-card.connected {
            border-left: 4px solid #28a745;
        }
        
        .calendar-status-card.disconnected {
            border-left: 4px solid #dc3545;
        }
        
        .calendar-status-card .status-icon {
            font-size: 2rem;
            font-weight: bold;
        }
        
        .calendar-status-card.connected .status-icon {
            color: #28a745;
        }
        
        .calendar-status-card.disconnected .status-icon {
            color: #dc3545;
        }
        
        .calendar-status-card .status-content {
            flex: 1;
        }
        
        .calendar-status-card .status-content h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }
        
        .calendar-status-card .status-content p {
            margin: 0 0 0.75rem 0;
            color: #6c757d;
        }
        
        .calendar-info {
            font-size: 0.9rem;
        }
        
        .calendar-info code {
            background: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-family: monospace;
            font-size: 0.85rem;
        }
        
        .benefits {
            margin: 0;
            padding-left: 1.25rem;
        }
        
        .benefits li {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        
        .status-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        /* Dashboard Table Styles */
        .bookings-table, .rentals-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .bookings-table th, .rentals-table th {
            background: #f8fafc;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .bookings-table td, .rentals-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .bookings-table tr:hover, .rentals-table tr:hover {
            background: #f9fafb;
        }
        
        .booking-name, .rental-name {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 2px;
        }
        
        .booking-details, .rental-details {
            font-size: 12px;
            color: #6b7280;
        }
        
        .time-slot {
            font-size: 12px;
            color: #6b7280;
        }
        
        .rental-amount {
            font-weight: 600;
            color: #059669;
        }
        
        .no-bookings, .no-rentals {
            text-align: center;
            padding: 40px;
            color: #6b7280;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        /* Quick Actions Grid */
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .quick-action-card {
            display: flex;
            align-items: center;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .quick-action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            text-decoration: none;
            color: inherit;
        }
        
        .action-icon {
            font-size: 32px;
            margin-right: 15px;
        }
        
        .action-content h3 {
            margin: 0 0 5px 0;
            color: #1f2937;
            font-size: 16px;
        }
        
        .action-content p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }
        
        /* Admin Form Styles */
        .admin-form-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .admin-form-card h3 {
            margin: 0 0 20px 0;
            color: #1f2937;
        }
        
        .input-group {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        
        .form-input {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-input.error {
            border-color: #ef4444;
        }
        
        .error-message {
            color: #ef4444;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .alert ul {
            margin: 0;
            padding-left: 20px;
        }
        
        /* Admin Dropdown Styles */
        .admin-dropdown {
            position: relative;
            display: inline-block;
        }
        
        .admin-text {
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 6px;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .admin-text:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .admin-text i {
            font-size: 0.8em;
            transition: transform 0.3s ease;
        }
        
        .admin-dropdown.active .admin-text i {
            transform: rotate(180deg);
        }
        
        .admin-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background: white;
            min-width: 320px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            z-index: 1000;
            margin-top: 8px;
            border: 1px solid #e9ecef;
            overflow: hidden;
        }
        
        .admin-dropdown-content.show {
            display: block;
            animation: dropdownFadeIn 0.3s ease;
        }
        
        @keyframes dropdownFadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .dropdown-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .dropdown-header h6 {
            margin: 0;
            font-size: 1.1em;
            font-weight: 600;
        }
        
        .admin-user-item {
            padding: 15px 20px;
            border-bottom: 1px solid #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.2s ease;
        }
        
        .admin-user-item:hover {
            background-color: #f8f9fa;
        }
        
        .admin-user-item:last-child {
            border-bottom: none;
        }
        
        .admin-user-info {
            flex: 1;
        }
        
        .admin-user-name {
            font-weight: 600;
            color: #333;
            font-size: 0.95em;
            margin-bottom: 4px;
        }
        
        .admin-user-email {
            color: #666;
            font-size: 0.85em;
        }
        
        .admin-user-status {
            margin-left: 15px;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-badge.active {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-badge.inactive {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .no-admins {
            padding: 30px 20px;
            text-align: center;
            color: #666;
        }
        
        .no-admins p {
            margin: 0;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Top Header -->
    <div class="top-header">
        <div class="logo-section">
            <div class="logo">
                🍋
            </div>
            <div>
                <div class="brand-text">LEMON</div>
                <small style="color: #6c757d;">HUB STUDIO</small>
            </div>
        </div>
        
        <div class="user-section">
            <i class="fas fa-bell notification-icon"></i>
            <div class="admin-dropdown">
                <span class="admin-text" onclick="toggleAdminDropdown()">Admin <i class="fas fa-chevron-down"></i></span>
                <div class="admin-dropdown-content" id="adminDropdown">
                    <div class="dropdown-header">
                        <h6>Admin Users</h6>
                    </div>
                    @php
                        $adminUsers = \App\Models\User::where('is_admin', true)->get();
                    @endphp
                    @foreach($adminUsers as $adminUser)
                        <div class="admin-user-item">
                            <div class="admin-user-info">
                                <div class="admin-user-name">{{ $adminUser->name }}</div>
                                <div class="admin-user-email">{{ $adminUser->email }}</div>
                            </div>
                            <div class="admin-user-status">
                                <span class="status-badge active">Active</span>
                            </div>
                        </div>
                    @endforeach
                    @if($adminUsers->isEmpty())
                        <div class="no-admins">
                            <p>No admin users found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h6 class="sidebar-title">Navigation</h6>
        </div>
        
        <ul class="sidebar-nav">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.bookings') }}" class="{{ request()->routeIs('admin.bookings') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i>
                    Bookings
                </a>
            </li>
            <li>
                <a href="{{ route('admin.instrument-bookings') }}" class="{{ request()->routeIs('admin.instrument-bookings') ? 'active' : '' }}">
                    <i class="fas fa-guitar"></i>
                    Instrument Bookings
                </a>
            </li>
            <li>
                <a href="{{ route('admin.music-lesson-bookings') }}" class="{{ request()->routeIs('admin.music-lesson-bookings') ? 'active' : '' }}">
                    <i class="fas fa-music"></i>
                    Music Lesson Bookings
                </a>
            </li>
            <li>
                <a href="{{ route('admin.calendar') }}" class="{{ request()->routeIs('admin.calendar') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i>
                    Calendar
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i>
                    Admin Users
                </a>
            </li>
            <li>
                <a href="{{ route('admin.analytics') }}" class="{{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    Reports
                </a>
            </li>
            <li>
                <a href="{{ route('admin.database') }}" class="{{ request()->routeIs('admin.database') ? 'active' : '' }}">
                    <i class="fas fa-database"></i>
                    Database Management
                </a>
            </li>
            <li>
                <a href="{{ route('admin.activity-logs') }}" class="{{ request()->routeIs('admin.activity-logs') ? 'active' : '' }}">
                    <i class="fas fa-history"></i>
                    Activity Logs
                </a>
            </li>
            <li>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    Sign Out
                </a>
            </li>
        </ul>
    </div>
    
    <!-- Hidden Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Alerts -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @yield('content')
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleAdminDropdown() {
            const dropdown = document.getElementById('adminDropdown');
            const adminDropdown = document.querySelector('.admin-dropdown');
            
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
                adminDropdown.classList.remove('active');
            } else {
                dropdown.classList.add('show');
                adminDropdown.classList.add('active');
            }
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const adminDropdown = document.querySelector('.admin-dropdown');
            const dropdown = document.getElementById('adminDropdown');
            
            if (!adminDropdown.contains(event.target)) {
                dropdown.classList.remove('show');
                adminDropdown.classList.remove('active');
            }
        });
        
        // Prevent dropdown from closing when clicking inside it
        document.getElementById('adminDropdown').addEventListener('click', function(event) {
            event.stopPropagation();
        });
    </script>
    
    @yield('scripts')
</body>
</html>