<?php

namespace App\Notifications;

use App\Models\Payslip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayslipGenerated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Payslip $payslip,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $payslip  = $this->payslip;
        $employee = $payslip->employee;
        $netPay   = '₹' . number_format((float) $payslip->net_pay, 2);
        $viewUrl  = url('/payroll/payslips/' . $payslip->id . '/pdf');

        return (new MailMessage)
            ->subject('Your Payslip for ' . $payslip->month . ' is Ready 💰')
            ->view('emails.payslip-generated', [
                'payslip'     => $payslip,
                'employee'    => $employee,
                'netPay'      => $netPay,
                'viewUrl'     => $viewUrl,
                'notifiable'  => $notifiable,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'      => 'Payslip Ready 💰',
            'body'       => 'Your payslip for ' . $this->payslip->month . ' is available. Net pay: ₹' . number_format((float) $this->payslip->net_pay, 2),
            'icon'       => 'banknotes',
            'payslip_id' => $this->payslip->id,
            'month'      => $this->payslip->month,
            'net_pay'    => $this->payslip->net_pay,
            'status'     => $this->payslip->status,
            'action_url' => '/payroll',
        ];
    }
}
