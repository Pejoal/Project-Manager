<?php

namespace App\Http\Controllers;

use App\Models\Payslip;
use App\Models\User;
use App\Models\TimeEntry;
use App\Models\TaxConfiguration;
use App\Models\PayrollSettings;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Events\ActivityLogged;

class PayslipController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth', 'role:admin']);
  }

  public function index(Request $request)
  {
    $query = Payslip::with(['user', 'generatedBy', 'approvedBy']);

    if ($request->filled('user_id')) {
      $query->where('user_id', $request->user_id);
    }

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    if ($request->filled('period_start')) {
      $query->whereDate('pay_period_start', '>=', $request->period_start);
    }

    if ($request->filled('period_end')) {
      $query->whereDate('pay_period_end', '<=', $request->period_end);
    }

    $payslips = $query->latest('pay_period_start')->paginate(15);

    $users = User::whereHas('employeeProfile')->select('id', 'name')->get();

    return Inertia::render('Payslips/Index', [
      'payslips' => $payslips,
      'filters' => $request->only(['user_id', 'status', 'period_start', 'period_end']),
      'users' => $users,
    ]);
  }

  public function generate(Request $request)
  {
    $request->validate([
      'user_ids' => 'required|array',
      'user_ids.*' => 'exists:users,id',
      'pay_period_start' => 'required|date',
      'pay_period_end' => 'required|date|after:pay_period_start',
      'pay_date' => 'required|date|after_or_equal:pay_period_end',
    ]);

    $users = User::with('employeeProfile')->whereIn('id', $request->user_ids)->get();
    $generatedCount = 0;

    foreach ($users as $user) {
      if (!$user->employeeProfile || !$user->employeeProfile->isActiveEmployee()) {
        continue;
      }

      // Check if payslip already exists for this period
      $existingPayslip = Payslip::where('user_id', $user->id)
        ->where('pay_period_start', $request->pay_period_start)
        ->where('pay_period_end', $request->pay_period_end)
        ->first();

      if ($existingPayslip) {
        continue;
      }

      $payslip = $this->generatePayslipForUser(
        $user,
        $request->pay_period_start,
        $request->pay_period_end,
        $request->pay_date
      );

      if ($payslip) {
        $generatedCount++;
      }
    }

    event(
      new ActivityLogged(
        auth()->user(),
        'generated_payslips',
        __('payroll.activity.payslips_generated', ['count' => $generatedCount]),
        null
      )
    );

    return redirect()
      ->route('payslips.index')
      ->with('flash.banner', __('payroll.payslips.generated_successfully', ['count' => $generatedCount]));
  }

  public function show(Payslip $payslip)
  {
    $payslip->load(['user.employeeProfile', 'generatedBy', 'approvedBy']);

    return Inertia::render('Payslips/Show', [
      'payslip' => $payslip,
    ]);
  }

  public function approve(Payslip $payslip)
  {
    if ($payslip->status !== 'draft') {
      return redirect()
        ->back()
        ->withErrors([
          'status' => __('payroll.payslips.cannot_approve_non_draft'),
        ]);
    }

    $payslip->update([
      'status' => 'approved',
      'approved_by' => auth()->id(),
      'approved_at' => now(),
    ]);

    event(
      new ActivityLogged(
        auth()->user(),
        'approved_payslip',
        __('payroll.activity.payslip_approved', ['user' => $payslip->user->name]),
        $payslip
      )
    );

    return redirect()->back()->with('flash.banner', __('payroll.payslips.approved_successfully'));
  }

  public function markAsPaid(Payslip $payslip)
  {
    if (!in_array($payslip->status, ['approved'])) {
      return redirect()
        ->back()
        ->withErrors([
          'status' => __('payroll.payslips.cannot_mark_paid_non_approved'),
        ]);
    }

    $payslip->update(['status' => 'paid']);

    event(
      new ActivityLogged(
        auth()->user(),
        'marked_payslip_paid',
        __('payroll.activity.payslip_marked_paid', ['user' => $payslip->user->name]),
        $payslip
      )
    );

    return redirect()->back()->with('flash.banner', __('payroll.payslips.marked_paid_successfully'));
  }

  public function bulkApprove(Request $request)
  {
    $request->validate([
      'payslip_ids' => 'required|array',
      'payslip_ids.*' => 'exists:payslips,id',
    ]);

    $payslips = Payslip::whereIn('id', $request->payslip_ids)->where('status', 'draft')->get();

    $approvedCount = 0;
    foreach ($payslips as $payslip) {
      $payslip->update([
        'status' => 'approved',
        'approved_by' => auth()->id(),
        'approved_at' => now(),
      ]);
      $approvedCount++;
    }

    event(
      new ActivityLogged(
        auth()->user(),
        'bulk_approved_payslips',
        __('payroll.activity.bulk_payslips_approved', ['count' => $approvedCount]),
        null
      )
    );

    return redirect()
      ->back()
      ->with('flash.banner', __('payroll.payslips.bulk_approved_successfully', ['count' => $approvedCount]));
  }

  public function destroy(Payslip $payslip)
  {
    if ($payslip->status === 'paid') {
      return redirect()
        ->back()
        ->withErrors([
          'status' => __('payroll.payslips.cannot_delete_paid'),
        ]);
    }

    $userName = $payslip->user->name;
    $payslip->delete();

    event(
      new ActivityLogged(
        auth()->user(),
        'deleted_payslip',
        __('payroll.activity.payslip_deleted', ['user' => $userName]),
        $payslip
      )
    );

    return redirect()->route('payslips.index')->with('flash.banner', __('payroll.payslips.deleted_successfully'));
  }

  public function downloadPdf(Payslip $payslip)
  {
    $payslip->load(['user.employeeProfile']);

    // Generate PDF using a PDF library (e.g., dompdf, tcpdf)
    // This is a placeholder for PDF generation
    return response()->json(
      [
        'message' => __('payroll.payslips.pdf_generation_not_implemented'),
      ],
      501
    );
  }

  protected function generatePayslipForUser(User $user, string $startDate, string $endDate, string $payDate): ?Payslip
  {
    $employeeProfile = $user->employeeProfile;

    // Get approved time entries for the period
    $timeEntries = TimeEntry::where('user_id', $user->id)
      ->whereBetween('start_datetime', [$startDate, $endDate])
      ->approved()
      ->get();

    if ($timeEntries->isEmpty()) {
      return null;
    }

    // Calculate regular and overtime hours
    $regularHours = 0;
    $overtimeHours = 0;

    $dailyHours = $timeEntries->groupBy(function ($entry) {
      return Carbon::parse($entry->start_datetime)->format('Y-m-d');
    });

    foreach ($dailyHours as $day => $dayEntries) {
      $dayTotal = $dayEntries->sum('hours_worked');
      $standardDaily = $employeeProfile->standard_hours_per_day;

      if ($dayTotal <= $standardDaily) {
        $regularHours += $dayTotal;
      } else {
        $regularHours += $standardDaily;
        $overtimeHours += $dayTotal - $standardDaily;
      }
    }

    // Calculate pay amounts
    $regularRate = $employeeProfile->base_hourly_rate;
    $overtimeRate = $employeeProfile->getOvertimeRate();

    $grossRegularPay = $regularHours * $regularRate;
    $grossOvertimePay = $overtimeHours * $overtimeRate;
    $grossTotalPay = $grossRegularPay + $grossOvertimePay;

    // Calculate taxes
    $taxResult = TaxConfiguration::calculateTotalTaxes($grossTotalPay);

    // Create payslip
    $payslip = Payslip::create([
      'user_id' => $user->id,
      'pay_period_start' => $startDate,
      'pay_period_end' => $endDate,
      'pay_date' => $payDate,
      'regular_hours' => $regularHours,
      'overtime_hours' => $overtimeHours,
      'regular_rate' => $regularRate,
      'overtime_rate' => $overtimeRate,
      'gross_regular_pay' => $grossRegularPay,
      'gross_overtime_pay' => $grossOvertimePay,
      'gross_total_pay' => $grossTotalPay,
      'tax_deductions' => $taxResult['breakdown'],
      'total_tax_deductions' => $taxResult['total'],
      'other_deductions' => [],
      'total_other_deductions' => 0,
      'bonuses' => [],
      'total_bonuses' => 0,
      'status' => 'draft',
      'generated_by' => auth()->id(),
      'metadata' => [
        'time_entries_count' => $timeEntries->count(),
        'generation_date' => now()->toDateTimeString(),
      ],
    ]);

    return $payslip;
  }

  public function generateBulk()
  {
    $settings = PayrollSettings::current();
    $users = User::whereHas('employeeProfile', function ($query) {
      $query->active();
    })
      ->with('employeeProfile')
      ->get();

    // Calculate pay period based on settings
    $today = now();
    switch ($settings->pay_period) {
      case 'weekly':
        $startDate = $today->startOfWeek()->format('Y-m-d');
        $endDate = $today->endOfWeek()->format('Y-m-d');
        break;

      case 'bi_weekly':
        $startDate = $today->subWeeks(2)->startOfWeek()->format('Y-m-d');
        $endDate = $today->endOfWeek()->format('Y-m-d');
        break;

      case 'monthly':
      default:
        $startDate = $today->startOfMonth()->format('Y-m-d');
        $endDate = $today->endOfMonth()->format('Y-m-d');
        break;
    }

    $payDate = $settings->getNextPayDate()->format('Y-m-d');

    return Inertia::render('Payslips/GenerateBulk', [
      'users' => $users->map(function ($user) {
        return [
          'id' => $user->id,
          'name' => $user->name,
          'email' => $user->email,
          'employee_id' => $user->employeeProfile->employee_id,
          'hourly_rate' => $user->employeeProfile->base_hourly_rate,
        ];
      }),
      'suggestedPeriod' => [
        'start_date' => $startDate,
        'end_date' => $endDate,
        'pay_date' => $payDate,
      ],
    ]);
  }
}
