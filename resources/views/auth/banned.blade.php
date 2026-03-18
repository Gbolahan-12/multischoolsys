<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Suspended</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{asset('admin-assets/images/logo/multischoollogo.jpeg')}}" />

    <style>
        body { background: #fff5f5; min-height: 100vh; display: flex; align-items: center; }
        .wall-card {
            max-width: 500px; width: 100%; margin: auto;
            border-radius: 20px; border: none;
            box-shadow: 0 8px 40px rgba(220,53,69,.12);
        }
        .icon-ring {
            width: 80px; height: 80px; border-radius: 50%;
            background: linear-gradient(135deg, #f8d7da, #f5c2c7);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.2rem; color: #842029;
        }
        .wa-btn {
            background: #25d366; border: none; color: #fff;
            padding: 12px 28px; border-radius: 50px;
            font-weight: 600; font-size: 15px;
            text-decoration: none; display: inline-flex;
            align-items: center; gap: 8px;
            transition: background .2s;
        }
        .wa-btn:hover { background: #1ebe5d; color: #fff; }
    </style>
</head>
<body>
<div class="container px-3">
    <div class="card wall-card">
        <div class="card-body p-5 text-center">

            <div class="icon-ring">
                <i class="bi bi-slash-circle"></i>
            </div>

            <h4 class="fw-bold mb-1 text-danger">School Suspended</h4>
            <p class="text-muted mb-3" style="font-size:15px;">
                <strong>{{ auth()->user()->school->name }}</strong> has been suspended
                and access to all features has been disabled.
            </p>

            @if(auth()->user()->school->ban_reason)
            <div class="alert alert-danger text-start rounded-3" style="font-size:14px;">
                <strong><i class="bi bi-info-circle me-1"></i>Reason:</strong>
                {{ auth()->user()->school->ban_reason }}
            </div>
            @endif

            <p class="text-muted mb-4" style="font-size:14px;">
                If you believe this is a mistake or you'd like to renew your subscription,
                please contact the super-admin on WhatsApp.
            </p>

            <a href="https://wa.me/2349155242973?text=Hello%2C%20my%20school%20{{ urlencode(auth()->user()->school->name) }}%20has%20been%20suspended.%20I'd%20like%20to%20resolve%20this."
               target="_blank" class="wa-btn mb-3">
                <i class="bi bi-whatsapp"></i> Contact Super-Admin
            </a>

            <br>

            <form action="{{ route('logout') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-link text-muted" style="font-size:13px;">
                    <i class="bi bi-box-arrow-left me-1"></i> Sign out
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>