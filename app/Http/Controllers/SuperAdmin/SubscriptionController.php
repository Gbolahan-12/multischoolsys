<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function __construct(private SubscriptionService $service) {}

    /**
     * List all subscriptions across all schools
     */
    public function index(Request $request)
    {
        $subscriptions = Subscription::with(['school', 'recordedBy'])
            ->when($request->school_id, fn($q) => $q->where('school_id', $request->school_id))
            ->when($request->search, fn($q) => $q->whereHas('school', fn($sq) =>
                $sq->where('name', 'like', "%{$request->search}%")
            ))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Schools expiring soon (within 7 days) or in warning
        $expiringSoon = School::where('status', 'active')
            ->whereNotNull('subscription_expires_at')
            ->where('subscription_expires_at', '<=', now()->addDays(7))
            ->where('subscription_expires_at', '>=', now())
            ->orderBy('subscription_expires_at')
            ->get();

        $inWarning = School::where('subscription_status', 'warning')->get();

        $schools = School::orderBy('name')->get(['id', 'name']);

        return view('dashboards.super-admin.subscriptions.index', compact(
            'subscriptions', 'expiringSoon', 'inWarning', 'schools'
        ));
    }

    /**
     * Show form to record a new subscription payment
     */
    public function create(Request $request)
    {
        $schools        = School::orderBy('name')->get();
        $selectedSchool = $request->school_id
            ? School::with(['subscriptions' => fn($q) => $q->latest()->limit(5)])->find($request->school_id)
            : null;

        return view('dashboards.super-admin.subscriptions.create', compact('schools', 'selectedSchool'));
    }

    /**
     * Store a new subscription payment
     */
    public function store(Request $request)
    {
        $request->validate([
            'school_id'      => 'required|exists:schools,id',
            'amount'         => 'required|numeric|min:1',
            'payment_date'   => 'required|date',
            'payment_method' => 'required|in:cash,transfer,pos,cheque',
            'duration_months'=> 'required|in:1,3,6,12',
            'reference'      => 'nullable|string|max:100',
            'note'           => 'nullable|string|max:500',
        ]);

        $school = School::findOrFail($request->school_id);

        $subscription = $this->service->record($school, $request->all(), Auth::user());

        return redirect()->route('superadmin.subscriptions.index')
            ->with('success', "✅ Subscription recorded for {$school->name}. Expires: {$subscription->expires_at->format('d M Y')}.");
    }

    /**
     * Show subscription history for a specific school
     */
    public function show(School $school)
    {
        $subscriptions = Subscription::with('recordedBy')
            ->where('school_id', $school->id)
            ->latest()
            ->get();

        $latest = $subscriptions->first();

        return view('dashboards.super-admin.subscriptions.show', compact('school', 'subscriptions', 'latest'));
    }

    /**
     * AJAX — get school subscription info when school is selected
     */
    public function schoolInfo(Request $request)
    {
        $school = School::find($request->school_id);
        if (!$school) return response()->json(null);

        $latest = Subscription::where('school_id', $school->id)->latest()->first();

        return response()->json([
            'name'                    => $school->name,
            'status'                  => $school->status,
            'subscription_status'     => $school->subscription_status,
            'subscription_expires_at' => $school->subscription_expires_at?->format('d M Y'),
            'is_expired'              => $school->subscription_expires_at
                                         ? now()->isAfter($school->subscription_expires_at)
                                         : true,
            'latest_duration'         => $latest?->duration_months,
            'latest_amount'           => $latest?->amount,
        ]);
    }
}