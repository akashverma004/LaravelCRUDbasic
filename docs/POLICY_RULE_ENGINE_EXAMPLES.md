# Policy Rule Engine JSON Examples

This document defines the JSON shape expected by the policy rule evaluator (`App\Services\Policies\PolicyRuleEvaluator`).

## 1. Core JSON Structure

```json
{
  "mode": "all",
  "conditions": [
    {
      "field": "employee.department",
      "operator": "in",
      "value": ["engineering", "product"]
    },
    {
      "field": "request.duration_days",
      "operator": "lte",
      "value": 5
    },
    {
      "group": {
        "mode": "any",
        "conditions": [
          { "field": "employee.role", "operator": "eq", "value": "manager" },
          { "field": "employee.level", "operator": "gte", "value": 4 }
        ]
      }
    }
  ],
  "actions": {
    "require_approval": true
  }
}
```

## 2. Supported Operators

- `eq`: equals
- `neq`: not equals
- `gt`: greater than
- `gte`: greater than or equal
- `lt`: lower than
- `lte`: lower than or equal
- `in`: value exists in array
- `not_in`: value not in array
- `contains`: string contains value OR array contains value
- `exists`: field exists in request context

## 3. Example Context Payload

This context is sent to evaluation endpoints:

```json
{
  "employee": {
    "id": 12,
    "department": "engineering",
    "role": "developer",
    "type": "full-time",
    "joined_months": 14,
    "location_country": "IN",
    "location_state": "KA"
  },
  "request": {
    "leave_type": "annual",
    "duration_days": 3,
    "days": 2
  },
  "attendance": {
    "late_minutes": 12,
    "overtime_minutes": 45,
    "clock_in": "09:14"
  },
  "claim": {
    "category": "internet",
    "amount": 2400
  },
  "payroll": {
    "pay_cycle": "monthly"
  }
}
```

## 4. Policy-Type Examples

### Leave Policy (`leave_policies.rules`)

```json
{
  "mode": "all",
  "conditions": [
    { "field": "employee.type", "operator": "eq", "value": "full-time" },
    { "field": "request.leave_type", "operator": "in", "value": ["annual", "sick"] },
    { "field": "employee.joined_months", "operator": "gte", "value": 3 }
  ],
  "actions": {
    "max_days_per_request": 10,
    "require_manager_approval": true,
    "allow_carry_forward": true
  }
}
```

### Attendance Policy (`attendance_policies.rules`)

```json
{
  "mode": "all",
  "conditions": [
    { "field": "attendance.clock_in", "operator": "exists", "value": true },
    { "field": "attendance.late_minutes", "operator": "lte", "value": 15 }
  ],
  "actions": {
    "mark_present": true,
    "apply_grace": true
  }
}
```

### Holiday Policy (`holiday_policies.rules`)

```json
{
  "mode": "any",
  "conditions": [
    { "field": "employee.location_country", "operator": "eq", "value": "IN" },
    { "field": "employee.location_state", "operator": "eq", "value": "KA" }
  ],
  "actions": {
    "holiday_calendar": "india-karnataka",
    "optional_holidays_allowed": 2
  }
}
```

### Payroll Policy (`payroll_policies.rules`)

```json
{
  "mode": "all",
  "conditions": [
    { "field": "payroll.pay_cycle", "operator": "eq", "value": "monthly" }
  ],
  "actions": {
    "pay_day": 30,
    "cutoff_day": 25,
    "prorate_on_join": true,
    "prorate_on_exit": true
  }
}
```

### Probation Policy (`probation_policies.rules`)

```json
{
  "mode": "all",
  "conditions": [
    { "field": "employee.role", "operator": "not_in", "value": ["intern"] }
  ],
  "actions": {
    "probation_days": 90,
    "extension_allowed": true,
    "max_extension_days": 60
  }
}
```

### Notice Period Policy (`notice_period_policies.rules`)

```json
{
  "mode": "all",
  "conditions": [
    { "field": "employee.confirmed", "operator": "eq", "value": true }
  ],
  "actions": {
    "notice_days": 30,
    "buyout_allowed": true,
    "waiver_allowed": false
  }
}
```

### Overtime Policy (`overtime_policies.rules`)

```json
{
  "mode": "all",
  "conditions": [
    { "field": "attendance.overtime_minutes", "operator": "gte", "value": 30 }
  ],
  "actions": {
    "weekday_multiplier": 1.5,
    "weekend_multiplier": 2.0,
    "holiday_multiplier": 2.5
  }
}
```

### WFH Policy (`wfh_policies.rules`)

```json
{
  "mode": "all",
  "conditions": [
    { "field": "employee.department", "operator": "in", "value": ["engineering", "design"] },
    { "field": "request.days", "operator": "lte", "value": 3 }
  ],
  "actions": {
    "approval_required": true,
    "monthly_limit_days": 8
  }
}
```

### Reimbursement Policy (`reimbursement_policies.rules`)

```json
{
  "mode": "all",
  "conditions": [
    { "field": "claim.category", "operator": "in", "value": ["travel", "meal", "internet"] },
    { "field": "claim.amount", "operator": "lte", "value": 10000 }
  ],
  "actions": {
    "receipt_required": true,
    "approval_matrix": ["manager", "finance"]
  }
}
```

### Code of Conduct Policy (`code_of_conduct_policies.rules`)

```json
{
  "mode": "all",
  "conditions": [
    { "field": "employee.status", "operator": "eq", "value": "active" }
  ],
  "actions": {
    "acknowledgement_required": true,
    "breach_actions": ["warning", "suspension", "termination"]
  }
}
```

## 5. API Evaluation Endpoints

For each module:

- `POST /api/policies/{module}/{id}/evaluate`
- `POST /api/policies/{module}/evaluate-active`

Body:

```json
{
  "context": {
    "employee": { "department": "engineering" }
  },
  "tenant_id": 1,
  "effective_on": "2026-03-04"
}
```

