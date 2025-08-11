<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Music Studio') - Admin Panel</title>
    
    <!-- Base styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .navbar {
            background: #2c3e50;
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            color: white;
        }
        
        .navbar-nav {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }
        
        .navbar-nav a {
            color: white;
            text-decoration: none;
            transition: opacity 0.3s;
        }
        
        .navbar-nav a:hover {
            opacity: 0.8;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #34495e;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .admin-badge {
            background: #e74c3c;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .main-content {
            min-height: calc(100vh - 70px);
            padding: 2rem 0;
        }
        
        .alert {
            padding: 12px 20px;
            border-radius: 6px;
            margin: 20px auto;
            max-width: 800px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .logout-form {
            display: inline;
        }
        
        .logout-btn {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: inherit;
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .navbar-content {
                flex-direction: column;
                gap: 1rem;
            }
            
            .navbar-nav {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-content">
            <a href="{{ route('admin.dashboard') }}" class="navbar-brand">
                🎵 Music Studio Admin
            </a>
            
            @auth
            <ul class="navbar-nav">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.bookings') }}">Bookings</a></li>
                <li><a href="{{ route('admin.calendar') }}">Calendar</a></li>
                <li><a href="{{ route('home') }}">Main Site</a></li>
                
                <li class="user-info">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div>{{ Auth::user()->name }}</div>
                        @if(Auth::user()->isAdmin())
                            <span class="admin-badge">Admin</span>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button type="submit" class="logout-btn">Logout</button>
                    </form>
                </li>
            </ul>
            @endauth
        </div>
    </nav>

    <!-- Alerts -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Scripts -->
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s';
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>

    <!-- Elfsight AI Chatbot | Untitled AI Chatbot -->
     <script src="https://static.elfsight.com/platform/platform.js" async></script>
     <div class="elfsight-app-96cc4395-da06-450f-9191-5bc6e30fa5f7" data-elfsight-app-lazy id="draggable-chatbot"></div>
     

     


     <!-- Draggable Chatbot Circle Styles -->
     <style>
       #draggable-chatbot {
         position: fixed !important;
         z-index: 9999 !important;
         cursor: move;
         transition: all 0.2s ease;
       }

       /* Make the chatbot circle draggable when minimized/closed */
       #draggable-chatbot .eapps-widget-toolbar,
       #draggable-chatbot [class*="toolbar"],
       #draggable-chatbot [class*="trigger"],
       #draggable-chatbot [class*="button"] {
         cursor: move !important;
       }

       /* Hover effect for the draggable circle */
       #draggable-chatbot:hover {
         transform: scale(1.05);
         box-shadow: 0 4px 15px rgba(0,0,0,0.2);
       }

       /* Ensure the chatbot stays within viewport */
       #draggable-chatbot {
         max-width: 100vw;
         max-height: 100vh;
       }
     </style>

      <!-- Draggable Chatbot Circle Functionality -->
      <script>
        let isDragging = false;
        let currentX;
        let currentY;
        let initialX;
        let initialY;
        let xOffset = 0;
        let yOffset = 0;
        let chatbotElement;

        // Wait for chatbot to load and initialize dragging
        function initializeDraggableChatbot() {
          chatbotElement = document.getElementById('draggable-chatbot');
          
          if (!chatbotElement) {
            setTimeout(initializeDraggableChatbot, 1000);
            return;
          }

          // Add drag functionality to the chatbot circle
          chatbotElement.addEventListener('mousedown', dragStart);
          chatbotElement.addEventListener('touchstart', dragStart, { passive: false });
          
          document.addEventListener('mousemove', drag);
          document.addEventListener('touchmove', drag, { passive: false });
          
          document.addEventListener('mouseup', dragEnd);
          document.addEventListener('touchend', dragEnd);

          console.log('Draggable chatbot circle initialized!');
        }

        function dragStart(e) {
          // Only allow dragging when clicking on the chatbot circle (not when chat is open)
          const chatWindow = chatbotElement.querySelector('[class*="chat"], [class*="window"], [class*="content"]');
          if (chatWindow && chatWindow.offsetHeight > 100) {
            // Chat is open, don't drag
            return;
          }

          if (e.type === 'touchstart') {
            initialX = e.touches[0].clientX - xOffset;
            initialY = e.touches[0].clientY - yOffset;
          } else {
            initialX = e.clientX - xOffset;
            initialY = e.clientY - yOffset;
          }

          isDragging = true;
          chatbotElement.style.cursor = 'grabbing';
          e.preventDefault();
        }

        function drag(e) {
          if (isDragging) {
            e.preventDefault();
            
            if (e.type === 'touchmove') {
              currentX = e.touches[0].clientX - initialX;
              currentY = e.touches[0].clientY - initialY;
            } else {
              currentX = e.clientX - initialX;
              currentY = e.clientY - initialY;
            }

            xOffset = currentX;
            yOffset = currentY;

            // Keep chatbot within viewport bounds
            const rect = chatbotElement.getBoundingClientRect();
            const maxX = window.innerWidth - rect.width;
            const maxY = window.innerHeight - rect.height;
            
            currentX = Math.max(0, Math.min(currentX, maxX));
            currentY = Math.max(0, Math.min(currentY, maxY));

            setTranslate(currentX, currentY, chatbotElement);
          }
        }

        function dragEnd(e) {
          if (isDragging) {
            initialX = currentX;
            initialY = currentY;
            isDragging = false;
            chatbotElement.style.cursor = 'move';
            
            // Snap to edges if close (within 30px)
            const rect = chatbotElement.getBoundingClientRect();
            const snapDistance = 30;
            
            if (rect.left < snapDistance) {
              currentX = 0;
              xOffset = 0;
            }
            if (rect.right > window.innerWidth - snapDistance) {
              currentX = window.innerWidth - rect.width;
              xOffset = currentX;
            }
            if (rect.top < snapDistance) {
              currentY = 0;
              yOffset = 0;
            }
            if (rect.bottom > window.innerHeight - snapDistance) {
              currentY = window.innerHeight - rect.height;
              yOffset = currentY;
            }
            
            setTranslate(currentX, currentY, chatbotElement);
          }
        }

        function setTranslate(xPos, yPos, el) {
          el.style.transform = `translate3d(${xPos}px, ${yPos}px, 0)`;
        }

        // Handle window resize
        window.addEventListener('resize', function() {
          if (chatbotElement) {
            const rect = chatbotElement.getBoundingClientRect();
            
            // Ensure chatbot stays within new viewport
            if (rect.right > window.innerWidth) {
              currentX = window.innerWidth - rect.width;
              xOffset = currentX;
            }
            if (rect.bottom > window.innerHeight) {
              currentY = window.innerHeight - rect.height;
              yOffset = currentY;
            }
            
            setTranslate(currentX, currentY, chatbotElement);
          }
        });

        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', initializeDraggableChatbot);
        } else {
          initializeDraggableChatbot();
        }

        // Also try to initialize after a delay to ensure Elfsight widget is loaded
        setTimeout(initializeDraggableChatbot, 2000);
      </script>
     


</body>
</html>