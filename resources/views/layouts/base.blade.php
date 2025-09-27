<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Arsip')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        /* ANCHOR: Custom Toastify Styles using app.css variables */
        .toastify-success {
            background: var(--success-color) !important;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3) !important;
        }
        
        .toastify-error {
            background: var(--danger-color) !important;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3) !important;
        }
        
        .toastify-warning {
            background: var(--warning-color) !important;
            color: #000 !important;
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3) !important;
        }
        
        .toastify-info {
            background: var(--info-color) !important;
            box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3) !important;
        }
        
        .toastify {
            border-radius: var(--border-radius) !important;
            font-family: 'Poppins', sans-serif !important;
            font-weight: 500 !important;
            padding: 16px 20px !important;
            min-width: 300px !important;
            max-width: 400px !important;
            box-shadow: var(--box-shadow) !important;
            transition: var(--transition) !important;
            color: #fff !important;
        }
        
        .toastify .toastify-content {
            display: flex !important;
            align-items: center !important;
        }
        
        /* Dark mode support using existing dark mode variables */
        body.dark-mode .toastify {
            background: rgba(45, 45, 45, 0.95) !important;
            color: #e0e0e0 !important;
            border: 1px solid #404040 !important;
        }
        
        body.dark-mode .toastify-success {
            background: var(--success-color) !important;
        }
        
        body.dark-mode .toastify-error {
            background: var(--danger-color) !important;
        }
        
        body.dark-mode .toastify-warning {
            background: var(--warning-color) !important;
            color: #000 !important;
        }
        
        body.dark-mode .toastify-info {
            background: var(--info-color) !important;
        }
    </style>
    @stack('head')
  </head>
  <body>
    @yield('content')
    <script src="https://cdn.jsdelivr.net/npm/toastify-js@1.12.0/src/toastify.min.js"></script>
    <script src="{{ asset('js/utils.js') }}"></script>
    @stack('scripts')
  </body>
</html> 