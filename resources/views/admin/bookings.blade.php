@extends('layouts.admin')

@section('title', 'Bookings Management')

@section('content')
<style>
    :root {
        --gradient-primary: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        --gradient-secondary: linear-gradient(135deg, #FFD700 0%, #FFED4E 100%);
        --gradient-success: linear-gradient(135deg, #34a853 0%, #4ade80 100%);
        --gradient-warning: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        --gradient-danger: linear-gradient(135deg, #ea4335 0%, #f87171 100%);
        --shadow-soft: 0 10px 40px rgba(0, 0, 0, 0.1);
        --shadow-hover: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .admin-content {
        padding: 2rem;
        background: #1a1a1a;
        min-height: 100vh;
        color: #e0e0e0;
    }

    .page-header {
        background: var(--gradient-primary);
        color: white;
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        position: relative;
        z-index: 1;
    }

    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: #2a2a2a;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: var(--shadow-soft);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: 1px solid #3a3a3a;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient-primary);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-label {
        color: #b0b0b0;
        font-weight: 500;
        margin-top: 0.5rem;
    }

    .filters-card {
        background: #2a2a2a;
        padding: 2rem;
        border-radius: 20px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 2rem;
        backdrop-filter: blur(10px);
        border: 1px solid #3a3a3a;
    }

    .filters-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        background: var(--gradient-secondary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-weight: 600;
        color: #FFD700;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    .form-control {
        padding: 0.9rem 1.2rem;
        border: 2px solid #4a4a4a;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        background: #2a2a2a;
        color: #ffffff;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
        text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    }

    .form-control:focus {
        outline: none;
        border-color: #FFD700;
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.3), inset 0 2px 4px rgba(0,0,0,0.2);
        background: #3a3a3a;
        color: #FFD700;
        text-shadow: 0 1px 3px rgba(0,0,0,0.7);
    }

    .form-control::placeholder {
        color: #FFD700;
        font-weight: 500;
        opacity: 0.8;
    }

    .form-select {
        padding: 0.9rem 1.2rem;
        border: 2px solid #4a4a4a;
        border-radius: 10px;
        font-size: 1.1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        background: #2a2a2a;
        color: #ffffff;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
        cursor: pointer;
        text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    }

    .form-select:focus {
        outline: none;
        border-color: #FFD700;
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.3), inset 0 2px 4px rgba(0,0,0,0.2);
        background: #3a3a3a;
        color: #FFD700;
        text-shadow: 0 1px 3px rgba(0,0,0,0.7);
    }

    .form-select option {
        background: #2a2a2a;
        color: #ffffff;
        padding: 0.8rem;
        font-weight: 500;
        border-bottom: 1px solid #4a4a4a;
    }

    .form-select option:hover {
        background: #FFD700;
        color: #1a1a1a;
    }

    .form-select option:selected {
        background: #FFD700;
        color: #1a1a1a;
        font-weight: 600;
    }

    .filter-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }

    .btn-primary {
        background: var(--gradient-primary);
        color: white;
    }

    .btn-secondary {
        background: #3a3a3a;
        color: #e0e0e0;
        border: 2px solid #4a4a4a;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-soft);
    }

    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: #2a2a2a;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: var(--shadow-soft);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: 1px solid #3a3a3a;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient-primary);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-label {
        color: #b0b0b0;
        font-weight: 500;
        margin-top: 0.5rem;
    }

    .filters-card {
        background: #2a2a2a;
        padding: 2rem;
        border-radius: 20px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 2rem;
        backdrop-filter: blur(10px);
        border: 1px solid #3a3a3a;
    }

    .filters-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        background: var(--gradient-secondary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-weight: 600;
        color: #FFD700;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    .form-control {
        padding: 0.75rem 1rem;
        border: 2px solid #3a3a3a;
        border-radius: 10px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        background: #1a1a1a;
        color: #e0e0e0;
    }

    .form-control:focus {
        outline: none;
        border-color: #FFD700;
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
        background: #2a2a2a;
    }

    .filter-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .bookings-table-card {
        background: #2a2a2a;
        border-radius: 20px;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        margin-bottom: 2rem;
        border: 1px solid #3a3a3a;
    }

    .table-header {
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        color: #1a1a1a;
        padding: 1.5rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-title {
        font-size: 1.6rem;
        font-weight: 800;
        margin: 0;
        color: #1a1a1a;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        letter-spacing: 0.5px;
    }

    .records-count {
        background: rgba(26, 26, 26, 0.2);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        color: #1a1a1a;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        margin: 0;
    }

    .table thead th {
        background: linear-gradient(135deg, #3a3a3a 0%, #2a2a2a 100%);
        color: #FFD700;
        font-weight: 700;
        padding: 1.2rem;
        border: none;
        text-transform: uppercase;
        font-size: 0.9rem;
        letter-spacing: 1px;
        position: sticky;
        top: 0;
        z-index: 10;
        text-shadow: 0 0 10px rgba(255, 215, 0, 0.4);
        border-bottom: 2px solid #FFD700;
    }

    .table tbody td {
        padding: 1.2rem;
        border-bottom: 1px solid #3a3a3a;
        vertical-align: middle;
        background: #2a2a2a;
        color: #f0f0f0;
        font-size: 0.95rem;
        font-weight: 500;
    }

    .table tbody tr {
        transition: all 0.3s ease;
    }

    .table tbody tr:hover {
        background: linear-gradient(135deg, #3a3a3a 0%, #2a2a2a 100%);
        transform: scale(1.01);
        box-shadow: 0 5px 15px rgba(255,215,0,0.1);
    }

    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .status-pending {
        background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
        color: #2d3436;
    }

    .status-accepted {
        background: var(--gradient-success);
        color: white;
    }

    .status-rejected {
        background: var(--gradient-danger);
        color: white;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--gradient-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .user-details {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        font-weight: 700;
        color: #ffffff;
        font-size: 1.1rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
        letter-spacing: 0.3px;
    }

    .user-email {
        color: #e0e0e0;
        font-size: 0.9rem;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        font-weight: 500;
    }

    .booking-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .service-type {
        font-weight: 700;
        color: #ffffff;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
        font-size: 1.05rem;
        letter-spacing: 0.3px;
    }

    .duration {
        color: #d0d0d0;
        font-size: 0.85rem;
        text-transform: capitalize;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    }

    .booking-dates {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        font-size: 0.85rem;
    }

    .date-time {
        color: #ffffff;
        font-weight: 600;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
        font-size: 0.95rem;
    }

    .time-slot {
        color: #d0d0d0;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        padding: 2rem;
        background: #2a2a2a;
        border-radius: 0 0 20px 20px;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #b0b0b0;
    }

    .bookings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem;
    }

    .booking-card {
        background: #3a3a3a;
        border-radius: 15px;
        box-shadow: var(--shadow-soft);
        transition: all 0.3s ease;
        border: 2px solid #4a4a4a;
        overflow: hidden;
    }

    .booking-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 70px rgba(0, 0, 0, 0.2), 0 0 30px rgba(255, 215, 0, 0.3);
        border-color: #FFD700;
    }

    .booking-card-header {
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #FFD700;
    }

    .booking-card-body {
        padding: 1.5rem;
    }

    .booking-card-footer {
        padding: 1rem 1.5rem;
        background: #2a2a2a;
        border-top: 1px solid #4a4a4a;
    }

    .booking-details {
        margin-top: 1rem;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #4a4a4a;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 700;
        color: #FFD700;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-shadow: 0 0 8px rgba(255, 215, 0, 0.3);
    }

    .detail-value {
        color: #ffffff;
        font-weight: 600;
        text-align: right;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        font-size: 1rem;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .action-buttons .btn {
        flex: 1;
        min-width: 80px;
        font-size: 0.8rem;
        padding: 0.5rem 1rem;
    }

    .btn-view {
        background: var(--gradient-secondary);
        color: #ffffff;
        border: 2px solid #FFD700;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }

    .btn-view:hover {
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        color: #1a1a1a;
        border-color: #FFD700;
        transform: translateY(-2px);
    }

    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border: none;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
        transform: translateY(-2px);
    }

    .btn-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        border: none;
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
        transform: translateY(-2px);
    }

    .btn-reschedule {
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        color: #1a1a1a;
        border: 2px solid #FFD700;
        font-weight: 600;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    }

    .btn-reschedule:hover {
        background: linear-gradient(135deg, #FFA500 0%, #FF8C00 100%);
        color: #1a1a1a;
        border-color: #FFA500;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 215, 0, 0.4);
    }

    .reference-id {
        color: #1a1a1a;
        font-weight: 700;
        font-size: 1.2rem;
        text-shadow: 1px 1px 3px rgba(255, 255, 255, 0.8);
        letter-spacing: 0.5px;
    }

    .created-date {
        color: #1a1a1a;
        font-size: 0.9rem;
        font-weight: 500;
        text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.6);
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 0.5; }
        50% { opacity: 0.8; }
    }

    .empty-state-text {
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
    }

    .empty-state-subtext {
        color: #888;
    }

    .created-date {
        color: #b0b0b0;
        font-size: 0.85rem;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .action-buttons .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 4px;
        font-weight: 500;
    }

    .action-buttons .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .action-buttons .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .action-buttons .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .action-buttons .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }

    .btn-view {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
    }

    .btn-view:hover {
        background-color: #138496;
        border-color: #117a8b;
    }

    .btn-reschedule {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
    }

    .btn-reschedule:hover {
        background-color: #e0a800;
        border-color: #d39e00;
    }

    @media (max-width: 768px) {
        .admin-content {
            padding: 1rem;
        }

        .page-header {
            padding: 1.5rem;
            background: var(--gradient-primary);
        }

        .page-title {
            font-size: 2rem;
        }

        .filters-grid {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            justify-content: stretch;
        }

        .filter-actions .btn {
            flex: 1;
        }

        .stats-overview {
            grid-template-columns: 1fr;
        }

        .bookings-grid {
            grid-template-columns: 1fr;
            padding: 1rem;
            gap: 1rem;
        }

        .booking-card-header {
            padding: 0.75rem 1rem;
            flex-direction: column;
            gap: 0.5rem;
            align-items: flex-start;
        }

        .booking-card-body {
            padding: 1rem;
        }

        .booking-card-footer {
            padding: 0.75rem 1rem;
        }

        .user-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .action-buttons {
            flex-direction: column;
        }

        .action-buttons .btn {
            width: 100%;
            margin-bottom: 0.25rem;
        }

        .detail-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.25rem;
        }

        .detail-value {
            text-align: left;
        }
    }

    /* Reschedule Modal Styles */
    .reschedule-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        animation: fadeIn 0.3s ease;
    }

    .reschedule-modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #2a2a2a;
        border-radius: 15px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
    }

    .reschedule-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
        border-bottom: 1px solid #3a3a3a;
        background: var(--gradient-primary);
        border-radius: 15px 15px 0 0;
    }

    .reschedule-modal-header h3 {
        margin: 0;
        color: white;
        font-size: 1.2rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .reschedule-modal-close {
        color: white;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .reschedule-modal-close:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: scale(1.1);
    }

    .reschedule-modal-body {
        padding: 1.5rem;
    }

    .reschedule-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding: 1.5rem;
        border-top: 1px solid #3a3a3a;
        background: #252525;
        border-radius: 0 0 15px 15px;
    }

    .reschedule-modal-footer .btn {
        min-width: 140px;
        flex: 1;
        max-width: 180px;
    }

    .date-picker {
        position: relative;
        cursor: pointer;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="%23ff6b35" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 4v10a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V4H2z"/></svg>');
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px;
        padding-right: 40px;
    }

    .date-picker::-webkit-calendar-picker-indicator {
        opacity: 0;
        cursor: pointer;
        position: absolute;
        right: 0;
        top: 0;
        width: 100%;
        height: 100%;
    }

    .date-picker:hover {
        border-color: #ff6b35;
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    }

    .form-text {
        color: #888;
        font-size: 0.8rem;
        margin-top: 0.5rem;
        display: block;
    }
