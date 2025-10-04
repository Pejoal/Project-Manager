<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lohnabrechnung {{ $payslip->payslip_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            width: 100%;
            margin-bottom: 20px;
        }
        .header td {
            padding: 5px;
            vertical-align: top;
        }
        .company-details {
            text-align: right;
            font-size: 9px;
        }
        .employee-details {
            font-size: 11px;
        }
        .payslip-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .summary-table td {
            border: 1px solid #ddd;
            padding: 6px;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .main-table th, .main-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .main-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            font-size: 9px;
            text-align: center;
            color: #777;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid #555;
            padding-bottom: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <table class="header">
            <tr>
                <td class="employee-details" style="width: 50%;">
                    <strong>{{ $payslip->user->name }}</strong><br>
                    @if($payslip->user->employeeProfile)
                        {{ $payslip->user->employeeProfile->street_address }}<br>
                        {{ $payslip->user->employeeProfile->postal_code }} {{ $payslip->user->employeeProfile->city }}
                    @endif
                </td>
                <td class="company-details" style="width: 50%;">
                    @if($settings)
                        <strong>{{ $settings->company_name }}</strong><br>
                        {{ $settings->company_address }}<br>
                        Steuernummer: {{ $settings->company_tax_id }}
                    @endif
                </td>
            </tr>
        </table>

        <div class="payslip-title">
            Lohn- und Gehaltsabrechnung
        </div>

        <table class="summary-table">
            <tr>
                <td><strong>Abrechnungszeitraum:</strong></td>
                <td>{{ $payslip->pay_period_start->format('d.m.Y') }} - {{ $payslip->pay_period_end->format('d.m.Y') }}</td>
                <td><strong>Personalnummer:</strong></td>
                <td>{{ $payslip->user->employeeProfile->employee_id ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Abrechnungsnummer:</strong></td>
                <td>{{ $payslip->payslip_number }}</td>
                <td><strong>Eintrittsdatum:</strong></td>
                <td>{{ $payslip->user->employeeProfile && $payslip->user->employeeProfile->hire_date ? \Carbon\Carbon::parse($payslip->user->employeeProfile->hire_date)->format('d.m.Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Zahltag:</strong></td>
                <td>{{ $payslip->pay_date->format('d.m.Y') }}</td>
                <td><strong>Steuer-ID:</strong></td>
                <td>{{ $payslip->user->employeeProfile->tax_id ?? 'N/A' }}</td>
            </tr>
        </table>

        <div class="section-title">Bezüge</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th>Art</th>
                    <th class="text-right">Stunden</th>
                    <th class="text-right">Satz (€)</th>
                    <th class="text-right">Betrag (€)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Grundgehalt (Regulär)</td>
                    <td class="text-right">{{ number_format($payslip->regular_hours, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($payslip->regular_rate, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($payslip->gross_regular_pay, 2, ',', '.') }}</td>
                </tr>
                @if($payslip->overtime_hours > 0)
                <tr>
                    <td>Überstunden</td>
                    <td class="text-right">{{ number_format($payslip->overtime_hours, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($payslip->overtime_rate, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($payslip->gross_overtime_pay, 2, ',', '.') }}</td>
                </tr>
                @endif
                @if($payslip->total_bonuses > 0 && is_array($payslip->bonuses))
                    @foreach($payslip->bonuses as $bonus)
                    <tr>
                        <td>Bonus: {{ $bonus['name'] ?? 'Bonus' }}</td>
                        <td colspan="2"></td>
                        <td class="text-right">{{ number_format($bonus['amount'] ?? 0, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @endif
                <tr class="total-row">
                    <td colspan="3"><strong>Gesamtbrutto</strong></td>
                    <td class="text-right"><strong>{{ number_format($payslip->gross_total_pay + $payslip->total_bonuses, 2, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">Gesetzliche Abzüge</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th>Art</th>
                    <th class="text-right">Betrag (€)</th>
                </tr>
            </thead>
            <tbody>
                @if(is_array($payslip->tax_deductions))
                    @foreach($payslip->tax_deductions as $deduction)
                    <tr>
                        <td>{{ $deduction['name'] }}</td>
                        <td class="text-right">{{ number_format($deduction['amount'], 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @endif
                 @if(is_array($payslip->other_deductions))
                    @foreach($payslip->other_deductions as $deduction)
                    <tr>
                        <td>{{ $deduction['name'] }}</td>
                        <td class="text-right">{{ number_format($deduction['amount'], 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @endif
                <tr class="total-row">
                    <td><strong>Gesamtabzüge</strong></td>
                    <td class="text-right"><strong>{{ number_format($payslip->total_tax_deductions + $payslip->total_other_deductions, 2, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>
        
        <div class="section-title">Auszahlung</div>
        <table class="main-table">
            <tbody>
                <tr class="total-row" style="font-size: 14px;">
                    <td><strong>NETTOVERDIENST</strong></td>
                    <td class="text-right"><strong>{{ number_format($payslip->net_pay, 2, ',', '.') }} €</strong></td>
                </tr>
            </tbody>
        </table>
        
        <div class="footer">
            Dieses Dokument wurde maschinell erstellt und ist ohne Unterschrift gültig.<br>
            @if($settings){{ $settings->company_name }} -@endif {{ now()->year }}
        </div>
    </div>
</body>
</html>

