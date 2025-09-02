<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ICMS - Industrial Condition Monitoring System') }}</title>



    <!-- Core CSS -->
    @include('layouts.style.style')

    <!-- Custom CSS for login page -->
    <style>
        body {
            background-image: url("{{ asset('assets/img/backgroundbogasari.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 100vh;
        }

        .auth-wrapper {
            min-height: 100vh;
            position: relative;
        }

        .auth-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1;
        }

        .auth-container {
            position: relative;
            z-index: 2;
            max-width: 1200px;
            width: 90%;
        }

        .form-control {
            border-radius: 8px !important;
            background-color: rgba(255, 255, 255, 0.95);
            border: 1px solid #dbdade !important;
        }

        .auth-card {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            border-radius: 1rem;
            overflow: hidden;
        }

        .system-info-column {
            background-color: rgba(0, 47, 108, 0.85);
            color: white;
            padding: 3rem 2rem;
            position: relative;
        }


        .login-column {
            padding: 3rem 2rem;
            background-color: rgba(255, 255, 255, 0.95);
        }

        .system-logo {
            display: flex;
            align-items: center;
            margin-bottom: 2.5rem;
        }

        .system-logo img {
            margin-right: 1rem;
        }

        .system-heading {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #fff;
        }

        .system-feature {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .system-feature i {
            font-size: 1.25rem;
            margin-right: 1rem;
            color: #3dd5f3;
        }

        .system-feature-content h4 {
            font-size: 1rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #fff;
        }

        .system-feature-content p {
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 0;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-header h2 {
            font-size: 1.75rem;
            font-weight: 600;
            color: #002F6C;
            margin-bottom: 0.75rem;
        }

        .login-header p {
            color: #6c757d;
        }

        .login-form-container {
            max-width: 400px;
            margin: 0 auto;
        }

        .industrial-icon {
            color: #002F6C;
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .login-divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
        }

        .login-divider::before,
        .login-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background-color: #e0e0e0;
        }

        .login-divider span {
            padding: 0 1rem;
            color: #6c757d;
            font-size: 0.875rem;
        }

        @media (max-width: 992px) {
            .system-info-column {
                padding: 2rem 1.5rem;
            }

            .login-column {
                padding: 2rem 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .system-info-column {
                display: none;
            }
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="auth-wrapper d-flex justify-content-center align-items-center">
        <div class="auth-overlay"></div>
        <div class="auth-container">
            <div class="auth-card">
                <div class="row g-0">
                    <!-- Left column with system info -->
                    <div class="col-md-6 system-info-column">
                        <div class="system-logo">
                            <img src="{{ asset('assets/is.png') }}" alt="Bogasari" width="50" height="35">
                            <h1 class="mb-0 fs-3 text-white fw-bold">ICMS</h1>
                        </div>

                        <h2 class="system-heading">Industrial Condition Monitoring System</h2>
                        <p class="mb-4 text-light-emphasis">Advanced industrial monitoring & control system to optimize
                            systems in your facility</p>

                        <div class="system-feature">
                            <i class="ti ti-chart-line"></i>
                            <div class="system-feature-content">
                                <h4>Real-Time Monitoring</h4>
                                <p>Monitor systems across all equipment in real-time with detailed
                                    analytics and dashboards</p>
                            </div>
                        </div>

                        <div class="system-feature">
                            <i class="ti ti-alert-triangle"></i>
                            <div class="system-feature-content">
                                <h4>Alert Management</h4>
                                <p>Receive instant notifications for anomalies and prevent potential issues before they
                                    become critical</p>
                            </div>
                        </div>



                        <div class="system-feature">
                            <i class="ti ti-device-analytics"></i>
                            <div class="system-feature-content">
                                <h4>Cross-Asset Analysis</h4>
                                <p>Compare performance across multiple assets to identify inefficiencies and
                                    optimize
                                    power distribution</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right column with login form -->
                    <div class="col-md-6 login-column">
                        <div class="login-header">
                            <i class="ti ti-building-factory industrial-icon d-block mx-auto"></i>
                            <h2>Welcome to ICMS</h2>
                            <p>Log in to access your control panel</p>
                        </div>
                        <div class="login-form-container">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>

    <!-- Password toggle script -->
    <script>
        $(document).ready(function() {
            // Toggle password visibility
            $('.input-group-text.cursor-pointer').on('click', function() {
                const $this = $(this);
                const $input = $this.siblings('input');
                const $icon = $this.find('i');

                if ($input.attr('type') === 'password') {
                    $input.attr('type', 'text');
                    $icon.removeClass('ti-eye-off').addClass('ti-eye');
                } else {
                    $input.attr('type', 'password');
                    $icon.removeClass('ti-eye').addClass('ti-eye-off');
                }
            });
        });
    </script>
</body>

</html>
