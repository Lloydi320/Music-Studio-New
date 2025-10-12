<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Band Equipment Rental - Lemon Hub Studio</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
  <!-- Font Awesome for modern icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Clean Layout Styles */
    body.booking-page {
      min-height: calc(100vh + 84px);
      margin: 0;
      padding: 0;
      background: #f7f7f7;
    }
    
    .main-content {
      margin: 0 auto;
      padding: 20px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      margin-top: 84px;
      margin-bottom: 20px;
      max-width: calc(100vw - 40px);
      overflow-x: hidden;
      box-sizing: border-box;
    }

    .page-header {
      text-align: center;
      margin-bottom: 30px;
      padding: 30px;
      background: linear-gradient(135deg, #ffd700 0%, #ffed4e 50%, #dbb411 100%);
      color: #111;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3);
    }

    .page-header h1 {
      margin: 0 0 10px 0;
      font-size: 2.2em;
      font-weight: 700;
      color: #111;
    }

    .page-header p {
      margin: 0;
      font-size: 1.1em;
      opacity: 0.9;
      font-weight: 400;
    }

    /* Form Grid Layout */
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 20px;
      margin-bottom: 30px;
      max-width: 100%;
      overflow: hidden;
    }

    /* Responsive Design */
    @media (min-width: 1800px) {
      .form-grid {
        grid-template-columns: 1fr 1fr 1fr;
        gap: 25px;
      }
    }

    @media (max-width: 1600px) {
      .form-grid {
        grid-template-columns: 1fr 1fr 1fr;
        gap: 18px;
      }
    }

    @media (max-width: 1400px) {
      .form-grid {
        grid-template-columns: 1fr 1fr 1fr;
        gap: 16px;
      }
    }

    @media (max-width: 1300px) {
      .form-grid {
        grid-template-columns: 1fr 1fr 1fr;
        gap: 15px;
      }
    }

    @media (max-width: 1150px) {
      .form-grid {
        grid-template-columns: 1fr 1fr;
        gap: 15px;
      }
    }

    @media (max-width: 1200px) {
      .form-grid {
        grid-template-columns: 1fr 1fr 1fr;
        gap: 15px;
      }
    }

    @media (max-width: 1000px) {
      .form-grid {
        grid-template-columns: 1fr;
        gap: 15px;
      }
      
      .main-content {
        margin: 15px;
        padding: 20px;
      }
    }

    @media (max-width: 900px) {
      .form-grid {
        grid-template-columns: 1fr;
        gap: 20px;
      }
      
      .main-content {
        margin: 10px;
        padding: 15px;
      }
      
      .page-header {
        padding: 20px 15px;
      }
      
      .page-header h1 {
        font-size: 1.8em;
      }
    }

    @media (max-width: 600px) {
      .form-section {
        padding: 20px 15px;
      }
      
      .price-summary {
        padding: 20px 15px;
      }
      
      .main-content {
        margin: 5px;
        padding: 10px;
      }
    }



    .form-section {
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      border: 1px solid #e2e8f0;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      min-width: 0;
      overflow-wrap: break-word;
    }

    .form-section:hover {
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      transform: translateY(-2px);
    }

    .form-section h3 {
      margin: 0 0 20px 0;
      color: #2d3748;
      font-size: 1.3em;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 10px;
      padding-bottom: 10px;
      border-bottom: 2px solid #ffd700;
    }

    .price-summary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.25);
      min-width: 0;
      overflow-wrap: break-word;
    }

    .price-summary h3 {
      margin: 0 0 20px 0;
      color: white;
      font-size: 1.3em;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 10px;
      border-bottom: 2px solid rgba(255, 255, 255, 0.3);
      padding-bottom: 10px;
    }

    /* Submit Section */
    .submit-section {
      text-align: center;
      padding: 30px;
      background: #fff;
      border-radius: 12px;
      border: 1px solid #e2e8f0;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      margin-top: 20px;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
      .form-grid {
        grid-template-columns: 1fr;
        gap: 20px;
      }
      
      .price-summary {
        order: -1;
      }
      
      .compact-terms-section {
        margin-top: 12px;
      }
    }
    
    @media (max-width: 768px) {
      .main-content {
        padding: 15px;
        margin: 10px;
        margin-top: 80px;
      }
      
      .page-header {
        padding: 20px;
      }
      
      .page-header h1 {
        font-size: 1.8rem;
      }
      
      .form-section {
        padding: 20px;
      }
      
      .compact-terms-section {
        margin-top: 12px;
      }
      
      .compact-terms-content {
        gap: 8px;
      }
    }
    
    @media (max-width: 480px) {
      .main-content {
        padding: 10px;
        margin: 20px;
        margin-top: 75px;
      }
      
      .form-section {
        padding: 15px;
      }
      
      .page-header h1 {
        font-size: 1.5rem;
      }
      
      .compact-terms-section {
        margin-top: 10px;
      }
      
      .compact-terms-content {
        gap: 6px;
      }
      
      .compact-terms-container {
        padding: 10px;
      }
    }

    /* Date Input Styling */
    input[type="date"] {
      width: 100%;
      padding: 12px 15px;
      border: 2px solid #e2e8f0;
      border-radius: 8px;
      font-size: 16px;
      background: white;
      transition: all 0.3s ease;
      position: relative;
    }

    input[type="date"]:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Disabled/Unavailable Date Styling */
    .date-unavailable {
      background-color: #ffebee !important;
      color: #c62828 !important;
      border-color: #e57373 !important;
      cursor: not-allowed !important;
    }

    .date-unavailable:hover {
      background-color: #ffcdd2 !important;
      border-color: #ef5350 !important;
    }

    /* Date picker calendar styling for unavailable dates */
    input[type="date"]::-webkit-calendar-picker-indicator {
      cursor: pointer;
      filter: invert(0.5);
    }

    .date-unavailable::-webkit-calendar-picker-indicator {
      filter: invert(0.8) sepia(1) saturate(5) hue-rotate(315deg);
      cursor: not-allowed;
    }

    /* Alert styling for better user feedback */
    .date-conflict-alert {
      background-color: #fff3cd;
      border: 1px solid #ffeaa7;
      color: #856404;
      padding: 12px 16px;
      border-radius: 8px;
      margin: 10px 0;
      display: none;
      animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Form group styling */
     .form-group {
       margin-bottom: 25px;
       position: relative;
     }

    .form-group label {
      display: block;
      margin-bottom: 10px;
      font-weight: 500;
      color: #4a5568;
      font-size: 0.95em;
      letter-spacing: 0.3px;
    }

    .form-group select,
    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 16px 18px;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      font-size: 16px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      background: #ffffff;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }

    .form-group select:focus,
    .form-group input:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15), 0 4px 12px rgba(102, 126, 234, 0.1);
      transform: translateY(-1px);
    }

    .form-group select:hover,
    .form-group input:hover,
    .form-group textarea:hover {
      border-color: #cbd5e0;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }

    .form-group small {
      display: block;
      margin-top: 8px;
      color: #718096;
      font-size: 0.85em;
      line-height: 1.4;
    }



         .price-details {
       display: grid;
       grid-template-columns: 1fr 1fr;
       gap: 15px;
       margin-bottom: 20px;
     }

    .price-item {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

         .package-option {
       margin: 15px 0;
       padding: 15px;
       background: rgba(255, 255, 255, 0.3);
       border-radius: 8px;
       border: 2px solid rgba(255, 255, 255, 0.5);
       box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
     }
     


     .package-option label {
       display: flex;
       align-items: center;
       gap: 10px;
       cursor: pointer;
       color: #111;
       font-weight: 500;
     }

     .package-option input[type="checkbox"] {
       transform: scale(1.2);
     }

     .total-price {
       font-size: 1.5em;
       font-weight: bold;
       text-align: center;
       padding: 15px;
       background: rgba(255, 255, 255, 0.3);
       border-radius: 8px;
       margin-top: 15px;
       color: #111;
       box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
     }
     

     




         .submit-btn {
       background: linear-gradient(135deg, #ffd700 0%, #ffed4e 50%, #dbb411 100%);
       color: #111;
       border: none;
       padding: 12px 30px;
       font-size: 16px;
       font-weight: 600;
       border-radius: 25px;
       cursor: pointer;
       transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
       box-shadow: 0 6px 20px rgba(255, 215, 0, 0.3);
       letter-spacing: 0.5px;
       position: relative;
       overflow: hidden;
       width: auto;
       max-width: 280px;
       margin: 0 auto;
       justify-content: center;
       display: flex;
       align-items: center;
       gap: 8px;
     }

     .submit-section-inline {
       margin-top: 25px;
       padding-top: 20px;
       border-top: 2px solid rgba(255, 255, 255, 0.2);
     }

     .submit-btn::before {
       content: '';
       position: absolute;
       top: 0;
       left: -100%;
       width: 100%;
       height: 100%;
       background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
       transition: left 0.5s;
     }

     .submit-btn:hover::before {
       left: 100%;
     }

         .submit-btn:hover {
       transform: translateY(-2px);
       box-shadow: 0 10px 30px rgba(255, 215, 0, 0.4);
       background: linear-gradient(135deg, #dbb411 0%, #ffd700 50%, #ffed4e 100%);
     }

     .rental-terms {
       background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
       border: 2px solid #e2e8f0;
       border-radius: 20px;
       padding: 30px;
       margin: 30px 0;
       box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
       position: relative;
     }

     .rental-terms::before {
       content: '';
       position: absolute;
       top: 0;
       left: 0;
       right: 0;
       height: 4px;
       background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
       border-radius: 20px 20px 0 0;
     }

     .rental-terms h3 {
       color: #2d3748;
       margin: 0 0 20px 0;
       font-size: 1.4em;
       font-weight: 600;
     }

     .terms-list {
       display: flex;
       flex-direction: column;
       gap: 10px;
     }

     .term-item {
       background: white;
       padding: 12px;
       border-radius: 6px;
       border-left: 4px solid #ffd700;
       font-size: 0.9em;
       line-height: 1.4;
       box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
     }

     .term-item strong {
       color: #dbb411;
     }
     
     .rental-terms-compact {
       margin-top: 20px;
       padding: 15px;
       background: linear-gradient(135deg, #fff8dc 0%, #fffacd 100%);
       border-radius: 8px;
       border: 2px solid #ffd700;
       box-shadow: 0 2px 10px rgba(255, 215, 0, 0.2);
     }
     
     .rental-terms-compact h4 {
       color: #111;
       margin: 0 0 10px 0;
       font-size: 1.1em;
     }
     
     .terms-compact {
       display: flex;
       flex-direction: column;
       gap: 8px;
     }
     
     .term-compact {
       font-size: 0.85em;
       color: #555;
       line-height: 1.3;
     }
     
     .form-section .price-details {
       margin-bottom: 20px;
     }
     
     .form-section .price-item {
       display: flex;
       justify-content: space-between;
       padding: 8px 0;
       border-bottom: 1px solid rgba(255, 255, 255, 0.3);
       font-size: 0.9em;
     }
     
     .form-section .price-item:last-child {
       border-bottom: none;
       font-weight: bold;
       font-size: 1em;
     }
     
     .form-section .package-option {
       margin: 15px 0;
       padding: 12px;
       background: rgba(255, 255, 255, 0.4);
       border-radius: 6px;
       border: 1px solid rgba(255, 255, 255, 0.6);
     }
     
     .form-section .total-price {
       font-size: 1.3em;
       padding: 12px;
       margin-top: 15px;
       background: rgba(255, 255, 255, 0.5);
       border: 2px solid rgba(255, 215, 0, 0.5);
     }
     
     .payment-note {
       margin-top: 15px;
       padding: 12px;
       background: rgba(255, 255, 255, 0.4);
       border-radius: 6px;
       border: 1px solid rgba(255, 215, 0, 0.3);
       font-size: 0.9em;
     }
     
     .payment-note ul {
       margin: 8px 0 0 0;
       padding-left: 20px;
     }
     
            .payment-note li {
       margin-bottom: 4px;
       color: #555;
     }
     
     /* Compact Terms Section - Below Button */
      .compact-terms-section {
        margin-top: 15px;
        max-width: 100%;
      }
      
      .compact-terms-container {
        background: rgba(248, 249, 250, 0.8);
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
      }
      
      .compact-terms-content {
        display: flex;
        flex-direction: column;
        gap: 10px;
      }
      
      .compact-terms-content .payment-terms, 
      .compact-terms-content .key-terms {
        padding: 8px;
        border-radius: 6px;
        font-size: 0.85em;
      }
      
      .compact-terms-content .payment-terms {
        background: rgba(255, 255, 255, 0.4);
        border: 1px solid rgba(255, 215, 0, 0.2);
      }
      
      .compact-terms-content .key-terms {
        background: rgba(255, 248, 220, 0.6);
        border: 1px solid rgba(255, 215, 0, 0.4);
      }
      
      .compact-terms-content .payment-terms ul {
        margin: 6px 0 0 0;
        padding-left: 16px;
      }
      
      .compact-terms-content .payment-terms li {
        margin-bottom: 3px;
        color: #666;
        font-size: 0.8em;
      }
      
      .compact-terms-content .terms-list {
        margin-top: 6px;
        display: flex;
        flex-direction: column;
        gap: 4px;
      }
      
      .compact-terms-content .term-item {
        font-size: 0.75em;
        color: #666;
        line-height: 1.2;
      }
      
      .compact-terms-content strong {
        font-size: 0.9em;
        color: #495057;
      }
     
     /* Modal Styles */
       .modal {
         display: none;
         position: fixed;
         z-index: 1000;
         left: 0;
         top: 0;
         width: 100%;
         height: 100%;
         background-color: rgba(0, 0, 0, 0.8);
         backdrop-filter: blur(5px);
       }

       .modal-container {
         display: flex;
         justify-content: center;
         align-items: center;
         height: 100%;
         padding: 20px;
       }

       .modal-content {
         background: white;
         border-radius: 20px;
         width: 95%;
         max-width: 1200px;
         max-height: 85vh;
         overflow-y: auto;
         overflow-x: hidden;
         display: flex;
         box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
         scroll-behavior: smooth;
       }

       .modal-left {
         flex: 1;
         padding: 25px;
         background: #f8f9fa;
         border-radius: 20px 0 0 20px;
         overflow-y: auto;
         max-height: 85vh;
       }

       .modal-center {
         flex: 1.2;
         padding: 25px;
         background: white;
         border-left: 1px solid #e9ecef;
         border-right: 1px solid #e9ecef;
         overflow-y: auto;
         max-height: 85vh;
       }

       .modal-right {
         flex: 1;
         padding: 25px;
         background: white;
         border-radius: 0 20px 20px 0;
         display: flex;
         align-items: flex-start;
         justify-content: center;
         overflow-y: auto;
         max-height: 85vh;
         padding-top: 15px;
       }
       
       .modal-header {
         margin-bottom: 20px;
       }

       .modal-title {
         font-size: 24px;
         font-weight: bold;
         color: #333;
         margin-bottom: 5px;
       }

       .modal-subtitle {
         color: #666;
         font-size: 14px;
         margin-bottom: 15px;
       }

       .location {
         color: #666;
         font-size: 14px;
         margin-bottom: 15px;
       }

       .booking-details {
         background: white;
         padding: 20px;
         border-radius: 10px;
         margin-bottom: 20px;
         box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
       }

       .detail-item {
         display: flex;
         justify-content: space-between;
         margin-bottom: 10px;
         padding: 8px 0;
         border-bottom: 1px solid #eee;
       }

       .detail-label {
         font-weight: 500;
         color: #555;
       }

       .detail-value {
         color: #333;
         font-weight: 600;
       }

       /* List-Style Booking Summary */
       .booking-summary-list {
         background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
         border-radius: 12px;
         padding: 20px;
         margin-bottom: 20px;
         border: 1px solid #dee2e6;
         box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
       }
       
       .summary-header {
         text-align: center;
         margin-bottom: 20px;
         padding-bottom: 12px;
         border-bottom: 2px solid #007bff;
       }
       
       .summary-header h3 {
         margin: 0;
         font-size: 1.3rem;
         font-weight: 600;
         color: #2c3e50;
       }
       
       .summary-header i {
         margin-right: 8px;
         color: #007bff;
       }
       
       .summary-list {
         margin-bottom: 20px;
       }
       
       .summary-list-item {
         display: flex;
         align-items: center;
         padding: 12px 0;
         border-bottom: 1px solid #e9ecef;
         transition: background-color 0.2s ease;
       }
       
       .summary-list-item:last-child {
         border-bottom: none;
       }
       
       .summary-list-item:hover {
         background-color: rgba(0, 123, 255, 0.05);
         border-radius: 6px;
       }
       
       .item-icon {
         width: 32px;
         height: 32px;
         background: linear-gradient(135deg, #007bff, #0056b3);
         border-radius: 50%;
         display: flex;
         align-items: center;
         justify-content: center;
         margin-right: 12px;
         flex-shrink: 0;
       }
       
       .item-icon i {
         font-size: 0.9rem;
         color: white;
       }
       
       .item-content {
         flex: 1;
         display: flex;
         justify-content: space-between;
         align-items: center;
       }
       
       .item-label {
         font-size: 0.9rem;
         color: #6c757d;
         font-weight: 500;
       }
       
       .item-value {
         font-weight: 600;
         color: #2c3e50;
         font-size: 0.9rem;
       }
       
       .price-breakdown-list {
         background: #f8f9fa;
         border-radius: 8px;
         padding: 16px;
         margin-bottom: 16px;
         border: 1px solid #e9ecef;
       }
       
       .price-list-item {
         display: flex;
         justify-content: space-between;
         align-items: center;
         padding: 8px 0;
         font-size: 0.85rem;
         border-bottom: 1px solid #e9ecef;
       }
       
       .price-list-item:last-child {
         border-bottom: none;
       }
       
       .price-label {
         color: #6c757d;
         font-weight: 500;
       }
       
       .price-label i {
         margin-right: 6px;
         width: 14px;
         color: #007bff;
       }
       
       .price-value {
         font-weight: 600;
         color: #2c3e50;
       }
       
       .price-list-total {
         display: flex;
         justify-content: space-between;
         align-items: center;
         padding: 12px 0 8px 0;
         margin-top: 8px;
         border-top: 2px solid #007bff;
         font-weight: 700;
         font-size: 1rem;
       }
       
       .total-label {
         color: #2c3e50;
         font-weight: 700;
       }
       
       .total-label i {
         margin-right: 6px;
         color: #007bff;
       }
       
       .total-value {
         color: #007bff;
         font-weight: 700;
         font-size: 1.1rem;
       }
       
       .reservation-fee {
         background: rgba(255, 193, 7, 0.1);
         border-radius: 4px;
         padding: 4px 0;
       }
       
       .reservation-fee .price-label {
         color: #856404;
       }
       
       .reservation-fee .price-value {
         color: #856404;
         font-weight: 700;
       }
       
       .payment-note-list {
         background: rgba(23, 162, 184, 0.1);
         border: 1px solid rgba(23, 162, 184, 0.3);
         border-radius: 6px;
         padding: 10px;
         display: flex;
         align-items: center;
         font-size: 0.8rem;
         color: #0c5460;
       }
       
       .payment-note-list i {
         margin-right: 8px;
         color: #17a2b8;
       }
       
       /* Responsive Design for List Summary */
       @media (max-width: 768px) {
         .booking-summary-list {
           padding: 16px;
         }
         
         .summary-list-item {
           padding: 10px 0;
         }
         
         .item-content {
           flex-direction: column;
           align-items: flex-start;
           gap: 4px;
         }
         
         .summary-header h3 {
           font-size: 1.1rem;
         }
       }

       .studio-image-modal {
         width: 100%;
         height: 200px;
         object-fit: cover;
         border-radius: 10px;
         margin-bottom: 20px;
       }

       .form-group {
         margin-bottom: 20px;
       }

       .form-group label {
         display: block;
         margin-bottom: 8px;
         font-weight: 600;
         color: #333;
         font-size: 14px;
       }

       .form-group input,
       .form-group select {
         width: 100%;
         padding: 12px 15px;
         border: 2px solid #e9ecef;
         border-radius: 8px;
         font-size: 14px;
         transition: all 0.3s ease;
         background: white;
       }

       .form-group input:focus,
       .form-group select:focus {
         outline: none;
         border-color: #007bff;
         box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
       }

       .form-group input[type="file"] {
         padding: 8px;
         border: 2px dashed #e9ecef;
         background: #f8f9fa;
       }

       .required {
         color: #dc3545;
       }

       .policy-section {
         margin: 20px 0;
         padding: 15px;
         background: #f8f9fa;
         border-radius: 8px;
         border-left: 4px solid #ffc107;
       }

       .policy-section h4 {
         color: #333;
         margin-bottom: 10px;
         font-size: 16px;
       }

       .policy-section p {
         color: #666;
         font-size: 13px;
         line-height: 1.5;
         margin-bottom: 8px;
       }

       .checkbox-group {
         margin: 20px 0;
       }

       .checkbox-group input[type="checkbox"] {
         margin-right: 8px;
       }

       .checkbox-group label {
         font-size: 14px;
         color: #333;
       }

       .checkbox-group a {
         color: #007bff;
         text-decoration: none;
       }

       .checkbox-group a:hover {
         text-decoration: underline;
       }
       
       .summary-section {
         margin-bottom: 20px;
         background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
         padding: 20px;
         border-radius: 12px;
         border: 1px solid #e2e8f0;
         box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
       }
       
       .summary-section h3 {
         color: #2d3748;
         margin: 0 0 15px 0;
         font-size: 1.2em;
         font-weight: 600;
         border-bottom: 3px solid #ffd700;
         padding-bottom: 8px;
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
         -webkit-background-clip: text;
         -webkit-text-fill-color: transparent;
         background-clip: text;
       }
       
       .summary-item {
         display: flex;
         justify-content: space-between;
         padding: 8px 0;
         border-bottom: 1px solid #e2e8f0;
         font-size: 0.95em;
         transition: all 0.2s ease;
       }
       
       .summary-item:hover {
         background: rgba(102, 126, 234, 0.05);
         padding-left: 8px;
         border-radius: 4px;
       }
       
       .summary-item.total {
         font-weight: bold;
         font-size: 1.15em;
         color: #dbb411;
         border-bottom: 3px solid #ffd700;
         background: linear-gradient(135deg, #fff8dc 0%, #fffacd 100%);
         padding: 12px 8px;
         border-radius: 8px;
         margin-top: 10px;
         box-shadow: 0 4px 12px rgba(255, 215, 0, 0.2);
       }
       
       .payment-reminder {
         background: linear-gradient(135deg, #fff8dc 0%, #fffacd 100%);
         border: 2px solid #ffd700;
         border-radius: 12px;
         padding: 20px;
         margin-top: 20px;
         box-shadow: 0 4px 15px rgba(255, 215, 0, 0.2);
         position: relative;
         overflow: hidden;
       }
       
       .payment-reminder::before {
         content: '';
         position: absolute;
         top: 0;
         left: 0;
         right: 0;
         height: 4px;
         background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
       }
       
       .payment-reminder strong {
         color: #2d3748;
         font-size: 1.1em;
       }
       
       .payment-reminder p {
         margin: 10px 0 0 0;
         color: #4a5568;
         font-size: 0.9em;
         line-height: 1.5;
       }
       
       .modal-footer {
         background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
         padding: 25px 30px;
         border-radius: 0 0 20px 20px;
         text-align: center;
         border-top: 1px solid #e2e8f0;
         display: flex;
         gap: 20px;
         justify-content: center;
       }
       
       .btn-cancel, .btn-confirm {
         padding: 12px 24px;
         border: none;
         border-radius: 12px;
         font-size: 1em;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
         position: relative;
         overflow: hidden;
         min-width: 120px;
         text-transform: uppercase;
         letter-spacing: 0.5px;
       }
       
       .btn-cancel::before, .btn-confirm::before {
         content: '';
         position: absolute;
         top: 0;
         left: -100%;
         width: 100%;
         height: 100%;
         background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
         transition: left 0.5s;
       }
       
       .btn-cancel:hover::before, .btn-confirm:hover::before {
         left: 100%;
       }
       
       .btn-cancel {
         background: linear-gradient(135deg, #718096 0%, #4a5568 100%);
         color: white;
         box-shadow: 0 4px 15px rgba(113, 128, 150, 0.4);
       }
       
       .btn-cancel:hover {
         transform: translateY(-2px);
         box-shadow: 0 8px 25px rgba(113, 128, 150, 0.6);
       }
       
       .btn-confirm {
         background: linear-gradient(135deg, #ffd700 0%, #dbb411 100%);
         color: #111;
         box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4);
       }
       
       .btn-confirm:hover {
         transform: translateY(-2px);
         box-shadow: 0 8px 25px rgba(255, 215, 0, 0.6);
       }

       .gcash-container {
         background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
         border-radius: 15px;
         padding: 20px;
         text-align: center;
         color: white;
         box-shadow: 0 10px 30px rgba(30, 64, 175, 0.3);
         position: relative;
         overflow: hidden;
       }

       .gcash-container::before {
         content: '';
         position: absolute;
         top: -50%;
         left: -50%;
         width: 200%;
         height: 200%;
         background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
         animation: shimmer 3s infinite;
       }

       @keyframes shimmer {
         0% { transform: rotate(0deg); }
         100% { transform: rotate(360deg); }
       }

       .gcash-logo {
         font-size: 24px;
         font-weight: bold;
         margin-bottom: 15px;
         position: relative;
         z-index: 2;
       }

       .qr-code {
         background: white;
         padding: 15px;
         border-radius: 10px;
         margin: 15px 0;
         position: relative;
         z-index: 2;
       }

       .qr-code img {
         width: 150px;
         height: 150px;
       }

       .payment-info {
         position: relative;
         z-index: 2;
       }

       .account-name {
         font-size: 14px;
         margin: 10px 0 5px;
         opacity: 0.9;
       }

       .account-number {
         font-size: 12px;
         opacity: 0.8;
         margin-bottom: 15px;
       }

       .scan-text {
         font-size: 12px;
         opacity: 0.9;
         margin-bottom: 10px;
       }

       .amount-display {
         font-size: 28px;
         font-weight: bold;
         margin: 15px 0;
         text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
         position: relative;
         z-index: 2;
       }

       .modal-actions {
         display: flex;
         gap: 15px;
         margin-top: 25px;
       }

       .btn-cancel-modal,
       .btn-confirm-booking {
         flex: 1;
         padding: 12px 20px;
         border: none;
         border-radius: 8px;
         font-size: 16px;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s ease;
       }

       .btn-cancel-modal {
         background: #6c757d;
         color: white;
       }

       .btn-cancel-modal:hover {
         background: #5a6268;
         transform: translateY(-1px);
       }

       .btn-confirm-booking {
         background: #28a745;
         color: white;
       }

       .btn-confirm-booking:hover {
         background: #218838;
         transform: translateY(-1px);
       }
       
       /* Responsive modal for smaller screens */
       @media (max-height: 700px) {
         .modal-content {
           margin: 0.5% auto;
           max-height: 98vh;
         }
         
         .modal-header {
           padding: 10px 15px;
         }
         
         .modal-header h2 {
           font-size: 1.2em;
         }
         
         .modal-body {
           padding: 8px 12px;
         }
         
         .summary-section {
           margin-bottom: 10px;
         }
         
         .summary-section h3 {
           font-size: 0.95em;
           margin: 0 0 6px 0;
         }
         
         .summary-item {
           padding: 3px 0;
           font-size: 0.85em;
         }
         
         .payment-reminder {
           padding: 6px;
           margin-top: 8px;
         }
         
         .payment-reminder p {
           font-size: 0.75em;
         }
         
         .modal-footer {
           padding: 8px 12px;
         }
         
         .btn-cancel, .btn-confirm {
           padding: 6px 14px;
           font-size: 12px;
         }
       }
     
     /* Mobile-First Responsive Design */
     @media (max-width: 480px) {
       .rental-container {
         margin: 5px;
         padding: 15px;
         border-radius: 15px;
       }
       
       .rental-header {
         padding: 20px 15px;
         text-align: center;
       }
       
       .rental-header h1 {
         font-size: 1.8em;
         margin-bottom: 8px;
       }
       
       .rental-header p {
         font-size: 0.9em;
       }
       
       .rental-form {
         grid-template-columns: 1fr;
         gap: 15px;
       }
       
       .form-section {
         padding: 20px 15px;
       }
       
       .form-section h3 {
         font-size: 1.1em;
       }
       
       .price-summary {
         padding: 20px 15px;
       }
       
       .submit-section {
         padding: 20px 15px;
       }
       
       .submit-btn {
         width: 100%;
         padding: 15px;
         font-size: 1.1em;
       }
       
       .modal-content {
         width: 95%;
         margin: 5% auto;
         border-radius: 15px;
       }
       
       .modal-header {
         padding: 20px 15px;
         border-radius: 15px 15px 0 0;
       }
       
       .modal-body {
         padding: 20px 15px;
       }
       
       .summary-section {
         padding: 15px;
         margin-bottom: 15px;
       }
       
       .modal-footer {
         flex-direction: column;
         gap: 15px;
         padding: 20px 15px;
       }
       
       .btn-cancel, .btn-confirm {
         width: 100%;
         padding: 15px;
       }
     }
     
     @media (min-width: 481px) and (max-width: 768px) {
       .rental-container {
         margin: 15px;
         padding: 25px;
       }
       
       .rental-form {
         grid-template-columns: 1fr;
         gap: 20px;
       }
       
       .form-section {
         padding: 25px 20px;
       }
       
       .price-summary {
         padding: 25px 20px;
       }
       
       .submit-section {
         padding: 25px 20px;
       }
       
       .modal-content {
         width: 90%;
         margin: 3% auto;
       }
       
       .modal-body {
         padding: 25px 20px;
       }
       
       .modal-footer {
         flex-direction: row;
         gap: 15px;
         justify-content: center;
       }
     }
     
     @media (min-width: 769px) and (max-width: 1024px) {
       .rental-container {
         max-width: 900px;
       }
       
       .rental-form {
         grid-template-columns: 2fr 1fr;
         gap: 25px;
       }
     }
     
     @media (max-width: 1200px) {
       .rental-form {
         grid-template-columns: 1fr 1fr;
         gap: 20px;
       }
       
       .rental-form .form-section:last-child {
         grid-column: 1 / -1;
         max-width: 600px;
         margin: 0 auto;
       }
     }
     
     @media (max-width: 768px) {
       .rental-form {
         grid-template-columns: 1fr;
         gap: 20px;
       }
       
       .rental-container {
         padding: 15px;
       }
       
       .rental-header h1 {
         font-size: 2em;
       }
     }
     

     
     /* Custom input styles to match the yellow theme */
     input[type="text"], input[type="email"], input[type="number"], input[type="date"], select, textarea {
       width: 100%;
       padding: 12px;
       border: 2px solid #e9ecef;
       border-radius: 6px;
       font-size: 16px;
       transition: all 0.3s ease;
       background: white;
     }
     
     input[type="text"]:focus, input[type="email"]:focus, input[type="number"]:focus, input[type="date"]:focus, select:focus, textarea:focus {
       outline: none;
       border-color: #ffd700;
       box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
     }
     


    .alert {
      padding: 15px;
      margin: 20px 0;
      border-radius: 6px;
      font-weight: 500;
    }

    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .alert-error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .instrument-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 15px;
      margin-top: 15px;
    }

    .instrument-card {
      background: white;
      border: 2px solid #e9ecef;
      border-radius: 8px;
      padding: 15px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .instrument-card:hover {
      border-color: #ffd700;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
    }

    .instrument-card.selected {
      border-color: #ffd700;
      background: linear-gradient(135deg, #ffd700 0%, #dbb411 100%);
      color: #111;
    }

    .instrument-name {
      font-weight: bold;
      margin-bottom: 5px;
    }

    .instrument-price {
      font-size: 0.9em;
      opacity: 0.8;
    }

         @media (max-width: 768px) {
       .form-grid {
         display: flex;
         flex-direction: column;
       }
       
       .form-section:nth-child(1) {
         order: 1;
       }
       
       .form-section:nth-child(2) {
         order: 2;
       }
       
       .price-summary {
         order: 3;
       }
       
       .rental-form {
         grid-template-columns: 1fr;
       }
       
       .price-details {
         grid-template-columns: 1fr;
       }
       
       .instrument-grid {
         grid-template-columns: 1fr;
         gap: 10px;
       }
       
       .instrument-card {
         padding: 12px;
       }
     }

     /* Ensure proper spacing */
     .booking-main {
       min-height: calc(100vh - 200px);
       padding-bottom: 50px;
     }

     .rental-container {
       margin-bottom: 30px;
     }

     .booking-footer {
       margin-top: 50px;
       padding-top: 30px;
       position: relative !important;
       bottom: auto !important;
     }
     
     /* Override the fixed footer and hidden overflow */
     body.booking-page, html {
       overflow: auto !important;
     }
     
     .booking-footer {
       position: relative !important;
       bottom: auto !important;
     }

     /* Error field styling for inline validation */
     .error-field {
       border: 2px solid #dc2626 !important;
       box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
       background-color: #fef2f2 !important;
     }
     
     /* Success field styling for inline validation */
     .success-field {
       border: 2px solid #10b981 !important;
       box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
       background-color: #f0fdf4 !important;
     }
     
     /* Success message styling */
     .success-message {
       background-color: #d1fae5 !important;
       color: #065f46 !important;
       border: 1px solid #a7f3d0 !important;
     }

     /* Hide Sign Out button in navigation on desktop view */
     @media (min-width: 769px) {
       .nav-signout-desktop-hidden {
         display: none !important;
       }
     }
  </style>
</head>
<body class="booking-page">

  <header class="navbar">
    <div class="logo">
      <a href="/" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
        <img src="{{ asset('images/studio-logo.png') }}" alt="Lemon Hub Studio Logo">
        <span>LEMON HUB STUDIO</span>
      </a>
    </div>
    
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle mobile menu">
      <span></span>
      <span></span>
      <span></span>
    </button>
    
    <nav class="nav-container">
      <ul class="nav-links">
        <li><a href="/">Home</a></li>
        <li><a href="/services">About Us & Our Services</a></li>
        <li><a href="#" id="contactLink">Contact</a></li>
        <li><a href="#" id="feedbackLink">Feedbacks</a></li>
        <li><a href="/map">Map</a></li>
        @if(Auth::check())
        <li><a href="#" id="rescheduleBookingLink">Rescheduling</a></li>
        @endif
        @if(Auth::check() && Auth::user()->isAdmin())
        <li><a href="/admin/calendar" style="color: #ff6b35; font-weight: bold;">📅 Admin Calendar</a></li>
        @endif
        @if(!Auth::check())
        <li class="nav-login-mobile">
          <a href="{{ route('login') }}" style="color: #FFD700; padding: 15px 20px; font-size: 1.1rem; text-decoration: none; width: 100%; text-align: left; border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: block;">
            Login
          </a>
        </li>
        @endif
        @if(Auth::check())
        <li class="nav-signout-desktop-hidden">
          <form action="/logout" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" style="background: none; border: none; color: #fff; padding: 15px 20px; font-size: 1.1rem; cursor: pointer; width: 100%; text-align: left; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
               Sign Out
            </button>
          </form>
        </li>
        @endif
      </ul>
    </nav>
    @if(Auth::check())
        <div class="modern-user-profile" id="userProfile">
            <div class="profile-trigger" onclick="toggleUserDropdown()">
                <div class="profile-avatar">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="User Avatar">
                    @else
                        <div class="avatar-placeholder">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    @if(Auth::user()->is_admin)
                        <div class="admin-indicator"></div>
                    @endif
                </div>
                <div class="profile-info">
                    <div class="profile-name">{{ Auth::user()->name }}</div>
                    @if(Auth::user()->is_admin)
                        <div class="profile-role">Admin</div>
                    @else
                        <div class="profile-role">Member</div>
                    @endif
                </div>
                <svg class="dropdown-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M7 10l5 5 5-5z"/>
                </svg>
            </div>
            
            <div class="user-dropdown" id="userDropdown">
                <div class="dropdown-header">
                    <div class="dropdown-avatar">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="User Avatar">
                        @else
                            <div class="avatar-placeholder large">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="dropdown-user-info">
                        <h4>{{ Auth::user()->name }}</h4>
                        <p>{{ Auth::user()->email }}</p>
                        @if(Auth::user()->is_admin)
                            <span class="user-badge">Admin</span>
                        @endif
                    </div>
                </div>
                
                <div class="dropdown-menu">
                    
                    <form action="{{ route('logout') }}" method="POST" class="dropdown-form">
                        @csrf
                        <button type="submit" class="dropdown-item logout-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.59L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
  </header>

  <main class="main-content">
    <header class="page-header">
      <h1><i class="fas fa-music"></i> Band Equipment Rental</h1>
      <p>Rent professional-grade band equipment for events, gigs, and performances</p>
    </header>

    @if(session('error'))
      <div class="alert alert-error">
        {{ session('error') }}
      </div>
    @endif

    <form id="rentalForm" action="{{ route('instrument-rental.store') }}" method="POST">
      @csrf
      <div class="form-grid">
          <section class="form-section">
            <h3><i class="fas fa-music"></i> Instrument Selection</h3>
            
            <div class="form-group">
              <label for="instrument_type">Instrument Type:</label>
              <select id="instrument_type" name="instrument_type" required>
                <option value="">Select Instrument Type</option>
                @foreach($instrumentTypes as $key => $type)
                  <option value="{{ $key }}" data-rate="{{ $dailyRates[$key] ?? 10 }}">{{ $type }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label for="instrument_name">Specific Instrument:</label>
              <select id="instrument_name" name="instrument_name" required disabled>
                <option value="">Select Instrument Type First</option>
              </select>
            </div>

            <div class="form-group">
              <label for="daily_rate">Daily Rate:</label>
              <input type="text" id="daily_rate" value="₱0.00" readonly>
            </div>

            <div class="form-group">
              <label for="venue_type">Venue Type:</label>
              <select name="venue_type" required>
                <option value="indoor">Indoor Venue (Required for safety)</option>
                <option value="outdoor" disabled>Outdoor Venue (Not allowed)</option>
              </select>
            </div>

            <div class="form-group">
              <label for="event_duration_hours">Event Duration (Hours):</label>
              <input type="number" name="event_duration_hours" min="1" max="12" value="7" required>
              <small>Maximum 7 hours included. ₱200 per exceeding hour.</small>
            </div>

            <div class="form-group">
              <label>
                <input type="checkbox" name="documentation_consent" value="1" checked>
                I consent to photos/videos being taken for studio documentation
              </label>
            </div>
          </section>

          <section class="form-section">
            <h3><i class="fas fa-calendar-alt"></i> Rental Period</h3>
            
            <div class="form-group">
              <label for="rental_start_date">Start Date:</label>
              <input type="date" id="rental_start_date" name="rental_start_date" required min="{{ date('Y-m-d') }}">
            </div>

            <div class="form-group">
              <label for="rental_end_date">End Date:</label>
              <input type="date" id="rental_end_date" name="rental_end_date" required>
            </div>

            <div class="form-group">
              <label for="rental_duration">Duration:</label>
              <input type="text" id="rental_duration" value="0 days" readonly>
            </div>

            <div class="form-group">
              <label for="pickup_location">Pickup Location:</label>
              <select name="pickup_location" required>
                <option value="Studio">Studio (288H Sto.Domingo Street, Calamba)</option>
                <option value="Delivery">Delivery (Additional fee may apply)</option>
              </select>
            </div>

            <div class="form-group">
              <label for="return_location">Return Location:</label>
              <select name="return_location" required>
                <option value="Studio">Studio (288H Sto.Domingo Street, Calamba)</option>
                <option value="Pickup">Pickup Service (Additional fee may apply)</option>
              </select>
            </div>

            <div class="form-group">
              <label for="transportation">Transportation Service:</label>
              <select name="transportation" id="transportation_select">
                <option value="none">No Transportation (Self Pickup)</option>
                <option value="delivery">Delivery & Pickup (₱500-₱600 within Laguna)</option>
              </select>
            </div>

            <div class="form-group">
              <label for="special_requests">Special Requirements or Notes:</label>
              <textarea name="special_requests" id="special_requests" rows="3" placeholder="Any special requirements, setup needs, or additional information..."></textarea>
            </div>
          </section>

          <section class="price-summary">
            <h3><i class="fas fa-calculator"></i> Price Summary</h3>
              
              <div class="price-details">
              <div class="price-item">
                <span>Daily Rate:</span>
                <span id="summary_daily_rate">₱0.00</span>
              </div>
              <div class="price-item">
                <span>Duration:</span>
                <span id="summary_duration">0 days</span>
              </div>
              <div class="price-item">
                <span>Subtotal:</span>
                <span id="summary_subtotal">₱0.00</span>
              </div>
              <div class="price-item">
                <span>Transportation Fee:</span>
                <span id="transportation_fee">₱0.00</span>
              </div>
              <div class="price-item">
                <span>Reservation Fee & Security Deposit:</span>
                <span>₱300.00</span>
              </div>
            </div>

            <div class="package-option">
              <label>
                <input type="checkbox" id="full_package" name="full_package">
                <strong>Full Package Rental (₱4,500/day)</strong> - Includes all equipment
              </label>
            </div>

            <div class="total-price">
              Total: <span id="summary_total">₱0.00</span>
            </div>
            
            <div class="submit-section-inline">
              <button type="button" class="submit-btn" id="showSummaryBtn">
                <i class="fas fa-check-circle"></i> Confirm Instrument Rental
              </button>
            </div>
            
            <!-- Compact Terms Section - Below Button -->
            <div class="compact-terms-section">
              <div class="compact-terms-container">
                <div class="compact-terms-content">
                  <div class="payment-terms">
                    <strong>💳 Payment Terms:</strong>
                    <ul>
                      <li>Security deposit (₱300 for individual items, ₱500 for full package) must be paid first to confirm booking</li>
                      <li>Full payment will be collected upon pickup/delivery</li>
                    </ul>
                  </div>
                  
                  <div class="key-terms">
                    <strong>⚠️ Key Terms:</strong>
                    <div class="terms-list">
                      <div class="term-item">• Indoor venues only</div>
                      <div class="term-item">• Max 7 hours included</div>
                      <div class="term-item">• ID required for pickup</div>
                      <div class="term-item">• Full payment before pickup</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
    </form>
    
    <!-- Instrument Rental Payment Modal -->
      <div id="instrumentRentalModal" class="modal">
        <div class="modal-container">
          <div class="modal-content">
            <!-- Left Side - Booking Details -->
            <div class="modal-left">
              <div class="modal-header">
                <h2 class="modal-title">INSTRUMENT RENTAL</h2>
                <p class="modal-subtitle">SELECT DATE</p>
                <p class="location">📍 288H Sto.Domingo Street 2nd Filmont Homes Subdivision, Calamba, 4027 Laguna</p>
              </div>
              
              <div class="booking-summary-list">
                <div class="summary-header">
                  <h3><i class="fas fa-calendar-check"></i> Rental Summary</h3>
                </div>
                
                <div class="summary-list">
                  <div class="summary-list-item">
                    <div class="item-icon">
                      <i class="fas fa-music"></i>
                    </div>
                    <div class="item-content">
                      <span class="item-label">Instrument</span>
                      <span class="item-value" id="modalInstrumentSummary">-</span>
                    </div>
                  </div>
                  
                  <div class="summary-list-item">
                    <div class="item-icon">
                      <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="item-content">
                      <span class="item-label">Rental Period</span>
                      <span class="item-value" id="modalRentalPeriod">-</span>
                    </div>
                  </div>
                  
                  <div class="summary-list-item">
                    <div class="item-icon">
                      <i class="fas fa-clock"></i>
                    </div>
                    <div class="item-content">
                      <span class="item-label">Event Duration</span>
                      <span class="item-value" id="modalEventDuration">7 hours</span>
                    </div>
                  </div>
                  
                  <div class="summary-list-item">
                    <div class="item-icon">
                      <i class="fas fa-truck"></i>
                    </div>
                    <div class="item-content">
                      <span class="item-label">Delivery</span>
                      <span class="item-value" id="modalDeliveryInfo">-</span>
                    </div>
                  </div>
                </div>
                
                <div class="price-breakdown-list">
                  <div class="price-list-item">
                    <span class="price-label"><i class="fas fa-tag"></i> Base Rate</span>
                    <span class="price-value" id="modalBaseRate">₱0.00</span>
                  </div>
                  <div class="price-list-item" id="transportationFeeRow" style="display: none;">
                    <span class="price-label"><i class="fas fa-shipping-fast"></i> Delivery Fee</span>
                    <span class="price-value" id="modalTransportationFee">₱0.00</span>
                  </div>
                  <div class="price-list-item" id="extraHoursRow" style="display: none;">
                    <span class="price-label"><i class="fas fa-plus-circle"></i> Extra Hours</span>
                    <span class="price-value" id="modalExtraHours">₱0.00</span>
                  </div>
                  <div class="price-list-item reservation-fee">
                    <span class="price-label"><i class="fas fa-shield-alt"></i> Reservation Fee</span>
                    <span class="price-value" id="modalReservationFee">₱300.00</span>
                  </div>
                  <div class="price-list-total">
                    <span class="total-label"><i class="fas fa-calculator"></i> Total Amount</span>
                    <span class="total-value" id="modalTotalPrice">₱300.00</span>
                  </div>
                </div>
                

              </div>
            </div>
            
            <!-- Center - Form Section -->
            <div class="modal-center">
              <h3>Enter Details</h3>
              <form id="instrumentRentalForm" method="POST" action="{{ route('instrument-rental.store') }}" enctype="multipart/form-data">
                @csrf
                
                <!-- Hidden fields for booking data -->
                <input type="hidden" id="modalInstrumentTypeInput" name="instrument_type">
                <input type="hidden" id="modalInstrumentNameInput" name="instrument_name">
                <input type="hidden" id="modalStartDateInput" name="rental_start_date">
                <input type="hidden" id="modalEndDateInput" name="rental_end_date">
                <input type="hidden" id="modalDurationInput" name="rental_duration">
                <input type="hidden" id="modalEventDurationInput" name="event_duration_hours">
                <input type="hidden" id="modalPickupLocationInput" name="pickup_location">
                <input type="hidden" id="modalReturnLocationInput" name="return_location">
                <input type="hidden" id="modalTransportationInput" name="transportation">
                <input type="hidden" id="modalFullPackageInput" name="full_package">
                <input type="hidden" id="modalTotalAmountInput" name="total_amount">
                <input type="hidden" id="modalVenueTypeInput" name="venue_type">
                <input type="hidden" id="modalNotesInput" name="notes">
                <input type="hidden" id="modalDocumentationConsentInput" name="documentation_consent">
                
                <div class="form-group">
                  <label class="form-label" for="modalFullName">FULL NAME *</label>
                  <input type="text" id="modalFullName" name="name" class="form-input" required>
                </div>
                
                <div class="form-group">
                  <label class="form-label" for="modalEmail">EMAIL ADDRESS *</label>
                  <input type="email" id="modalEmail" name="email" class="form-input" required>
                </div>
                
                <div class="form-group">
                  <label class="form-label" for="modalContactNumber">CONTACT NUMBER *</label>
                  <input type="tel" id="modalContactNumber" name="phone" class="form-input" required>
                </div>
                
                <div class="form-group">
                  <label class="form-label" for="modalReferenceCode">GCASH PAYMENT REFERENCE NUMBER</label>
                  <input type="text" id="modalReferenceCode" name="reference_number" class="form-input" maxlength="13" pattern="[0-9]{13}" placeholder="Enter 13-digit reference number">
                  <!-- Error message container for inline validation -->
                  <div id="modalReferenceErrorMessage" style="display: none; background-color: #fee2e2; color: #dc2626; padding: 8px 12px; margin: 5px 0 0 0; border-radius: 6px; border-left: 4px solid #dc2626; font-size: 0.85rem;">
                    <span id="modalReferenceErrorText">Reference number already exists.</span>
                  </div>
                </div>
                
                <div class="form-group">
                  <label class="form-label" for="modalPicture">UPLOAD PICTURE</label>
                  <input type="file" id="modalPicture" name="picture" class="file-input" accept="image/*">
                  <div class="alert alert-warning" style="background-color: #fff3cd; color: #856404; padding: 10px; margin: 5px 0; border-radius: 5px; border: 1px solid #ffeaa7; font-size: 0.85rem;">
                    ⚠️ Please upload a clear image of your GCash payment receipt. Accepted formats: JPG, PNG, GIF. Maximum file size: 5MB.
                  </div>
                </div>
                

                
                <div class="checkbox-group">
                  <input type="checkbox" id="modalAgreement" name="agreement" required>
                  <label class="checkbox-label" for="modalAgreement">I agree to <a href="#" target="_blank">User Agreement</a> and <a href="#" target="_blank">Privacy Policy</a></label>
                </div>
                
                <div class="modal-buttons">
                  <button type="button" class="btn-cancel" id="cancelRentalModal">Cancel</button>
                  <button type="submit" class="btn-confirm">Confirm Booking</button>
                </div>
              </form>
            </div>
            
            <!-- Right Side - GCash Payment Section -->
            <div class="modal-right">
              <div class="gcash-container">
                <div class="gcash-logo">
                  <span>💳</span> GCash
                </div>
                
                <div class="gcash-qr">
                  <img src="{{ asset('images/LemonQr.png') }}" alt="GCash QR Code">
                </div>
                
                <div class="gcash-details">
                  <div>Scan to pay with GCash</div>
                  <div class="gcash-merchant">LEMON HUB</div>
                  <div>Mobile No: 0995...217</div>
                  <div>Account ID: ...60JPSU</div>
                </div>
                
                <div class="gcash-amount">₱ <span id="modalGcashAmount">300.00</span></div>
              </div>
            </div>
          </div>
        </div>
    </div>
  </main>

  <!-- Calendar Section for Viewing Rental Bookings -->
  <section class="calendar-section" style="padding: 40px 20px; background: #f8f9fa;">
    <div class="container" style="max-width: 1200px; margin: 0 auto;">
      <h2 style="text-align: center; margin-bottom: 30px; color: #333;">
        <i class="fas fa-calendar-alt" style="color: #ffd700; margin-right: 10px;"></i>
        Instrument Rental Calendar
      </h2>
      <p style="text-align: center; color: #666; margin-bottom: 40px;">
        View existing instrument rental bookings by selecting a date
      </p>
      
      <!-- Floating Action Button for Calendar -->
      <button class="calendar-fab" id="calendarFab" title="Toggle Calendar" style="
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ffd700 0%, #dbb411 100%);
        border: none;
        box-shadow: 0 4px 20px rgba(255, 215, 0, 0.4);
        cursor: pointer;
        z-index: 1000;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
      ">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="color: #333;">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2"/>
          <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2"/>
          <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2"/>
          <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2"/>
        </svg>
      </button>
      
      <div class="calendar-container hidden" id="calendarContainer" style="
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        padding: 30px;
        max-width: 800px;
        margin: 0 auto;
      ">
        <div id="calendar-header" style="
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 20px;
          padding: 0 10px;
        ">
          <button id="prevMonth" style="
            background: #ffd700;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            font-size: 18px;
            color: #333;
            transition: all 0.3s ease;
          ">&#9664;</button>
          <span id="monthYear" style="
            font-size: 20px;
            font-weight: bold;
            color: #333;
          "></span>
          <button id="nextMonth" style="
            background: #ffd700;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            font-size: 18px;
            color: #333;
            transition: all 0.3s ease;
          ">&#9654;</button>
        </div>
        <div class="calendar-grid" id="calendarGrid" style="
          display: grid;
          grid-template-columns: repeat(7, 1fr);
          gap: 2px;
          margin-bottom: 20px;
        "></div>
        <div class="time-slots" id="timeSlots" style="
          min-height: 100px;
          padding: 20px;
          background: #f8f9fa;
          border-radius: 8px;
          border: 2px solid #e9ecef;
        "></div>
      </div>
    </div>
  </section>

  <!-- Add spacing before footer -->
  <div style="height: 100px; margin-top: 50px;"></div>

  <footer class="booking-footer">
    <div class="footer-content">
      <p>&copy; 2025 Lemon Hub Studio - All Rights Reserved</p>
      <p>Professional Music Studio Services</p>
    </div>
  </footer>

  <script>
    // Instrument rental form functionality
    document.addEventListener('DOMContentLoaded', function() {
      const instrumentTypeSelect = document.getElementById('instrument_type');
      const instrumentNameSelect = document.getElementById('instrument_name');
      const dailyRateInput = document.getElementById('daily_rate');
      const startDateInput = document.getElementById('rental_start_date');
      const endDateInput = document.getElementById('rental_end_date');
      const durationInput = document.getElementById('rental_duration');
      const form = document.getElementById('rentalForm');

      // Available instruments data
      const availableInstruments = @json($availableInstruments ?? []);
      const dailyRates = @json($dailyRates ?? []);
      
      // Store booked dates for conflict checking
      let bookedDates = [];
      
      // Fetch booked dates from API
      async function fetchBookedDates() {
        try {
          const response = await fetch('/api/instrument-rental/booked-dates');
          const data = await response.json();
          bookedDates = data.booked_dates || [];
          updateDatePickerConstraints();
        } catch (error) {
          console.error('Error fetching booked dates:', error);
        }
      }
      
      // Update date picker constraints to disable booked dates
      function updateDatePickerConstraints() {
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        startDateInput.setAttribute('min', today);
        endDateInput.setAttribute('min', today);
        
        // Add event listeners to validate selected dates
        startDateInput.addEventListener('input', validateDateSelection);
        endDateInput.addEventListener('input', validateDateSelection);
        
        // Add CSS to visually disable booked dates
        addDisabledDatesStyle();
      }
      
      // Add CSS styling for disabled dates
      function addDisabledDatesStyle() {
        // Remove existing style if it exists
        const existingStyle = document.getElementById('disabled-dates-style');
        if (existingStyle) {
          existingStyle.remove();
        }
        
        // Create CSS rules to disable booked dates
        const style = document.createElement('style');
        style.id = 'disabled-dates-style';
        
        let cssRules = '';
        bookedDates.forEach(date => {
          // Convert date to the format used by date input (YYYY-MM-DD)
          const dateObj = new Date(date + 'T00:00:00');
          const year = dateObj.getFullYear();
          const month = String(dateObj.getMonth() + 1).padStart(2, '0');
          const day = String(dateObj.getDate()).padStart(2, '0');
          const formattedDate = `${year}-${month}-${day}`;
          
          cssRules += `
            input[type="date"]::-webkit-calendar-picker-indicator {
              pointer-events: auto;
            }
            input[type="date"][value="${formattedDate}"] {
              background-color: #ffebee !important;
              color: #c62828 !important;
              border-color: #e57373 !important;
            }
          `;
        });
        
        style.textContent = cssRules;
        document.head.appendChild(style);
      }
      
      // Validate that selected dates don't conflict with existing bookings
      function validateDateSelection(event) {
        const input = event.target;
        const selectedDate = input.value;
        
        // Remove any existing conflict alerts
        const existingAlert = document.querySelector('.date-conflict-alert');
        if (existingAlert) {
          existingAlert.remove();
        }
        
        // Check if selected date is in the past
        if (selectedDate) {
          const today = new Date();
          today.setHours(0, 0, 0, 0); // Set to start of today
          const selected = new Date(selectedDate + 'T00:00:00');
          
          if (selected < today) {
            // Prevent past date selection
            input.value = '';
            input.classList.add('date-unavailable');
            
            const formattedDate = selected.toLocaleDateString('en-US', { 
              year: 'numeric', 
              month: 'long', 
              day: 'numeric' 
            });
            
            // Create and show alert for past date
            const alertDiv = document.createElement('div');
            alertDiv.className = 'date-conflict-alert';
            alertDiv.style.display = 'block';
            alertDiv.innerHTML = `
              <strong>Invalid Date:</strong> ${formattedDate} is in the past. Please select today or a future date.
            `;
            
            input.parentNode.insertBefore(alertDiv, input.nextSibling);
            
            // Remove alert after 5 seconds
            setTimeout(() => {
              if (alertDiv.parentNode) {
                alertDiv.remove();
              }
            }, 5000);
            
            return false;
          }
        }
        
        if (selectedDate && bookedDates.includes(selectedDate)) {
          // Prevent the date from being selected
          input.value = '';
          
          // Add visual styling to indicate unavailable date
          input.classList.add('date-unavailable');
          
          // Show user-friendly message with better styling
          const dateObj = new Date(selectedDate + 'T00:00:00');
          const formattedDate = dateObj.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
          });
          
          // Create and show alert
          const alertDiv = document.createElement('div');
          alertDiv.className = 'date-conflict-alert';
          alertDiv.style.display = 'block';
          alertDiv.innerHTML = `
            <strong>Date Unavailable:</strong> ${formattedDate} is already booked for studio/band or solo rehearsal. Please choose a different date.
          `;
          
          input.parentNode.insertBefore(alertDiv, input.nextSibling);
          
          // Remove alert after 5 seconds
          setTimeout(() => {
            if (alertDiv.parentNode) {
              alertDiv.remove();
            }
          }, 5000);
          
          return false;
        } else {
          // Remove unavailable styling if date is valid
          input.classList.remove('date-unavailable');
        }
        
        // Additional validation for date range conflicts
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        
        if (startDate && endDate) {
          const start = new Date(startDate);
          const end = new Date(endDate);
          const conflictDates = [];
          
          // Check if any date in the range is booked
          for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
            const dateStr = d.toISOString().split('T')[0];
            if (bookedDates.includes(dateStr)) {
              conflictDates.push(dateStr);
            }
          }
          
          if (conflictDates.length > 0) {
            const conflictDateStrings = conflictDates.map(date => {
              const dateObj = new Date(date + 'T00:00:00');
              return dateObj.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
              });
            });
            
            // Create and show range conflict alert
            const alertDiv = document.createElement('div');
            alertDiv.className = 'date-conflict-alert';
            alertDiv.style.display = 'block';
            alertDiv.innerHTML = `
              <strong>Date Range Conflict:</strong> The following dates in your rental period are already booked: ${conflictDateStrings.join(', ')}. Please choose different dates.
            `;
            
            input.parentNode.insertBefore(alertDiv, input.nextSibling);
            
            // Clear the problematic date and add styling
            if (input === endDateInput) {
              endDateInput.value = '';
              endDateInput.classList.add('date-unavailable');
            } else if (input === startDateInput) {
              startDateInput.value = '';
              startDateInput.classList.add('date-unavailable');
            }
            
            // Remove alert after 7 seconds
            setTimeout(() => {
              if (alertDiv.parentNode) {
                alertDiv.remove();
              }
            }, 7000);
            
            return false;
          }
        }
        
        return true;
      }
       
       // Initialize booked dates on page load
       fetchBookedDates();

      // Update instruments when type changes
      instrumentTypeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        const dailyRate = dailyRates[selectedType] || 10;
        
        // Update daily rate display
        dailyRateInput.value = `₱${dailyRate.toFixed(2)}`;
        
        // Update instrument options
        instrumentNameSelect.innerHTML = '<option value="">Select Specific Instrument</option>';
        instrumentNameSelect.disabled = !selectedType;
        
        if (selectedType && availableInstruments[selectedType]) {
          Object.entries(availableInstruments[selectedType]).forEach(([key, name]) => {
            const option = document.createElement('option');
            option.value = key;
            option.textContent = name;
            instrumentNameSelect.appendChild(option);
          });
        }
        
        updatePriceSummary();
      });

      // Calculate duration when dates change
      function calculateDuration() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        
        if (startDate && endDate && endDate >= startDate) {
          const diffTime = Math.abs(endDate - startDate);
          const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // Include both start and end dates
          durationInput.value = `${diffDays} day${diffDays !== 1 ? 's' : ''}`;
        } else {
          durationInput.value = '0 days';
        }
        
        updatePriceSummary();
      }

             startDateInput.addEventListener('change', calculateDuration);
       endDateInput.addEventListener('change', calculateDuration);

       // Add event listeners for full package and transportation
       document.getElementById('full_package').addEventListener('change', function() {
         const fullPackage = this.checked;
         
         // Disable/enable instrument selection fields
         instrumentTypeSelect.disabled = fullPackage;
         instrumentNameSelect.disabled = fullPackage;
         
         // Clear selections when full package is enabled
         if (fullPackage) {
           instrumentTypeSelect.value = '';
           instrumentNameSelect.value = '';
           instrumentNameSelect.innerHTML = '<option value="">Select instrument type first</option>';
         }
         
         updatePriceSummary();
       });
       document.getElementById('transportation_select').addEventListener('change', updatePriceSummary);
       
       // Add event listener for event duration hours
       document.querySelector('input[name="event_duration_hours"]').addEventListener('input', updatePriceSummary);

             // Update price summary
       function updatePriceSummary() {
         const selectedType = instrumentTypeSelect.value;
         const fullPackage = document.getElementById('full_package').checked;
         const transportation = document.getElementById('transportation_select').value;
         const eventDurationHours = parseInt(document.querySelector('input[name="event_duration_hours"]').value) || 7;
         
         let dailyRate = 0;
         let duration = parseInt(durationInput.value) || 0;
         
         if (fullPackage) {
           dailyRate = 4500; // Full package rate
         } else {
           dailyRate = dailyRates[selectedType] || 0;
         }
         
         const subtotal = dailyRate * duration;
         const transportationFee = transportation === 'delivery' ? 550 : 0; // Average of ₱500-₱600
         const reservationFee = fullPackage ? 500 : 300; // ₱500 for full package, ₱300 for individual items
         
         // Calculate extra hour charges (₱200 per exceeding hour after 7 hours)
         const maxIncludedHours = 7;
         const extraHours = Math.max(0, eventDurationHours - maxIncludedHours);
         const extraHourCharges = extraHours * 200;
         
         const total = subtotal + transportationFee + reservationFee + extraHourCharges;
         
         document.getElementById('summary_daily_rate').textContent = `₱${dailyRate.toFixed(2)}`;
         document.getElementById('summary_duration').textContent = `${duration} day${duration !== 1 ? 's' : ''}`;
         document.getElementById('summary_subtotal').textContent = `₱${subtotal.toFixed(2)}`;
         document.getElementById('transportation_fee').textContent = `₱${transportationFee.toFixed(2)}`;
         
         // Update reservation fee display
         const reservationFeeElement = document.querySelector('.price-item:nth-child(4) span:last-child');
         if (reservationFeeElement) {
           reservationFeeElement.textContent = `₱${reservationFee.toFixed(2)}`;
         }
         
         document.getElementById('summary_total').textContent = `₱${total.toFixed(2)}`;
         
         // Update the event duration note to show extra charges if applicable
         const eventDurationInput = document.querySelector('input[name="event_duration_hours"]');
         const noteElement = eventDurationInput.parentNode.querySelector('small');
         if (extraHours > 0) {
           noteElement.textContent = `Maximum 7 hours included. ₱200 per exceeding hour. (Extra ${extraHours} hour(s): ₱${extraHourCharges.toFixed(2)})`;
           noteElement.style.color = '#e67e22';
           noteElement.style.fontWeight = 'bold';
         } else {
           noteElement.textContent = 'Maximum 7 hours included. ₱200 per exceeding hour.';
           noteElement.style.color = '';
           noteElement.style.fontWeight = '';
         }
       }

      // Modal functionality
      const modal = document.getElementById('instrumentRentalModal');
      const showSummaryBtn = document.getElementById('showSummaryBtn');
      const cancelRentalModal = document.getElementById('cancelRentalModal');
      const instrumentRentalForm = document.getElementById('instrumentRentalForm');
      
      // Show modal when button is clicked
      showSummaryBtn.addEventListener('click', function() {
        // Validate form first
        if (!validateForm()) {
          return;
        }
        
        // Populate modal with current form data
        populateModal();
        
        // Show modal
        modal.style.display = 'block';
      });
      
      // Close modal functions
      function closeModalFunc() {
        modal.style.display = 'none';
      }
      
      if (cancelRentalModal) {
        cancelRentalModal.addEventListener('click', closeModalFunc);
      }
      
      // Close modal when clicking outside
      if (modal) {
        modal.addEventListener('click', function(event) {
          if (event.target === modal) {
            closeModalFunc();
          }
        });
      }
      
      // Handle form submission
      if (instrumentRentalForm) {
        instrumentRentalForm.addEventListener('submit', function(e) {
          // Allow normal form submission
          // The form will submit to the server normally
        });
      }
      
      // Form validation function
      function validateForm() {
        // Check if dates are filled
        if (!startDateInput.value || !endDateInput.value) {
          alert('Please select both start date and end date.');
          return false;
        }
        
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (startDate < today) {
          alert('Start date cannot be in the past.');
          return false;
        }
        
        if (endDate <= startDate) {
          alert('End date must be after start date.');
          return false;
        }
        
        const fullPackage = document.getElementById('full_package').checked;
        
        if (!fullPackage && (!instrumentTypeSelect.value || !instrumentNameSelect.value)) {
          alert('Please select both instrument type and specific instrument.');
          return false;
        }
        
        return true;
      }
      
      // Populate modal with form data
      function populateModal() {
        const selectedType = instrumentTypeSelect.value;
        const selectedInstrument = instrumentNameSelect.value;
        const eventDurationHours = parseInt(document.querySelector('input[name="event_duration_hours"]').value) || 7;
        const transportation = document.getElementById('transportation_select').value;
        const pickupLocation = document.querySelector('select[name="pickup_location"]').value;
        const returnLocation = document.querySelector('select[name="return_location"]').value;
        
        // Get instrument names
        const instrumentTypes = @json($instrumentTypes ?? []);
        const availableInstruments = @json($availableInstruments ?? []);
        
        // Calculate prices
        const fullPackage = document.getElementById('full_package').checked;
        let dailyRate = 0;
        let duration = parseInt(durationInput.value) || 0;
        
        if (fullPackage) {
          dailyRate = 4500;
        } else {
          dailyRate = dailyRates[selectedType] || 0;
        }
        
        const subtotal = dailyRate * duration;
        const transportationFee = transportation === 'delivery' ? 550 : 0;
        const reservationFee = fullPackage ? 500 : 300; // ₱500 for full package, ₱300 for individual items
        const maxIncludedHours = 7;
        const extraHours = Math.max(0, eventDurationHours - maxIncludedHours);
        const extraHourCharges = extraHours * 200;
        const total = subtotal + transportationFee + reservationFee + extraHourCharges;
        
        // Format dates for display
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        const formatDate = (date) => {
          return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
          });
        };
        
        // Populate modern summary fields
        const instrumentDisplay = fullPackage ? 'Full Package' : (selectedInstrument || selectedType || 'Selected Instrument');
        document.getElementById('modalInstrumentSummary').textContent = instrumentDisplay;
        
        const rentalPeriod = `${formatDate(startDate)} - ${formatDate(endDate)} (${duration} days)`;
        document.getElementById('modalRentalPeriod').textContent = rentalPeriod;
        
        document.getElementById('modalEventDuration').textContent = `${eventDurationHours} hours`;
        
        const deliveryInfo = transportation === 'delivery' ? `Delivery to ${pickupLocation}` : 'Pickup Only';
        document.getElementById('modalDeliveryInfo').textContent = deliveryInfo;
        
        // Populate price breakdown
        document.getElementById('modalBaseRate').textContent = '₱' + subtotal.toFixed(2);
        
        // Show/hide transportation fee
        const transportationRow = document.getElementById('transportationFeeRow');
        if (transportationFee > 0) {
          transportationRow.style.display = 'flex';
          document.getElementById('modalTransportationFee').textContent = '₱' + transportationFee.toFixed(2);
        } else {
          transportationRow.style.display = 'none';
        }
        
        // Show/hide extra hours
        const extraHoursRow = document.getElementById('extraHoursRow');
        if (extraHourCharges > 0) {
          extraHoursRow.style.display = 'flex';
          document.getElementById('modalExtraHours').textContent = '₱' + extraHourCharges.toFixed(2);
        } else {
          extraHoursRow.style.display = 'none';
        }
        
        // Update reservation fee
        document.getElementById('modalReservationFee').textContent = '₱' + reservationFee.toFixed(2);
        
        document.getElementById('modalTotalPrice').textContent = '₱' + total.toFixed(2);
        
        // Get additional form values
        const venueType = document.querySelector('select[name="venue_type"]').value;
        const notes = document.querySelector('textarea[name="special_requests"]').value;
        const documentationConsent = document.querySelector('input[name="documentation_consent"]').checked;
        
        // Populate hidden form fields
        // Handle full package scenario
        if (fullPackage) {
          document.getElementById('modalInstrumentTypeInput').value = 'Full Package';
          document.getElementById('modalInstrumentNameInput').value = 'Full Package';
        } else {
          document.getElementById('modalInstrumentTypeInput').value = selectedType;
          document.getElementById('modalInstrumentNameInput').value = selectedInstrument;
        }
        document.getElementById('modalStartDateInput').value = startDateInput.value;
        document.getElementById('modalEndDateInput').value = endDateInput.value;
        document.getElementById('modalDurationInput').value = durationInput.value;
        document.getElementById('modalEventDurationInput').value = eventDurationHours;
        document.getElementById('modalPickupLocationInput').value = pickupLocation;
        document.getElementById('modalReturnLocationInput').value = returnLocation;
        document.getElementById('modalTransportationInput').value = transportation;
        document.getElementById('modalFullPackageInput').value = fullPackage ? '1' : '0';
        document.getElementById('modalTotalAmountInput').value = total;
        document.getElementById('modalVenueTypeInput').value = venueType;
        document.getElementById('modalNotesInput').value = notes;
        document.getElementById('modalDocumentationConsentInput').value = documentationConsent ? '1' : '0';
        
        // Update GCash amount (security deposit)
        document.getElementById('modalGcashAmount').textContent = reservationFee.toFixed(2);
      }

      // Set minimum end date based on start date
      startDateInput.addEventListener('change', function() {
        const startDate = new Date(this.value);
        const minEndDate = new Date(startDate);
        minEndDate.setDate(minEndDate.getDate() + 1);
        endDateInput.min = minEndDate.toISOString().split('T')[0];
      });
      
      // Reference code validation functionality
      const referenceCodeInput = document.getElementById('modalReferenceCode');
      const referenceErrorMessage = document.getElementById('modalReferenceErrorMessage');
      const referenceErrorText = document.getElementById('modalReferenceErrorText');
      let validationTimeout;
      let isReferenceValid = false;
      
      // Function to show reference error
      function showReferenceError(message) {
        referenceCodeInput.classList.add('error-field');
        referenceErrorText.textContent = message;
        referenceErrorMessage.style.display = 'block';
      }
      
      // Function to hide reference error
      function hideReferenceError() {
        referenceCodeInput.classList.remove('error-field');
        referenceErrorMessage.style.display = 'none';
      }
      
      // Function to show reference success
      function showReferenceSuccess(message) {
        referenceCodeInput.classList.remove('error-field');
        referenceCodeInput.classList.add('success-field');
        referenceErrorText.textContent = message;
        referenceErrorMessage.style.display = 'block';
        referenceErrorMessage.classList.add('success-message');
      }
      
      // Function to hide reference success
      function hideReferenceSuccess() {
        referenceCodeInput.classList.remove('success-field');
        referenceErrorMessage.classList.remove('success-message');
        referenceErrorMessage.style.display = 'none';
      }
      
      if (referenceCodeInput) {
          // Add input event listener with debounce for real-time validation
          referenceCodeInput.addEventListener('input', function() {
            // Clear error and success when user starts typing
            hideReferenceError();
            hideReferenceSuccess();
          const referenceCode = this.value.trim();
          
          // Clear previous timeout
          if (validationTimeout) {
            clearTimeout(validationTimeout);
          }
          
          // Hide error message if input is empty
          if (referenceCode === '') {
            hideReferenceError();
            isReferenceValid = false;
            return;
          }
          
          // Only validate if we have exactly 13 digits
          if (referenceCode.length === 13 && /^\d{13}$/.test(referenceCode)) {
            // Show loading state (optional - you can remove this if you don't want loading indication)
            // For now, we'll just proceed with validation
            
            // Debounce the API call
            validationTimeout = setTimeout(() => {
              fetch('/api/check-reference-code', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ reference_code: referenceCode })
              })
              .then(response => response.json())
              .then(data => {
                if (data.exists) {
                  // Reference code already exists - show inline error
                  showReferenceError('Reference number already exists.');
                  isReferenceValid = false;
                } else {
                  // Reference code is available - show success message
                  showReferenceSuccess('Reference code is available.');
                  isReferenceValid = true;
                }
              })
              .catch(error => {
                console.error('Error checking reference code:', error);
                showReferenceError('Error checking reference code. Please try again.');
                isReferenceValid = false;
              });
            }, 500); // 500ms debounce
          } else {
            hideReferenceError();
            isReferenceValid = false;
          }
        });
        
        // Prevent form submission if reference is invalid
        const form = document.getElementById('instrumentRentalForm');
        if (form) {
          form.addEventListener('submit', function(e) {
            const referenceCode = referenceCodeInput.value.trim();
            
            if (referenceCode.length === 13 && /^\d{13}$/.test(referenceCode) && !isReferenceValid) {
              e.preventDefault();
              showReferenceError('Please use a different reference code. The current one is already taken.');
              referenceCodeInput.focus();
              return false;
            }
          });
        }
      }
    });
  </script>

  <!-- Success Confirmation Modal -->
  @if(session('booking_confirmed'))
  <div id="successModal" class="modal" style="display: block; animation: fadeIn 0.3s ease-out;">
    <div class="modal-container" style="animation: slideInUp 0.4s ease-out;">
      <div class="modal-content" style="
        max-width: 560px;
        border-radius: 20px;
        padding: 30px;
        background: #ffffff;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border: none;
        position: relative;
        overflow: hidden;
        display: flex;
        gap: 20px;
        align-items: flex-start;
      ">
        
        <!-- Left Section: Icon and Title -->
        <div style="
          display: flex;
          flex-direction: column;
          align-items: flex-start;
          flex-shrink: 0;
        ">
          <!-- Success Icon -->
          <div style="
            width: 60px;
            height: 60px;
            margin-bottom: 16px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: bounceIn 0.6s ease-out 0.2s both;
          ">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
          </div>
          
          <!-- Title -->
          <h2 style="
            color: #10b981;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            letter-spacing: -0.3px;
            animation: fadeInUp 0.5s ease-out 0.3s both;
            white-space: nowrap;
          ">Booking<br>Confirmed!</h2>
        </div>
        
        <!-- Right Section: Details -->
        <div style="
          flex: 1;
          display: flex;
          flex-direction: column;
          gap: 16px;
        ">
          <!-- Success Message -->
          <div style="
            background: #f0fdf4;
            color: #166534;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #bbf7d0;
            font-weight: 400;
            line-height: 1.5;
            font-size: 14px;
            animation: fadeInUp 0.5s ease-out 0.4s both;
          ">
            Booking confirmed! Your rental on {{ session('booking_details.rental_start_date') }} at {{ session('booking_details.created_at') }} for {{ session('booking_details.rental_duration_days') }} day(s) has been accepted. Reference: {{ session('booking_details.reference') }}. You will receive an email confirmation shortly.
          </div>
          
          <!-- Bottom Section -->
          <div style="
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
          ">
            <!-- Email confirmation text -->
            <p style="
              color: #6b7280;
              margin: 0;
              font-size: 14px;
              font-weight: 400;
              animation: fadeInUp 0.5s ease-out 0.5s both;
              flex: 1;
            ">You will receive an email confirmation shortly.</p>
            
            <!-- Countdown -->
            <div style="
              color: #6b7280;
              font-size: 13px;
              font-weight: 400;
              animation: fadeInUp 0.5s ease-out 0.6s both;
              text-align: right;
              flex-shrink: 0;
            ">
              Redirecting in <span id="countdown" style="color: #374151; font-weight: 500;">4</span> seconds...
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 10000;
    }

    .modal-container {
      position: relative;
      z-index: 10001;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes slideInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes bounceIn {
      0% {
        opacity: 0;
        transform: scale(0.3);
      }
      50% {
        opacity: 1;
        transform: scale(1.05);
      }
      70% {
        transform: scale(0.9);
      }
      100% {
        opacity: 1;
        transform: scale(1);
      }
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>

  <script>
    // Auto-redirect after countdown
    @if(session('booking_confirmed'))
    let countdown = 4;
    const countdownElement = document.getElementById('countdown');
    const countdownInterval = setInterval(() => {
      countdown--;
      if (countdownElement) {
        countdownElement.textContent = countdown;
      }
      if (countdown <= 0) {
        clearInterval(countdownInterval);
        window.location.href = '/instrument-rental';
      }
    }, 1000);
    @endif
  </script>
  @endif

  <script src="{{ asset('js/page-transitions.js') }}"></script>
  
  <script>
    // User dropdown functionality
    function toggleUserDropdown() {
      const dropdown = document.getElementById('userDropdown');
      dropdown.classList.toggle('show');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      const userProfile = document.getElementById('userProfile');
      const dropdown = document.getElementById('userDropdown');
      
      if (userProfile && dropdown && !userProfile.contains(event.target)) {
        dropdown.classList.remove('show');
      }
    });

    // Calendar functionality for instrument rentals
    let currentDate = new Date();
    let selectedDate = null;
    let bookedDates = [];

    // Initialize calendar when page loads
    document.addEventListener('DOMContentLoaded', function() {
      generateCalendar();
      fetchBookedDates();
    });

    // Toggle calendar visibility
    function toggleCalendar() {
      const calendarContainer = document.getElementById('calendarContainer');
      const calendarFab = document.getElementById('calendarFab');
      
      if (calendarContainer.style.display === 'none' || calendarContainer.style.display === '') {
        calendarContainer.style.display = 'block';
        calendarFab.innerHTML = '<i class="fas fa-times"></i>';
        calendarFab.style.backgroundColor = '#ef4444';
      } else {
        calendarContainer.style.display = 'none';
        calendarFab.innerHTML = '<i class="fas fa-calendar-alt"></i>';
        calendarFab.style.backgroundColor = '#3b82f6';
      }
    }

    // Generate calendar
    function generateCalendar() {
      const calendarGrid = document.getElementById('calendarGrid');
      const monthYear = document.getElementById('monthYear');
      
      const year = currentDate.getFullYear();
      const month = currentDate.getMonth();
      
      monthYear.textContent = new Date(year, month).toLocaleDateString('en-US', { 
        month: 'long', 
        year: 'numeric' 
      });
      
      calendarGrid.innerHTML = '';
      
      // Add day headers
      const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
      dayHeaders.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.className = 'calendar-day-header';
        dayHeader.textContent = day;
        calendarGrid.appendChild(dayHeader);
      });
      
      // Get first day of month and number of days
      const firstDay = new Date(year, month, 1).getDay();
      const daysInMonth = new Date(year, month + 1, 0).getDate();
      
      // Add empty cells for days before month starts
      for (let i = 0; i < firstDay; i++) {
        const emptyDay = document.createElement('div');
        emptyDay.className = 'calendar-day empty';
        calendarGrid.appendChild(emptyDay);
      }
      
      // Add days of the month
      for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        dayElement.textContent = day;
        
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const today = new Date().toISOString().split('T')[0];
        
        // Mark past dates
        if (dateStr < today) {
          dayElement.classList.add('past');
        }
        
        // Mark booked dates
        if (bookedDates.includes(dateStr)) {
          dayElement.classList.add('booked');
        }
        
        // Mark selected date
        if (selectedDate === dateStr) {
          dayElement.classList.add('selected');
        }
        
        // Add click event
        dayElement.addEventListener('click', () => selectDate(dateStr, dayElement));
        
        calendarGrid.appendChild(dayElement);
      }
    }

    // Select date and fetch booking info
    function selectDate(dateStr, element) {
      // Remove previous selection
      document.querySelectorAll('.calendar-day.selected').forEach(day => {
        day.classList.remove('selected');
      });
      
      // Add selection to clicked date
      element.classList.add('selected');
      selectedDate = dateStr;
      
      // Fetch booking information for this date
      fetchBookingInfo(dateStr);
    }

    // Fetch booked dates
    function fetchBookedDates() {
      fetch('/api/instrument-booked-dates')
        .then(response => response.json())
        .then(data => {
          bookedDates = data.booked_dates || [];
          generateCalendar();
        })
        .catch(error => {
          console.error('Error fetching booked dates:', error);
        });
    }

    // Fetch booking information for selected date
    function fetchBookingInfo(date) {
      const bookingInfo = document.getElementById('bookingInfo');
      const bookingContent = document.getElementById('bookingContent');
      
      bookingContent.innerHTML = '<div style="text-align: center; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
      bookingInfo.style.display = 'block';
      
      fetch(`/api/instrument-bookings-by-date?date=${date}`)
        .then(response => response.json())
        .then(data => {
          if (data.success && data.bookings.length > 0) {
            displayBookingInfo(data.bookings, date);
          } else {
            displayNoBookings(date);
          }
        })
        .catch(error => {
          console.error('Error fetching booking info:', error);
          bookingContent.innerHTML = '<div style="text-align: center; padding: 20px; color: #ef4444;"><i class="fas fa-exclamation-triangle"></i> Error loading booking information</div>';
        });
    }

    // Display booking information
    function displayBookingInfo(bookings, date) {
      const bookingContent = document.getElementById('bookingContent');
      
      let html = `
        <div style="margin-bottom: 15px;">
          <h4 style="margin: 0; color: #1f2937; font-size: 16px; font-weight: 600;">
            <i class="fas fa-calendar-day" style="color: #3b82f6; margin-right: 8px;"></i>
            ${new Date(date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
          </h4>
          <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">
            ${bookings.length} instrument rental${bookings.length > 1 ? 's' : ''} for this date
          </p>
        </div>
      `;
      
      bookings.forEach((booking, index) => {
        const statusColor = booking.status === 'confirmed' ? '#10b981' : '#f59e0b';
        const statusIcon = booking.status === 'confirmed' ? 'fa-check-circle' : 'fa-clock';
        
        html += `
          <div style="
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: ${index < bookings.length - 1 ? '12px' : '0'};
          ">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
              <div>
                <h5 style="margin: 0; color: #1f2937; font-size: 15px; font-weight: 600;">
                  ${booking.instrument_name}
                </h5>
                <p style="margin: 2px 0 0 0; color: #6b7280; font-size: 13px; text-transform: capitalize;">
                  ${booking.instrument_type.replace('_', ' ')}
                </p>
              </div>
              <span style="
                background: ${statusColor};
                color: white;
                padding: 4px 8px;
                border-radius: 12px;
                font-size: 11px;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
              ">
                <i class="fas ${statusIcon}" style="margin-right: 4px;"></i>
                ${booking.status}
              </span>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
              <div>
                <p style="margin: 0; color: #6b7280; font-size: 12px; font-weight: 500;">Customer</p>
                <p style="margin: 2px 0 0 0; color: #1f2937; font-size: 13px;">${booking.customer_name}</p>
              </div>
              <div>
                <p style="margin: 0; color: #6b7280; font-size: 12px; font-weight: 500;">Total Cost</p>
                <p style="margin: 2px 0 0 0; color: #1f2937; font-size: 13px; font-weight: 600;">₱${booking.total_cost}</p>
              </div>
            </div>
            
            <div style="margin-bottom: 10px;">
              <p style="margin: 0; color: #6b7280; font-size: 12px; font-weight: 500;">Rental Period</p>
              <p style="margin: 2px 0 0 0; color: #1f2937; font-size: 13px;">
                ${new Date(booking.rental_start_date).toLocaleDateString()} - ${new Date(booking.rental_end_date).toLocaleDateString()}
              </p>
            </div>
            
            ${booking.pickup_location ? `
              <div style="margin-bottom: 10px;">
                <p style="margin: 0; color: #6b7280; font-size: 12px; font-weight: 500;">Pickup Location</p>
                <p style="margin: 2px 0 0 0; color: #1f2937; font-size: 13px;">${booking.pickup_location}</p>
              </div>
            ` : ''}
            
            ${booking.transportation ? `
              <div style="margin-bottom: 10px;">
                <p style="margin: 0; color: #6b7280; font-size: 12px; font-weight: 500;">Transportation</p>
                <p style="margin: 2px 0 0 0; color: #1f2937; font-size: 13px; text-transform: capitalize;">${booking.transportation}</p>
              </div>
            ` : ''}
            
            ${booking.special_requests ? `
              <div>
                <p style="margin: 0; color: #6b7280; font-size: 12px; font-weight: 500;">Special Requests</p>
                <p style="margin: 2px 0 0 0; color: #1f2937; font-size: 13px;">${booking.special_requests}</p>
              </div>
            ` : ''}
          </div>
        `;
      });
      
      bookingContent.innerHTML = html;
    }

    // Display no bookings message
    function displayNoBookings(date) {
      const bookingContent = document.getElementById('bookingContent');
      
      bookingContent.innerHTML = `
        <div style="text-align: center; padding: 30px 20px;">
          <div style="
            width: 60px;
            height: 60px;
            background: #f3f4f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px auto;
          ">
            <i class="fas fa-guitar" style="color: #9ca3af; font-size: 24px;"></i>
          </div>
          <h4 style="margin: 0 0 8px 0; color: #1f2937; font-size: 16px; font-weight: 600;">
            No instrument rentals for this date
          </h4>
          <p style="margin: 0; color: #6b7280; font-size: 14px;">
            ${new Date(date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}
          </p>
        </div>
      `;
    }

    // Navigate calendar months
    function previousMonth() {
      currentDate.setMonth(currentDate.getMonth() - 1);
      generateCalendar();
    }

    function nextMonth() {
      currentDate.setMonth(currentDate.getMonth() + 1);
      generateCalendar();
    }
  </script>
</body>
</html>