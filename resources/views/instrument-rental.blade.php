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

    /* Consent checkbox alignment and spacing */
    .form-note {
      margin-top: 8px;
      margin-bottom: 16px;
      color: #4a5568;
      font-size: 0.9em;
    }
    .consent-group .checkbox-label {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 4px;
    }
    .consent-group input[type="checkbox"] {
      width: auto;
      padding: 0;
      border: none;
      box-shadow: none;
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
         overflow: hidden;
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
         flex: 1 1 auto;
         display: flex;
         justify-content: space-between;
         align-items: center;
         min-width: 0;
         gap: 8px;
       }
       
       .item-label {
         font-size: 0.9rem;
         color: #6c757d;
         font-weight: 500;
         flex: 0 0 auto;
         white-space: nowrap;
       }
       
       .item-value {
         font-weight: 600;
         color: #2c3e50;
         font-size: 0.9rem;
         /* Prevent long values from overflowing the summary row */
         flex: 1 1 auto;
         min-width: 0;
         white-space: nowrap;
         overflow: hidden;
         text-overflow: ellipsis;
         text-align: right;
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
         flex-direction: column;
         max-height: 92vh;
       }
       
       .modal-header {
         padding: 20px 15px;
         border-radius: 15px 15px 0 0;
       }
       
       .modal-body {
         padding: 20px 15px;
       }

       /* Stack modal panels for mobile */
       .modal-left,
       .modal-center,
       .modal-right {
         width: 100%;
         flex: 1 1 auto;
         max-height: none;
         overflow: visible;
         padding: 16px 14px;
         border: none;
         border-radius: 0;
       }

       .modal-left { border-radius: 12px 12px 0 0; }
       .modal-right { border-radius: 0 0 12px 12px; }
      
       /* Compact form controls in modal for mobile */
       .modal-center h3 {
         font-size: 1em;
         margin-bottom: 8px;
         letter-spacing: 0.2px;
       }
       .modal-center .form-group {
         margin-bottom: 10px;
       }
       .modal-center .form-group label,
       .modal-center .form-label {
         font-size: 0.85em;
         margin-bottom: 6px;
         line-height: 1.2;
       }
       .modal-center .form-input,
       .modal-center .form-group input,
       .modal-center .form-group select,
       .modal-center .form-group textarea {
         padding: 10px 12px;
         font-size: 14px;
         border-radius: 8px;
       }
       .modal-center .form-group input[type="file"] {
         padding: 6px;
       }
       .modal-center small,
       .modal-center .form-note { font-size: 0.8em; }
       
       /* Slightly smaller summary text */
       .modal-left .item-label,
       .modal-left .item-value { font-size: 0.85rem; }
       .modal-left .booking-summary-list { padding: 16px; }
       
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
         padding: 12px;
       }
       /* Scale payment panel elements for small screens */
       .gcash-container { padding: 16px; }
       .qr-code img { width: 120px; height: 120px; }
       .amount-display { font-size: 24px; }
       .modal-actions { flex-direction: column; }
       .btn-cancel-modal, .btn-confirm-booking { width: 100%; padding: 10px 16px; font-size: 14px; }
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

       /* Mobile layout for payment modal */
       .modal-container { padding: 12px; }
       .modal-content {
         flex-direction: column;
         width: 95%;
         max-height: 92vh;
       }
       .modal-left,
       .modal-center,
       .modal-right {
         width: 100%;
         flex: 1 1 auto;
         max-height: none;
         overflow: visible;
         padding: 18px 16px;
         border: none;
         border-radius: 0;
       }
       .modal-left { border-radius: 14px 14px 0 0; }
       .modal-right { border-radius: 0 0 14px 14px; }
       .modal-footer { flex-direction: column; }
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
     
     /* Viewport and scrolling fixes scoped to booking page */
     html, body.booking-page {
       height: auto !important;
       min-height: 100vh !important;
     }
     html {
       overflow-y: auto !important;
     }
     body.booking-page {
       overflow-y: visible !important;
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
            <p class="form-note">Indoor venue (Required for safety)</p>

            <div class="form-group consent-group">
              <label class="checkbox-label">
                <input type="checkbox" name="documentation_consent" value="1" checked>
                I consent to photos/videos being taken for studio documentation
              </label>
            </div>
            <div class="form-group">
              <label for="special_requests">Special Requirements or Notes:</label>
              <textarea name="special_requests" id="special_requests" rows="3" placeholder="Any special requirements, setup needs, or additional information..."></textarea>
            </div>
          </section>

          <section class="form-section">
            <h3><i class="fas fa-calendar-alt"></i> Rental Period</h3>

            <div class="form-group" id="startDateGroup">
              <label for="rental_start_date" id="startDateLabel">Rent Date:</label>
              <input type="date" id="rental_start_date" name="rental_start_date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
              <small class="form-note">Single day rent only.</small>
            </div>

            <div class="form-group" id="eventDurationGroup">
              <label for="event_duration_hours">Event Duration (Hours):</label>
              <input type="number" name="event_duration_hours" min="1" max="12" value="7">
              <small>Maximum 7 hours included. ₱200 per exceeding hour.</small>
            </div>

            <div class="form-group" id="endDateGroup" style="display: none;">
              <label for="rental_end_date">End Date:</label>
              <input type="date" id="rental_end_date" name="rental_end_date">
            </div>

            <div class="form-group">
              <label for="rental_duration">Duration:</label>
              <input type="text" id="rental_duration" value="0 days" readonly>
            </div>

            <div class="form-group">
              <label for="transportation">Transportation Method:</label>
              <select name="transportation" id="transportation_select">
                <option value="none">Self Pickup</option>
                <option value="delivery">Deliver</option>
              </select>
            </div>

            <div class="form-group" id="pickupLocationGroup">
              <label for="pickup_location">Pickup Location:</label>
              <select name="pickup_location" id="pickup_location" disabled>
                <option value="Studio" selected>Studio (288H Sto.Domingo Street, Calamba)</option>
              </select>
            </div>

            <div class="form-group" id="returnLocationGroup">
              <label for="return_location">Return Location:</label>
              <select name="return_location" id="return_location">
                <option value="Studio">Studio (288H Sto.Domingo Street, Calamba)</option>
                <option value="Pickup">Pickup Service (Additional fee may apply)</option>
              </select>
            </div>

            <!-- Self Pickup: Pick-up Time (shown only when self pickup is selected) -->
            <div class="form-group" id="pickupTimeGroup" style="display: none;">
              <label for="pickup_time">Pick-up Time:</label>
              <select id="pickup_time" name="pickup_time"></select>
            </div>

            <div class="form-group" id="deliveryLocationGroup" style="display: none;">
              <label for="delivery_location">Delivery Location:</label>
              <input type="text" id="delivery_location" name="delivery_location" placeholder="Enter event delivery address">
            </div>

            <!-- Delivery: Delivery Time (shown only when delivery is selected) -->
            <div class="form-group" id="deliveryTimeGroup" style="display: none;">
              <label for="delivery_time">Delivery Time:</label>
              <select id="delivery_time" name="delivery_time"></select>
            </div>

            <div class="form-group" id="pickupFromEventGroup" style="display: none;">
              <label>
                <input type="checkbox" id="pickup_from_event" name="pickup_from_event" value="1"> Pick up from event location
              </label>
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
                <span id="reservation_fee">₱300.00</span>
              </div>
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
                <input type="hidden" id="modalRentTypeInput" name="rent_type" value="single">
                
                <!-- Hidden fields for booking data -->
                <input type="hidden" id="modalInstrumentTypeInput" name="instrument_type">
                <input type="hidden" id="modalInstrumentNameInput" name="instrument_name">
                <input type="hidden" id="modalStartDateInput" name="rental_start_date">
                <input type="hidden" id="modalEndDateInput" name="rental_end_date">
                <input type="hidden" id="modalDurationInput" name="rental_duration">
                <input type="hidden" id="modalEventDurationInput" name="event_duration_hours">
                <input type="hidden" id="modalPickupLocationInput" name="pickup_location">
                <input type="hidden" id="modalPickupTimeInput" name="pickup_time">
                <input type="hidden" id="modalReturnLocationInput" name="return_location">
                <input type="hidden" id="modalTransportationInput" name="transportation">
                <input type="hidden" id="modalFullPackageInput" name="full_package">
                <input type="hidden" id="modalTotalAmountInput" name="total_amount">
                <input type="hidden" id="modalVenueTypeInput" name="venue_type">
                <input type="hidden" id="modalNotesInput" name="notes">
                <input type="hidden" id="modalDocumentationConsentInput" name="documentation_consent">
                <input type="hidden" id="modalDeliveryLocationInput" name="delivery_location">
                <input type="hidden" id="modalPickupFromEventInput" name="pickup_from_event">
                <input type="hidden" id="modalDeliveryTimeInput" name="delivery_time">
                
                <div class="form-group">
                  <label class="form-label" for="modalFullName">FULL NAME *</label>
                  <input type="text" id="modalFullName" name="name" class="form-input" required>
                </div>
                
                @auth
                  <!-- Email is sourced from the authenticated account; no visible field -->
                  <input type="hidden" id="modalEmail" name="email" value="{{ auth()->user()->email }}">
                @else
                  <div class="form-group">
                    <label class="form-label" for="modalEmail">EMAIL ADDRESS *</label>
                    <input type="email" id="modalEmail" name="email" class="form-input" required>
                  </div>
                @endauth
                
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
  <!-- Instrument Rental Calendar section removed -->

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
      const eventDurationGroup = document.getElementById('eventDurationGroup');
      const startDateGroup = document.getElementById('startDateGroup');
      const endDateGroup = document.getElementById('endDateGroup');
      const transportationSelect = document.getElementById('transportation_select');
      const deliveryLocationGroup = document.getElementById('deliveryLocationGroup');
      const pickupFromEventGroup = document.getElementById('pickupFromEventGroup');
      const pickupLocationSelect = document.getElementById('pickup_location');
      const returnLocationSelect = document.getElementById('return_location');
      const pickupTimeGroup = document.getElementById('pickupTimeGroup');
      const deliveryTimeGroup = document.getElementById('deliveryTimeGroup');
      const pickupLocationGroup = document.getElementById('pickupLocationGroup');
      const returnLocationGroup = document.getElementById('returnLocationGroup');
      const pickupTimeInput = document.getElementById('pickup_time');
      const deliveryTimeInput = document.getElementById('delivery_time');
      const eventDurationInput = document.querySelector('input[name="event_duration_hours"]');
      const form = document.getElementById('rentalForm');

      // Studio closing time logic (8:00 PM) for single-day self pickup
      const STUDIO_CLOSING_TIME = '20:00';
      const eventDurationHelp = document.querySelector('#eventDurationGroup small');

      function timeToMinutes(t) {
        if (!t || typeof t !== 'string' || !t.includes(':')) return null;
        const [h, m] = t.split(':').map(Number);
        if (Number.isNaN(h) || Number.isNaN(m)) return null;
        return h * 60 + m;
      }

      function updateDurationMaxForSingleDay() {
        const method = transportationSelect ? transportationSelect.value : 'none';
        const activeTimeInput = method === 'delivery' ? deliveryTimeInput : pickupTimeInput;

        // Apply for single-day rentals using the active time input (pickup or delivery)
        if (activeTimeInput && activeTimeInput.value) {
          const closing = timeToMinutes(STUDIO_CLOSING_TIME);
          const startMinutes = timeToMinutes(activeTimeInput.value);
          if (closing == null || startMinutes == null) return;
          const remainingMinutes = closing - startMinutes;
          const allowedHours = Math.max(0, Math.floor(remainingMinutes / 60));

          if (eventDurationInput) {
            const defaultMax = parseInt(eventDurationInput.getAttribute('max') || '12', 10);
            eventDurationInput.max = Math.max(0, Math.min(defaultMax, allowedHours));
            const current = parseInt(eventDurationInput.value || '0', 10);
            if (allowedHours >= 1) {
              if (current > allowedHours) eventDurationInput.value = allowedHours;
            } else {
              eventDurationInput.value = 0;
            }
          }

          if (eventDurationHelp) {
            if (allowedHours >= 1) {
              eventDurationHelp.textContent = `Up to ${allowedHours} hour(s) available today before closing at 8:00 PM.`;
            } else {
              eventDurationHelp.textContent = `Not enough time after ${activeTimeInput.value}. Choose earlier time or another day.`;
            }
          }
        } else {
          // Reset to defaults when not applicable
          if (eventDurationInput) {
            eventDurationInput.max = 12;
          }
          if (eventDurationHelp) {
            eventDurationHelp.textContent = 'Maximum 7 hours included. ₱200 per exceeding hour.';
          }
        }
      }

      // Keep duration constraints updated
      if (pickupTimeInput) {
        pickupTimeInput.addEventListener('change', updateDurationMaxForSingleDay);
        pickupTimeInput.addEventListener('input', updateDurationMaxForSingleDay);
      }
      if (deliveryTimeInput) {
        deliveryTimeInput.addEventListener('change', updateDurationMaxForSingleDay);
        deliveryTimeInput.addEventListener('input', updateDurationMaxForSingleDay);
      }
      if (transportationSelect) transportationSelect.addEventListener('change', updateDurationMaxForSingleDay);
      // Initialize once on load
      updateDurationMaxForSingleDay();

      // ===== Real-time logic: prevent past-time selection for same-day rentals =====
      function getLocalDateString(dateObj) {
        const y = dateObj.getFullYear();
        const m = String(dateObj.getMonth() + 1).padStart(2, '0');
        const d = String(dateObj.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
      }

      function minutesToHHMM(min) {
        const h = Math.floor(min / 60);
        const m = min % 60;
        return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
      }

      function roundUpMinutesToStep(min, stepMinutes) {
        return Math.ceil(min / stepMinutes) * stepMinutes;
      }

      // ===== Slot-based time selection helpers =====
      const EARLIEST_TIME = '08:00';
      const TIME_SLOT_INTERVAL_MIN = 30;

      function formatTime12(hhmm) {
        const [hh, mm] = hhmm.split(':').map(Number);
        const period = hh >= 12 ? 'PM' : 'AM';
        const h12 = ((hh % 12) || 12);
        return `${String(h12)}:${String(mm).padStart(2,'0')} ${period}`;
      }

      function createTimeSlots(start = EARLIEST_TIME, end = STUDIO_CLOSING_TIME, intervalMinutes = TIME_SLOT_INTERVAL_MIN) {
        const slots = [];
        let curMin = timeToMinutes(start);
        const endMin = timeToMinutes(end);
        while (curMin <= endMin) {
          const hh = String(Math.floor(curMin / 60)).padStart(2, '0');
          const mm = String(curMin % 60).padStart(2, '0');
          slots.push(`${hh}:${mm}`);
          curMin += intervalMinutes;
        }
        return slots;
      }

      function populateTimeSelect(selectEl) {
        if (!selectEl) return;
        const baseSlots = createTimeSlots();
        selectEl.innerHTML = '';
        baseSlots.forEach(t => {
          const opt = document.createElement('option');
          opt.value = t;
          opt.textContent = formatTime12(t);
          selectEl.appendChild(opt);
        });
      }

      function roundUpToNextSlot(hhmm) {
        const cur = timeToMinutes(hhmm);
        const step = TIME_SLOT_INTERVAL_MIN;
        const rounded = Math.ceil(cur / step) * step;
        const hh = String(Math.floor(rounded / 60)).padStart(2, '0');
        const mm = String(rounded % 60).padStart(2, '0');
        return `${hh}:${mm}`;
      }

      function refreshTimeSlotConstraints() {
        const method = transportationSelect ? transportationSelect.value : 'none';
        const activeSelect = method === 'delivery' ? deliveryTimeInput : pickupTimeInput;
        const otherSelect = method === 'delivery' ? pickupTimeInput : deliveryTimeInput;

        // Base allowed range
        let minAllowed = EARLIEST_TIME;
        let maxAllowed = STUDIO_CLOSING_TIME;

        // For single-day, cap latest start by duration
        if (eventDurationInput && eventDurationInput.value) {
          const durationMin = parseInt(eventDurationInput.value, 10) * 60;
          const closingMin = timeToMinutes(STUDIO_CLOSING_TIME);
          const maxStartMin = Math.max(0, closingMin - durationMin);
          const hh = String(Math.floor(maxStartMin / 60)).padStart(2, '0');
          const mm = String(maxStartMin % 60).padStart(2, '0');
          maxAllowed = `${hh}:${mm}`;
        }

        // If start date is today, disallow past slots
        if (startDateInput && startDateInput.value === getLocalDateString(new Date())) {
          const now = new Date();
          const nowHH = String(now.getHours()).padStart(2, '0');
          const nowMM = String(now.getMinutes()).padStart(2, '0');
          const nowHHMM = `${nowHH}:${nowMM}`;
          const roundedNow = roundUpToNextSlot(nowHHMM);
          if (roundedNow > minAllowed) {
            minAllowed = roundedNow;
          }
        }

        // Disable invalid options and select the first valid one if needed
        function applyConstraints(selectEl) {
          if (!selectEl) return;
          let selectedValid = false;
          [...selectEl.options].forEach(opt => {
            const val = opt.value;
            const isValid = (val >= minAllowed && val <= maxAllowed);
            opt.disabled = !isValid;
            if (val === selectEl.value && isValid) {
              selectedValid = true;
            }
          });
          if (!selectedValid) {
            const firstValid = [...selectEl.options].find(o => !o.disabled);
            if (firstValid) selectEl.value = firstValid.value;
          }
        }

        applyConstraints(activeSelect);
        applyConstraints(otherSelect);
        updateDurationMaxForSingleDay();
      }

      function updatePickupMinForToday() {
        // With slot-based selects, simply refresh constraints
        refreshTimeSlotConstraints();
      }

      // Populate pickup/delivery selects on load before applying constraints
      populateTimeSelect(pickupTimeInput);
      populateTimeSelect(deliveryTimeInput);

      if (startDateInput) startDateInput.addEventListener('change', refreshTimeSlotConstraints);
      if (transportationSelect) transportationSelect.addEventListener('change', refreshTimeSlotConstraints);
      refreshTimeSlotConstraints();

      // ===== Disable current day after studio closing (8:00 PM) =====
      function isPastClosingNow() {
        const now = new Date();
        const nowMin = now.getHours() * 60 + now.getMinutes();
        const closingMin = timeToMinutes(STUDIO_CLOSING_TIME);
        return closingMin != null && nowMin >= closingMin;
      }

      function updateStartDateMinBasedOnClosing() {
        if (!startDateInput) return;
        const todayStr = getLocalDateString(new Date());
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = getLocalDateString(tomorrow);

        // If time is past closing, force min to tomorrow
        if (isPastClosingNow()) {
          startDateInput.min = tomorrowStr;
          if (startDateInput.value && startDateInput.value < startDateInput.min) {
            startDateInput.value = startDateInput.min;
          }
        } else {
          // Otherwise allow from today
          startDateInput.min = todayStr;
        }
      }

      // Initialize and keep in sync when page becomes visible again
      updateStartDateMinBasedOnClosing();
      document.addEventListener('visibilitychange', function() {
        if (!document.hidden) updateStartDateMinBasedOnClosing();
      });

      // Dynamically cap latest allowable pick-up time based on event duration
      function updatePickupMaxByDuration() {
        const method = transportationSelect ? transportationSelect.value : 'none';
        const activeTimeInput = method === 'delivery' ? deliveryTimeInput : pickupTimeInput;
        if (!activeTimeInput) return;
        // Default hard cap to closing time
        activeTimeInput.max = STUDIO_CLOSING_TIME;

        if (eventDurationInput) {
          const durationHours = parseInt(eventDurationInput.value || '0', 10);
          const closingMin = timeToMinutes(STUDIO_CLOSING_TIME);
          const maxPickupMin = Math.max(0, closingMin - (durationHours * 60));
          const maxHHMM = minutesToHHMM(maxPickupMin);
          // Set dynamic max so that pickup + duration does not pass closing
          activeTimeInput.max = maxHHMM;
          // If current value exceeds max, clamp it
          if (activeTimeInput.value && activeTimeInput.value > activeTimeInput.max) {
            activeTimeInput.value = activeTimeInput.max;
          }

          // If min > max (e.g., too late today), reflect not enough time
          if (activeTimeInput.min && activeTimeInput.max && activeTimeInput.min > activeTimeInput.max) {
            if (eventDurationHelp) {
              eventDurationHelp.textContent = `Not enough time today for ${durationHours} hour(s). Choose earlier pickup or reduce duration.`;
            }
          }
        }
      }

      if (eventDurationInput) {
        eventDurationInput.addEventListener('input', function() {
          updatePickupMaxByDuration();
          updateDurationMaxForSingleDay();
        });
        eventDurationInput.addEventListener('change', function() {
          updatePickupMaxByDuration();
          updateDurationMaxForSingleDay();
        });
      }
      if (transportationSelect) transportationSelect.addEventListener('change', updatePickupMaxByDuration);
      updatePickupMaxByDuration();

      // Available instruments data
      const availableInstruments = @json($availableInstruments ?? []);
      const dailyRates = @json($dailyRates ?? []);
      const instrumentRates = @json($instrumentRates ?? []);
      
      // Store booked dates for conflict checking
      let bookedDates = [];

      // Gate form: disable dependent fields until instrument type is selected
      (function gateFieldsOnLoad() {
        const idsToDisable = [
          'rental_end_date',
          'transportation_select',
          'pickup_time',
          'delivery_time',
          'return_location',
          'delivery_location',
          'pickup_from_event'
        ];
        idsToDisable.forEach(id => {
          const el = document.getElementById(id);
          if (el) el.disabled = true;
        });
      })();
      
      // Fetch booked dates from APIs (instrument rentals + ANY studio booking days)
      async function fetchBookedDates() {
        try {
          const [instrumentResp, studioUnavailableResp] = await Promise.all([
            fetch('/api/instrument-rental/booked-dates'),
            // Studio-level unavailable dates (fully booked or drums/full package rentals)
            fetch('/api/booked-dates')
          ]);
          const instrumentData = await instrumentResp.json();
          const studioUnavailableData = await studioUnavailableResp.json();
          const instrumentDates = instrumentData.booked_dates || [];
          const studioUnavailableDates = studioUnavailableData.booked_dates || [];
          // Merge and de-duplicate only truly unavailable dates
          bookedDates = Array.from(new Set([...instrumentDates, ...studioUnavailableDates]));
          updateDatePickerConstraints();
        } catch (error) {
          console.error('Error fetching booked dates:', error);
        }
      }
      
      // Update date picker constraints to disable booked dates
      function updateDatePickerConstraints() {
        // Respect any dynamic minimum already set (e.g., tomorrow after 8 PM)
        const today = new Date().toISOString().split('T')[0];
        const currentStartMin = startDateInput.getAttribute('min') || today;
        const effectiveMin = currentStartMin > today ? currentStartMin : today;
        
        startDateInput.setAttribute('min', effectiveMin);
        endDateInput.setAttribute('min', effectiveMin);

        // Clamp values if user previously selected a date below the effective minimum
        if (startDateInput.value && startDateInput.value < effectiveMin) {
          startDateInput.value = effectiveMin;
        }
        if (endDateInput.value && endDateInput.value < effectiveMin) {
          endDateInput.value = effectiveMin;
        }
        
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
            <strong>Date Unavailable:</strong> ${formattedDate} has an existing band rehearsal or instrument rental. Please choose a different date.
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
        const dailyRate = dailyRates[selectedType] || 0;
        
        // Update daily rate display (type-level fallback; specific instrument may override)
        dailyRateInput.value = `₱${dailyRate.toFixed(2)}`;
        
        // Update instrument options
        instrumentNameSelect.innerHTML = '<option value="">Select Specific Instrument</option>';
        if (selectedType === 'full_package') {
          instrumentNameSelect.disabled = true;
          instrumentNameSelect.innerHTML = '<option value="">Full Package selected</option>';
        } else {
          instrumentNameSelect.disabled = !selectedType;
        }
        
        if (selectedType && availableInstruments[selectedType]) {
          Object.entries(availableInstruments[selectedType]).forEach(([key, name]) => {
            const option = document.createElement('option');
            option.value = key;
            option.textContent = name;
            instrumentNameSelect.appendChild(option);
          });
        }
        
        updatePriceSummary();

        // Enable dependent fields only after choosing instrument type
        const enableIds = [
          'rental_end_date',
          'transportation_select',
          'pickup_time',
          'delivery_time',
          'return_location',
          'delivery_location',
          'pickup_from_event'
        ];
        enableIds.forEach(id => {
          const el = document.getElementById(id);
          if (el) el.disabled = !selectedType;
        });
      });

      // Update rate when specific instrument changes
      instrumentNameSelect.addEventListener('change', function() {
        const selectedInstrument = this.value;
        const instrumentRate = instrumentRates[selectedInstrument];
        const fallbackRate = dailyRates[instrumentTypeSelect.value] || 0;
        const finalRate = instrumentRate != null ? instrumentRate : fallbackRate;
        dailyRateInput.value = `₱${finalRate.toFixed(2)}`;
        updatePriceSummary();
      });

      // Calculate duration for single-day rentals
      function calculateDuration() {
        if (startDateInput.value) {
          endDateInput.value = startDateInput.value;
          durationInput.value = '1 day';
        } else {
          durationInput.value = '0 days';
        }
        updatePriceSummary();
      }

      // Initialize UI for single-day rentals
      function initializeSingleDayUI() {
        if (startDateGroup) startDateGroup.style.display = 'block';
        if (endDateGroup) endDateGroup.style.display = 'none';
        if (eventDurationGroup) eventDurationGroup.style.display = 'block';
        calculateDuration();
        updatePriceSummary();
      }
      initializeSingleDayUI();
             startDateInput.addEventListener('change', calculateDuration);
       endDateInput.addEventListener('change', calculateDuration);

       // Add event listeners for transportation only (Full Package now handled via instrument type)
       // Transportation method UI toggling
       function toggleTransportationUI() {
         const method = transportationSelect ? transportationSelect.value : 'none';
         if (method === 'delivery') {
           // Show only delivery location; hide pickup/return and related fields
           if (deliveryLocationGroup) deliveryLocationGroup.style.display = 'block';
           if (pickupLocationGroup) pickupLocationGroup.style.display = 'none';
           if (returnLocationGroup) returnLocationGroup.style.display = 'none';
           if (pickupFromEventGroup) pickupFromEventGroup.style.display = 'none';
           if (pickupTimeGroup) pickupTimeGroup.style.display = 'none';
           if (deliveryTimeGroup) deliveryTimeGroup.style.display = 'block';
           if (pickupLocationSelect) pickupLocationSelect.disabled = true; // fixed Studio
           if (returnLocationSelect) returnLocationSelect.disabled = true; // hidden for delivery

            // Reset label when not using self pickup
            const pickupLabel = pickupLocationGroup ? pickupLocationGroup.querySelector('label') : null;
            if (pickupLabel) pickupLabel.textContent = 'Pickup Location:';
           // Populate delivery time slots and apply constraints
           if (deliveryTimeInput) {
             populateTimeSelect(deliveryTimeInput);
           }
           refreshTimeSlotConstraints();
         } else {
           // Self pickup: show pickup/return and pick-up time; hide delivery-specific fields
           if (deliveryLocationGroup) deliveryLocationGroup.style.display = 'none';
           if (pickupLocationGroup) pickupLocationGroup.style.display = 'block';
           if (returnLocationGroup) returnLocationGroup.style.display = 'none';
           if (pickupFromEventGroup) pickupFromEventGroup.style.display = 'none';
           if (pickupTimeGroup) pickupTimeGroup.style.display = 'block';
           if (deliveryTimeGroup) deliveryTimeGroup.style.display = 'none';
           // Populate pickup time slots and apply constraints
           if (pickupTimeInput) {
             populateTimeSelect(pickupTimeInput);
           }
           refreshTimeSlotConstraints();
           // Lock both pickup and return to Studio and make unchangeable
           if (pickupLocationSelect) {
             pickupLocationSelect.value = 'Studio';
             pickupLocationSelect.disabled = true;
           }
           if (returnLocationSelect) {
             returnLocationSelect.value = 'Studio';
             returnLocationSelect.disabled = true; // lock to Studio for self pickup
           }

           // Update label to reflect unified location
           const pickupLabel = pickupLocationGroup ? pickupLocationGroup.querySelector('label') : null;
           if (pickupLabel) pickupLabel.textContent = 'Pickup & Return Location:';
         }
         updatePriceSummary();
       }

       if (transportationSelect) {
         transportationSelect.addEventListener('change', toggleTransportationUI);
         toggleTransportationUI();
       }

       // Enforce 8:00 AM minimum for self pickup via input listeners
       if (pickupTimeInput) {
         ['input', 'change', 'blur'].forEach(evt => {
           pickupTimeInput.addEventListener(evt, function() {
             // Only enforce when self pickup is selected
             if (transportationSelect && transportationSelect.value !== 'delivery') {
               if (pickupTimeInput.value && pickupTimeInput.value < '08:00') {
                 pickupTimeInput.value = '08:00';
               }
             }
           });
         });
       }

       // Enforce 8:00 AM minimum for delivery via input listeners
       if (deliveryTimeInput) {
         ['input', 'change', 'blur'].forEach(evt => {
           deliveryTimeInput.addEventListener(evt, function() {
             if (transportationSelect && transportationSelect.value === 'delivery') {
               if (deliveryTimeInput.value && deliveryTimeInput.value < '08:00') {
                 deliveryTimeInput.value = '08:00';
               }
             }
           });
         });
       }
       
       // Event duration affects price (single-day only now)
       if (eventDurationInput) {
         eventDurationInput.addEventListener('input', function() {
           updatePriceSummary();
         });
       }

             // Update price summary
       function updatePriceSummary() {
         const selectedType = instrumentTypeSelect.value;
         const fullPackage = selectedType === 'full_package';
         const transportation = transportationSelect.value;
         const rentType = 'single';
         const eventDurationHours = parseInt(eventDurationInput.value) || 7;
         
         let dailyRate = 0;
         let duration = parseInt(durationInput.value) || 0;
         const selectedInstrument = instrumentNameSelect ? instrumentNameSelect.value : '';
         const instrumentRate = selectedInstrument ? instrumentRates[selectedInstrument] : null;
         dailyRate = fullPackage ? 4500 : (instrumentRate != null ? instrumentRate : (dailyRates[selectedType] || 0));
         
         const subtotal = dailyRate * duration;
         const transportationFee = transportation === 'delivery' ? 550 : 0; // Average of ₱500-₱600
         const reservationFee = fullPackage ? 500 : 300; // ₱500 for full package, ₱300 for individual items
         
         // Calculate extra hour charges (₱200 per exceeding hour after 7 hours)
         const maxIncludedHours = 7;
         const extraHours = rentType === 'single' ? Math.max(0, eventDurationHours - maxIncludedHours) : 0;
         const extraHourCharges = extraHours * 200;
         
         const total = subtotal + transportationFee + reservationFee + extraHourCharges;
         
         document.getElementById('summary_daily_rate').textContent = `₱${dailyRate.toFixed(2)}`;
         document.getElementById('summary_duration').textContent = `${duration} day${duration !== 1 ? 's' : ''}`;
         document.getElementById('summary_subtotal').textContent = `₱${subtotal.toFixed(2)}`;
         document.getElementById('transportation_fee').textContent = `₱${transportationFee.toFixed(2)}`;
         const reservationFeeEl = document.getElementById('reservation_fee');
         if (reservationFeeEl) reservationFeeEl.textContent = `₱${reservationFee.toFixed(2)}`;
         
         // Reservation fee is already updated via #reservation_fee; avoid overwriting other rows
         
         document.getElementById('summary_total').textContent = `₱${total.toFixed(2)}`;
         
         // Update the event duration note to show extra charges if applicable
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
        // Require instrument type first
        if (!instrumentTypeSelect.value) {
          alert('Please select instrument type first.');
          return false;
        }
        const rentType = 'single';
        // Check required dates based on rent type
        if (!startDateInput.value) {
          alert('Please select a date for single-day rental.');
          return false;
        }
        endDateInput.value = startDateInput.value;
        durationInput.value = '1 day';

        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (startDate < today) {
          alert('Start date cannot be in the past.');
          return false;
        }
        
        if (rentType !== 'single' && endDate <= startDate) {
          alert('End date must be after start date.');
          return false;
        }
        
        const fullPackage = instrumentTypeSelect.value === 'full_package';
        
        if (!fullPackage && (!instrumentTypeSelect.value || !instrumentNameSelect.value)) {
          alert('Please select both instrument type and specific instrument.');
          return false;
        }

        // Transportation-specific validation
        if (transportationSelect.value === 'delivery') {
          const deliveryLocation = document.getElementById('delivery_location').value.trim();
          if (!deliveryLocation) {
            alert('Please enter a delivery location.');
            return false;
          }
          const deliveryTimeInputLocal = document.getElementById('delivery_time');
          if (!deliveryTimeInputLocal || !deliveryTimeInputLocal.value) {
            alert('Please select a delivery time.');
            return false;
          }
          // Guard against early times before 8:00 AM
          if (deliveryTimeInputLocal.value && deliveryTimeInputLocal.value < '08:00') {
            alert('Delivery time must be at or after 8:00 AM.');
            return false;
          }
        } else {
          if (pickupTimeInput && !pickupTimeInput.value) {
            alert('Please select a pick-up time for self pickup.');
            return false;
          }
          // Guard against early times before 8:00 AM
          if (pickupTimeInput && pickupTimeInput.value && pickupTimeInput.value < '08:00') {
            alert('Pick-up time must be at or after 8:00 AM.');
            return false;
          }
        }

        // For single-day rentals, ensure event duration fits before closing time (8:00 PM)
        if (rentType === 'single' && transportationSelect.value !== 'delivery') {
          if (!eventDurationInput || !eventDurationInput.value) {
            alert('Please enter event duration in hours.');
            return false;
          }
          // Block past-time selection when renting today
          const todayStr = getLocalDateString(new Date());
          if (startDateInput.value === todayStr) {
            const now = new Date();
            const nowHH = String(now.getHours()).padStart(2, '0');
            const nowMM = String(now.getMinutes()).padStart(2, '0');
            const nowHHMM = `${nowHH}:${nowMM}`;
            const effectiveMin = pickupTimeInput.min || '08:00';
            if (pickupTimeInput.value < effectiveMin || pickupTimeInput.value < nowHHMM) {
              alert('Pick-up time cannot be in the past for a same-day rental.');
              return false;
            }
          }
          const pickupMin = timeToMinutes(pickupTimeInput.value);
          const closingMin = timeToMinutes(STUDIO_CLOSING_TIME);
          if (pickupMin == null || closingMin == null) {
            alert('Invalid time values. Please check your pick-up time.');
            return false;
          }
          // Ensure start date respects dynamic minimum (e.g., today disabled after closing)
          if (startDateInput && startDateInput.min && startDateInput.value < startDateInput.min) {
            alert('Start date is no longer available today. Please choose a later date.');
            return false;
          }
          const remainingMin = closingMin - pickupMin;
          const requiredMin = parseInt(eventDurationInput.value, 10) * 60;
          if (requiredMin > remainingMin) {
            const allowedHours = Math.max(0, Math.floor(remainingMin / 60));
            alert(`With a ${eventDurationInput.value} hour event starting at ${pickupTimeInput.value}, return exceeds 8:00 PM. Reduce duration to ${allowedHours} hour(s) or choose an earlier pickup.`);
            return false;
          }

          // Also block pickup times that start after the latest allowable start based on duration
          const maxPickupMin = Math.max(0, closingMin - requiredMin);
          if (pickupMin > maxPickupMin) {
            alert('Pick-up time is too late for the chosen duration. Choose an earlier time.');
            return false;
          }
          // And never allow pickup beyond closing time
          if (pickupTimeInput.value > STUDIO_CLOSING_TIME) {
            alert('Pick-up time cannot be past 8:00 PM.');
            return false;
          }
        }

        // For single-day delivery, ensure event duration fits before closing (8:00 PM)
        if (rentType === 'single' && transportationSelect.value === 'delivery') {
          if (!eventDurationInput || !eventDurationInput.value) {
            alert('Please enter event duration in hours.');
            return false;
          }
          const deliveryTimeInputLocal = document.getElementById('delivery_time');
          if (!deliveryTimeInputLocal || !deliveryTimeInputLocal.value) {
            alert('Please select a delivery time.');
            return false;
          }
          // Block past-time selection when renting today
          const todayStr = getLocalDateString(new Date());
          if (startDateInput.value === todayStr) {
            const now = new Date();
            const nowHH = String(now.getHours()).padStart(2, '0');
            const nowMM = String(now.getMinutes()).padStart(2, '0');
            const nowHHMM = `${nowHH}:${nowMM}`;
            const effectiveMin = deliveryTimeInputLocal.min || '08:00';
            if (deliveryTimeInputLocal.value < effectiveMin || deliveryTimeInputLocal.value < nowHHMM) {
              alert('Delivery time cannot be in the past for a same-day rental.');
              return false;
            }
          }
          const startMin = timeToMinutes(deliveryTimeInputLocal.value);
          const closingMin = timeToMinutes(STUDIO_CLOSING_TIME);
          if (startMin == null || closingMin == null) {
            alert('Invalid time values. Please check your delivery time.');
            return false;
          }
          // Ensure start date respects dynamic minimum (e.g., today disabled after closing)
          if (startDateInput && startDateInput.min && startDateInput.value < startDateInput.min) {
            alert('Start date is no longer available today. Please choose a later date.');
            return false;
          }
          const remainingMin = closingMin - startMin;
          const requiredMin = parseInt(eventDurationInput.value, 10) * 60;
          if (requiredMin > remainingMin) {
            const allowedHours = Math.max(0, Math.floor(remainingMin / 60));
            alert(`With a ${eventDurationInput.value} hour event starting at ${deliveryTimeInputLocal.value}, return exceeds 8:00 PM. Reduce duration to ${allowedHours} hour(s) or choose an earlier delivery time.`);
            return false;
          }

          // Also block delivery times that start after the latest allowable start based on duration
          const maxStartMin = Math.max(0, closingMin - requiredMin);
          if (startMin > maxStartMin) {
            alert('Delivery time is too late for the chosen duration. Choose an earlier time.');
            return false;
          }
          // And never allow delivery beyond closing time
          if (deliveryTimeInputLocal.value > STUDIO_CLOSING_TIME) {
            alert('Delivery time cannot be past 8:00 PM.');
            return false;
          }
        }
        
        return true;
      }
      
      // Populate modal with form data
      function populateModal() {
        const selectedType = instrumentTypeSelect.value;
        const selectedInstrument = instrumentNameSelect.value;
        const rentType = 'single';
        const eventDurationHours = (parseInt(document.querySelector('input[name="event_duration_hours"]').value) || 7);
        const transportation = document.getElementById('transportation_select').value;
        const pickupLocation = document.querySelector('select[name="pickup_location"]').value;
        const returnLocation = document.querySelector('select[name="return_location"]').value;
        const deliveryTimeVal = (document.getElementById('delivery_time') && document.getElementById('delivery_time').value) || '';
        
        // Get instrument names
        const instrumentTypes = @json($instrumentTypes ?? []);
        const availableInstruments = @json($availableInstruments ?? []);
        
        // Calculate prices
        const fullPackage = instrumentTypeSelect.value === 'full_package';
        let dailyRate = 0;
        let duration = parseInt(durationInput.value) || 0;
        const selectedInstrumentName = selectedInstrument;
        const instrumentRateModal = selectedInstrumentName ? instrumentRates[selectedInstrumentName] : null;
        dailyRate = fullPackage ? 4500 : (instrumentRateModal != null ? instrumentRateModal : (dailyRates[selectedType] || 0));
        
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
        // Show only the instrument type label (no brand name)
        const instrumentDisplay = fullPackage
          ? 'Full Package'
          : (instrumentTypes[selectedType] || 'Instrument');
        document.getElementById('modalInstrumentSummary').textContent = instrumentDisplay;
        
        const rentalPeriod = `${formatDate(startDate)} - ${formatDate(endDate)} (${duration} days)`;
        document.getElementById('modalRentalPeriod').textContent = rentalPeriod;
        
        document.getElementById('modalEventDuration').textContent = rentType === 'single' ? `${eventDurationHours} hours` : '-';

        const deliveryLocationVal = document.getElementById('delivery_location').value || '';
        const pickupFromEvent = document.getElementById('pickup_from_event').checked;
        const pickupTimeVal = (document.getElementById('pickup_time') && document.getElementById('pickup_time').value) || '';
        // Cleaner wording and shorter location text for delivery/self pickup
        const cleanLocation = (s) => (s || '').split('(')[0].trim();
        const pickupShort = cleanLocation(pickupLocation);
        const deliveryShort = cleanLocation(deliveryLocationVal);
        const pickupTimePart = (pickupTimeVal && pickupTimeVal !== '0') ? ' (pickup time: ' + pickupTimeVal + ')' : '';
        const deliveryTimePart = deliveryTimeVal ? ' (delivery time: ' + deliveryTimeVal + ')' : '';
        const deliveryInfo = transportation === 'delivery'
          ? `Delivery to ${deliveryShort || 'specified location'}${deliveryTimePart}; Pickup from event: ${pickupFromEvent ? 'Yes' : 'No'}`
          : `Self pickup at ${pickupShort || 'Studio'}${pickupTimePart}`;
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
        const venueType = 'indoor';
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
        document.getElementById('modalEventDurationInput').value = rentType === 'single' ? eventDurationHours : 0;
        document.getElementById('modalPickupLocationInput').value = pickupLocation;
        document.getElementById('modalPickupTimeInput').value = pickupTimeVal;
        const modalDeliveryTimeInput = document.getElementById('modalDeliveryTimeInput');
        if (modalDeliveryTimeInput) modalDeliveryTimeInput.value = deliveryTimeVal;
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

      // Set minimum end date based on start date (single-day default)
      startDateInput.addEventListener('change', function() {
        if (!this.value) return;
        endDateInput.value = this.value;
        endDateInput.min = this.value;
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
      if (dropdown) dropdown.classList.toggle('show');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      const userProfile = document.getElementById('userProfile');
      const dropdown = document.getElementById('userDropdown');
      
      if (userProfile && dropdown && !userProfile.contains(event.target)) {
        dropdown.classList.remove('show');
      }
    });

    // Instrument Rental Calendar scripts removed from this page
  </script>
</body>
</html>