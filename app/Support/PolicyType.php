<?php

namespace App\Support;

final class PolicyType
{
    public const LEAVE = 'leave';
    public const ATTENDANCE = 'attendance';
    public const HOLIDAY = 'holiday';
    public const PAYROLL = 'payroll';
    public const PROBATION = 'probation';
    public const NOTICE_PERIOD = 'notice_period';
    public const OVERTIME = 'overtime';
    public const WFH = 'wfh';
    public const REIMBURSEMENT = 'reimbursement';
    public const CODE_OF_CONDUCT = 'code_of_conduct';

    public const ALL = [
        self::LEAVE,
        self::ATTENDANCE,
        self::HOLIDAY,
        self::PAYROLL,
        self::PROBATION,
        self::NOTICE_PERIOD,
        self::OVERTIME,
        self::WFH,
        self::REIMBURSEMENT,
        self::CODE_OF_CONDUCT,
    ];

    private function __construct()
    {
    }
}
