<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activation Pending</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{asset('admin-assets/images/logo/multischoollogo.jpeg')}}" />

    <style>
        body { background: #f0f4ff; min-height: 100vh; display: flex; align-items: center; }
        .wall-card {
            max-width: 520px; width: 100%; margin: auto;
            border-radius: 20px; border: none;
            box-shadow: 0 8px 40px rgba(13,110,253,.12);
        }
        .icon-ring {
            width: 80px; height: 80px; border-radius: 50%;
            background: linear-gradient(135deg, #fff3cd, #ffe69c);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.2rem; color: #856404;
        }
        .step-item {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 10px 0; border-bottom: 1px solid #f0f0f0;
        }
        .step-item:last-child { border-bottom: none; }
        .step-num {
            width: 26px; height: 26px; border-radius: 50%;
            background: #0d6efd; color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; flex-shrink: 0; margin-top: 1px;
        }
        .wa-btn {
            background: #25d366; border: none; color: #fff;
            padding: 12px 28px; border-radius: 50px;
            font-weight: 600; font-size: 15px;
            text-decoration: none; display: inline-flex;
            align-items: center; gap: 8px;
            transition: background .2s, transform .15s;
        }
        .wa-btn:hover { background: #1ebe5d; color: #fff; transform: translateY(-1px); }
    </style>
</head>
<body>
<div class="container px-3">
    <div class="card wall-card">
        <div class="card-body p-5 text-center">

            <div class="icon-ring">
                <i class="bi bi-hourglass-split"></i>
            </div>

            <h4 class="fw-bold mb-1">School Not Yet Activated</h4>
            <p class="text-muted mb-4" style="font-size:15px;">
                Your school <strong>{{ auth()->user()->school->name }}</strong> is registered
                but not yet active. To unlock all features, please complete your activation payment.
            </p>

            <div class="card bg-light border-0 rounded-3 p-3 mb-4 text-start">
                <p class="fw-semibold mb-3" style="font-size:13px; text-transform:uppercase; letter-spacing:.05em;">
                    How to activate your school
                </p>
                <div class="step-item">
                    <div class="step-num">1</div>
                    <div style="font-size:14px;">
                        <strong>Chat the Super-Admin on WhatsApp</strong><br>
                        <small class="text-muted">Click the button below to open WhatsApp directly.</small>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">2</div>
                    <div style="font-size:14px;">
                        <strong>Make your payment</strong><br>
                        <small class="text-muted">The super-admin will provide payment details.</small>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">3</div>
                    <div style="font-size:14px;">
                        <strong>Send proof of payment</strong><br>
                        <small class="text-muted">Once confirmed, your school will be activated within minutes.</small>
                    </div>
                </div>
            </div>

            {{-- Replace the number below with actual WhatsApp number --}}
            <a href="https://wa.me/2349155242973?text=Hello%2C%20I%20just%20registered%20{{ urlencode(auth()->user()->school->name) }}%20and%20I'd%20like%20to%20activate%20my%20school."
               target="_blank" class="wa-btn mb-3">
                <i class="bi bi-whatsapp"></i> Chat Super-Admin on WhatsApp
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