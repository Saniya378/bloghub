<!DOCTYPE html>
<html lang="en" data-theme="{{ session('theme', 'light') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Server Error — BlogHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dark-mode.css') }}">
    <style>
        .bh-error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--space-8) 0;
            background-color: var(--color-bg);
        }
        .bh-error-content {
            text-align: center;
            max-width: 540px;
            padding: 0 var(--space-4);
        }
        .bh-error-code {
            font-family: var(--font-heading);
            font-size: clamp(4rem, 10vw, 7rem);
            font-weight: 800;
            color: var(--color-primary);
            line-height: 1;
            margin-bottom: var(--space-2);
            letter-spacing: -0.04em;
        }
        .bh-error-title {
            font-family: var(--font-heading);
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--color-primary);
            margin-bottom: var(--space-3);
        }
        [data-theme="dark"] .bh-error-title {
            color: #F1F4FA;
        }
        .bh-error-text {
            font-family: var(--font-body);
            font-size: 1.0625rem;
            line-height: 1.65;
            color: var(--color-text-muted);
            margin-bottom: var(--space-6);
        }
    </style>
</head>
<body>
    <div class="bh-error-page">
        <div class="bh-error-content">
            <div class="mb-4">
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning-subtle text-warning" style="width: 80px; height: 80px; font-size: 32px;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </span>
            </div>
            <div class="bh-error-code">500</div>
            <h1 class="bh-error-title">Something Went Wrong</h1>
            <p class="bh-error-text">
                An internal server error occurred while processing your request. Our technical team has been notified and we are looking into it.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ url('/') }}" class="btn btn-bh btn-bh-primary">
                    <i class="fa-solid fa-house me-2"></i>Back to Home
                </a>
                <button type="button" onclick="window.location.reload()" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-rotate-right me-2"></i>Reload Page
                </button>
            </div>
        </div>
    </div>
</body>
</html>
