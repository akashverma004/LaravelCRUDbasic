<?php

namespace Database\Seeders;

use App\Models\OnboardingTemplate;
use App\Models\OnboardingTemplateTask;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class OnboardingSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = Tenant::first()->id ?? 1;

        $template = OnboardingTemplate::create([
            'tenant_id' => $tenantId,
            'name' => 'Standard Employee Onboarding',
            'description' => 'General tasks for all new hires.',
        ]);

        $tasks = [
            ['title' => 'Submit Identity Documents', 'description' => 'Upload your ID or Passport in the Documents section.'],
            ['title' => 'Complete Bank Details', 'description' => 'Add your bank account information in My Profile.'],
            ['title' => 'Setup Workstation', 'description' => 'Coordinate with IT to get your laptop and monitor.'],
            ['title' => 'Introduction Call', 'description' => 'Schedule a brief intro with your team lead.'],
            ['title' => 'Read Company Policy', 'description' => 'Review the employee handbook in the Policies section.'],
        ];

        foreach ($tasks as $index => $task) {
            OnboardingTemplateTask::create(array_merge($task, [
                'template_id' => $template->id,
                'sort_order' => $index
            ]));
        }
    }
}
