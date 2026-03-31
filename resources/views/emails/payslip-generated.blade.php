<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Payslip Ready – {{ $payslip->month }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f1f5f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f1f5f9;min-height:100vh;">
    <tr>
        <td align="center" style="padding:40px 16px;">

            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;">

                {{-- ── Header ── --}}
                <tr>
                    <td style="background:linear-gradient(135deg,#0e7490 0%,#0369a1 100%);border-radius:16px 16px 0 0;padding:36px 40px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td>
                                    <p style="margin:0 0 4px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.2em;color:rgba(255,255,255,0.65);">PeopleFlow HRMS</p>
                                    <h1 style="margin:0;font-size:24px;font-weight:800;color:#ffffff;letter-spacing:-0.5px;line-height:1.2;">Your Payslip is Ready 💰</h1>
                                    <p style="margin:8px 0 0;font-size:13px;color:rgba(255,255,255,0.75);">{{ $payslip->month }}</p>
                                </td>
                                <td align="right" valign="top">
                                    <div style="display:inline-block;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);border-radius:10px;padding:10px 16px;text-align:center;">
                                        <p style="margin:0;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:0.15em;color:rgba(255,255,255,0.7);">Net Pay</p>
                                        <p style="margin:4px 0 0;font-size:22px;font-weight:800;color:#ffffff;letter-spacing:-0.5px;">{{ $netPay }}</p>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- ── Body ── --}}
                <tr>
                    <td style="background:#ffffff;padding:36px 40px;">

                        {{-- Greeting --}}
                        <p style="margin:0 0 20px;font-size:15px;font-weight:600;color:#1e293b;">
                            Hi {{ $employee->full_name ?? $notifiable->name }},
                        </p>
                        <p style="margin:0 0 28px;font-size:14px;color:#475569;line-height:1.7;">
                            Your payslip for <strong style="color:#1e293b;">{{ $payslip->month }}</strong> has been generated and is ready to view.
                            Here's a quick summary of your earnings for this period:
                        </p>

                        {{-- Pay Summary Card --}}
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;margin-bottom:28px;">
                            <tr style="background:#f1f5f9;">
                                <td style="padding:10px 20px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.15em;color:#94a3b8;border-bottom:1px solid #e2e8f0;">Pay Summary</td>
                                <td style="padding:10px 20px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.15em;color:#94a3b8;text-align:right;border-bottom:1px solid #e2e8f0;">Amount</td>
                            </tr>
                            <tr>
                                <td style="padding:12px 20px;font-size:13px;color:#475569;border-bottom:1px solid #f1f5f9;">Base Salary</td>
                                <td style="padding:12px 20px;font-size:13px;font-weight:600;color:#1e293b;text-align:right;border-bottom:1px solid #f1f5f9;">₹{{ number_format((float)$payslip->base_salary, 2) }}</td>
                            </tr>
                            @if((float)$payslip->total_allowances > 0)
                            <tr>
                                <td style="padding:12px 20px;font-size:13px;color:#475569;border-bottom:1px solid #f1f5f9;">+ Allowances &amp; Earnings</td>
                                <td style="padding:12px 20px;font-size:13px;font-weight:600;color:#059669;text-align:right;border-bottom:1px solid #f1f5f9;">+₹{{ number_format((float)$payslip->total_allowances, 2) }}</td>
                            </tr>
                            @endif
                            @if((float)$payslip->total_deductions > 0)
                            <tr>
                                <td style="padding:12px 20px;font-size:13px;color:#475569;border-bottom:1px solid #f1f5f9;">− Deductions</td>
                                <td style="padding:12px 20px;font-size:13px;font-weight:600;color:#dc2626;text-align:right;border-bottom:1px solid #f1f5f9;">−₹{{ number_format((float)$payslip->total_deductions, 2) }}</td>
                            </tr>
                            @endif
                            <tr style="background:#eff6ff;">
                                <td style="padding:14px 20px;font-size:14px;font-weight:800;color:#1e293b;">Net Take-Home Pay</td>
                                <td style="padding:14px 20px;font-size:18px;font-weight:800;color:#0e7490;text-align:right;">{{ $netPay }}</td>
                            </tr>
                        </table>

                        {{-- Period detail --}}
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:32px;">
                            <tr>
                                <td width="50%" style="padding:4px 0;">
                                    <p style="margin:0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#94a3b8;">Pay Period</p>
                                    <p style="margin:4px 0 0;font-size:13px;font-weight:600;color:#334155;">
                                        {{ $payslip->period_start->format('d M Y') }} – {{ $payslip->period_end->format('d M Y') }}
                                    </p>
                                </td>
                                <td width="50%" style="padding:4px 0;text-align:right;">
                                    <p style="margin:0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#94a3b8;">Status</p>
                                    <p style="margin:4px 0 0;">
                                        @if($payslip->status === 'paid')
                                            <span style="display:inline-block;background:#dcfce7;color:#15803d;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;padding:3px 10px;border-radius:20px;">Paid</span>
                                        @else
                                            <span style="display:inline-block;background:#fef3c7;color:#b45309;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;padding:3px 10px;border-radius:20px;">Pending</span>
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        </table>

                        {{-- CTA Button --}}
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:32px;">
                            <tr>
                                <td align="center">
                                    <a href="{{ $viewUrl }}"
                                       style="display:inline-block;background:linear-gradient(135deg,#0e7490,#0369a1);color:#ffffff;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.15em;text-decoration:none;padding:14px 36px;border-radius:10px;box-shadow:0 4px 14px rgba(14,116,144,0.35);">
                                        Download PDF Payslip
                                    </a>
                                </td>
                            </tr>
                        </table>

                        {{-- Divider --}}
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
                            <tr><td style="border-top:1px solid #e2e8f0;"></td></tr>
                        </table>

                        {{-- Footer note --}}
                        <p style="margin:0;font-size:12px;color:#94a3b8;line-height:1.6;">
                            If you have any questions about your payslip, please reach out to your HR team.
                            This email was sent automatically — please do not reply directly.
                        </p>

                    </td>
                </tr>

                {{-- ── Email Footer ── --}}
                <tr>
                    <td style="background:#f8fafc;border:1px solid #e2e8f0;border-top:none;border-radius:0 0 16px 16px;padding:20px 40px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="font-size:11px;color:#94a3b8;">
                                    © {{ date('Y') }} PeopleFlow HRMS · All rights reserved
                                </td>
                                <td align="right" style="font-size:11px;color:#cbd5e1;">
                                    Ref #{{ str_pad($payslip->id, 6, '0', STR_PAD_LEFT) }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