</style>

<div class="admin-content">
    <div class="page-header">
        <h1 class="page-title">🎵 Bookings Management</h1>
    </div>

    <!-- Stats Overview -->
    <div class="stats-overview">
        <div class="stat-card">
            <div class="stat-number">{{ $statusCounts['all'] }}</div>
            <div class="stat-label">Total Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $statusCounts['confirmed'] }}</div>
            <div class="stat-label">Confirmed Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $statusCounts['pending'] }}</div>
            <div class="stat-label">Pending Approvals</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $statusCounts['rejected'] }}</div>
            <div class="stat-label">Rejected Bookings</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-card">
        <h3 class="filters-title">🔍 Filter Options</h3>
        <form method="GET" action="{{ route('admin.bookings') }}">
            <div class="filters-grid">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search by name, email..." value="{{ $search }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Service Type</label>
                    <select name="service_type" class="form-control">
                        <option value="all" {{ ($serviceType ?? 'all') === 'all' ? 'selected' : '' }}>All Services</option>
                        <option value="studio_rental" {{ ($serviceType ?? '') === 'studio_rental' ? 'selected' : '' }}>Studio Rental</option>
                        <option value="solo_rehearsal" {{ ($serviceType ?? '') === 'solo_rehearsal' ? 'selected' : '' }}>Solo Rehearsal</option>
                        <option value="instrument_rental" {{ ($serviceType ?? '') === 'instrument_rental' ? 'selected' : '' }}>Instrument Rental</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Date Filter</label>
                    <select name="date_filter" class="form-control">
                        <option value="all" {{ $dateFilter === 'all' ? 'selected' : '' }}>All Dates</option>
                        <option value="today" {{ $dateFilter === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ $dateFilter === 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $dateFilter === 'month' ? 'selected' : '' }}>This Month</option>
                    </select>
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.bookings') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Studio Rental Records -->
    <div class="bookings-table-card">
        <div class="table-header">
            <h3 class="table-title">📋 Booking Records</h3>
            <div class="records-count">{{ $bookings->total() }} Records</div>
        </div>
        
        <div class="bookings-grid">
            @if($bookings->count() > 0)
                @foreach($bookings as $booking)
                    <div class="booking-card">
                        <div class="booking-card-header">
                            <div class="reference-id">
                                <strong>#{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</strong>
                            </div>
                            <span class="status-badge status-{{ $booking->status }}">
                                @if($booking->status === 'pending')
                                    ⏳ Pending
                                @elseif($booking->status === 'confirmed')
                                    ✅ Confirmed
                                @elseif($booking->status === 'rejected')
                                    ❌ Rejected
                                @else
                                    {{ ucfirst($booking->status) }}
                                @endif
                            </span>
                        </div>
                        
                        <div class="booking-card-body">
                            <div class="user-info">
                                <div class="user-avatar">
                                    {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                                </div>
                                <div class="user-details">
                                    <div class="user-name">{{ $booking->user->name }}</div>
                                    <div class="user-email">{{ $booking->user->email }}</div>
                                </div>
                            </div>
                            
                            <div class="booking-details">
                                <div class="detail-row">
                                    <span class="detail-label">Service:</span>
                                    <span class="detail-value">{{ $booking->service_type ?? 'Studio Rental' }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Duration:</span>
                                    <span class="detail-value">{{ $booking->duration }} hours</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Date:</span>
                                    <span class="detail-value">{{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Time:</span>
                                    <span class="detail-value">{{ $booking->time_slot }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Created:</span>
                                    <span class="detail-value">{{ $booking->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="booking-card-footer">
                            <div class="action-buttons">
                                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-view" title="View Details">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if($booking->status === 'pending')
                                    <form method="POST" action="{{ route('admin.booking.approve', $booking->id) }}" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to accept this booking?')">
                                            ✓ Accept
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.booking.reject', $booking->id) }}" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this booking?')">
                                            ✗ Reject
                                        </button>
                                    </form>
                                @elseif($booking->status === 'confirmed')
                                    <button class="btn btn-reschedule" onclick="rescheduleBooking({{ $booking->id }})" title="Rescheduling">
                                        <i class="fas fa-calendar-alt"></i> Reschedule
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">🎵</div>
                    <div class="empty-state-text">No bookings found</div>
                    <div class="empty-state-subtext">Try adjusting your filters or check back later</div>
                </div>
            @endif
        </div>
        
        @if($bookings->hasPages())
            <div class="pagination-wrapper">
                {{ $bookings->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Reschedule Modal -->
<div id="rescheduleModal" class="reschedule-modal">
    <div class="reschedule-modal-content">
        <div class="reschedule-modal-header">
            <h3><i class="fas fa-calendar-alt"></i> Rescheduling</h3>
            <span class="reschedule-modal-close" onclick="closeRescheduleModal()">&times;</span>
        </div>
        <form id="rescheduleForm" method="POST" action="">
            @csrf
            @method('PATCH')
            <div class="reschedule-modal-body">
                <div class="form-group">
                    <label for="reschedule_date">Date</label>
                    <input type="date" id="reschedule_date" name="date" class="form-control date-picker" required min="{{ date('Y-m-d') }}" placeholder="Select a date">
                    <small class="form-text">Click to select a new date for the booking</small>
                </div>
                <div class="form-group">
                    <label for="reschedule_time_slot">Time Slot</label>
                    <select id="reschedule_time_slot" name="time_slot" class="form-control" required>
                        <option value="">Select Time Slot</option>
                        <option value="09:00-10:00">09:00 - 10:00</option>
                        <option value="10:00-11:00">10:00 - 11:00</option>
                        <option value="11:00-12:00">11:00 - 12:00</option>
                        <option value="12:00-13:00">12:00 - 13:00</option>
                        <option value="13:00-14:00">13:00 - 14:00</option>
                        <option value="14:00-15:00">14:00 - 15:00</option>
                        <option value="15:00-16:00">15:00 - 16:00</option>
                        <option value="16:00-17:00">16:00 - 17:00</option>
                        <option value="17:00-18:00">17:00 - 18:00</option>
                        <option value="18:00-19:00">18:00 - 19:00</option>
                        <option value="19:00-20:00">19:00 - 20:00</option>
                        <option value="20:00-21:00">20:00 - 21:00</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="reschedule_duration">Duration (hours)</label>
                    <select id="reschedule_duration" name="duration" class="form-control" required>
                        <option value="">Select Duration</option>
                        <option value="1">1 hour</option>
                        <option value="2">2 hours</option>
                        <option value="3">3 hours</option>
                        <option value="4">4 hours</option>
                        <option value="5">5 hours</option>
                        <option value="6">6 hours</option>
                        <option value="7">7 hours</option>
                        <option value="8">8 hours</option>
                    </select>
                </div>
            </div>
            <div class="reschedule-modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeRescheduleModal()">Cancel</button>
                <button type="submit" class="btn btn-warning"><i class="fas fa-calendar-alt"></i> Submit Reschedule</button>
            </div>
        </form>
    </div>
</div>

<script>
    function rescheduleBooking(bookingId) {
        // Set the form action URL
        const form = document.getElementById('rescheduleForm');
        form.action = `/admin/bookings/${bookingId}/reschedule`;
        
        // Show the modal
        openRescheduleModal();
    }
    
    function openRescheduleModal() {
        const modal = document.getElementById('rescheduleModal');
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Focus on date picker for better UX
        setTimeout(() => {
            const datePicker = document.getElementById('reschedule_date');
            if (datePicker) {
                datePicker.focus();
            }
        }, 100);
    }
    
    function closeRescheduleModal() {
        const modal = document.getElementById('rescheduleModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Add smooth animations for status badges
    document.addEventListener('DOMContentLoaded', function() {
        const statusBadges = document.querySelectorAll('.status-badge');
        statusBadges.forEach(badge => {
            badge.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.05)';
            });
            badge.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });

        // Add confirmation for action buttons
        const acceptButtons = document.querySelectorAll('.btn-success');
        const rejectButtons = document.querySelectorAll('.btn-danger');

        acceptButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to accept this booking?')) {
                    e.preventDefault();
                }
            });
        });

        rejectButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to reject this booking?')) {
                    e.preventDefault();
                }
            });
        });
        
        // Add event listeners for reschedule modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRescheduleModal();
            }
        });
        
        document.getElementById('rescheduleModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRescheduleModal();
            }
        });
        
        // Enhanced date picker functionality
        const datePicker = document.getElementById('reschedule_date');
        if (datePicker) {
            // Auto-focus and open date picker when modal opens
            datePicker.addEventListener('click', function() {
                this.showPicker();
            });
            
            // Set default date to tomorrow for better UX
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const defaultDate = tomorrow.toISOString().split('T')[0];
            datePicker.setAttribute('value', defaultDate);
        }
    });
</script>
@endsection