<?php

namespace Database\Seeders;

use App\Models\AttendanceLog;
use App\Models\AuditLog;
use App\Models\CalendarIntegration;
use App\Models\Customer;
use App\Models\DemandForecast;
use App\Models\FeatureModule;
use App\Models\ServiceRequest;
use App\Models\Technician;
use App\Models\User;
use App\Services\AiForecastService;
use App\Services\ForecastService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('modules.definitions', []) as $module) {
            FeatureModule::updateOrCreate(['key' => $module['key']], $module);
        }

        $admin = User::updateOrCreate(
            ['email' => 'admin@electricaldemandlens.test'],
            [
                'name' => 'Alex Admin',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'phone' => '555-0100',
                'company' => 'ElectricalDemandLens Demo Electric',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $owner = User::updateOrCreate(
            ['email' => 'owner@electricaldemandlens.test'],
            [
                'name' => 'Olivia Owner',
                'password' => Hash::make('password'),
                'role' => User::ROLE_OWNER,
                'phone' => '555-0101',
                'company' => 'BrightCircuit Electric LLC',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $staff = User::updateOrCreate(
            ['email' => 'staff@electricaldemandlens.test'],
            [
                'name' => 'Sam Planner',
                'password' => Hash::make('password'),
                'role' => User::ROLE_STAFF,
                'phone' => '555-0102',
                'company' => 'BrightCircuit Electric LLC',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $techUser = User::updateOrCreate(
            ['email' => 'tech@electricaldemandlens.test'],
            [
                'name' => 'Taylor Tech',
                'password' => Hash::make('password'),
                'role' => User::ROLE_TECHNICIAN,
                'phone' => '555-0103',
                'company' => 'BrightCircuit Electric LLC',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@electricaldemandlens.test'],
            [
                'name' => 'Casey Customer',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CUSTOMER,
                'phone' => '555-0199',
                'company' => null,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $techs = collect([
            ['name' => 'Taylor Tech', 'email' => 'tech@electricaldemandlens.test', 'specialty' => 'panel_upgrade', 'license_number' => 'EL-TX-4412', 'service_zips' => '75201,75202,75204', 'user_id' => $techUser->id],
            ['name' => 'Jordan Volt', 'email' => 'jordan@electricaldemandlens.test', 'specialty' => 'ev_charger', 'license_number' => 'EL-TX-5521', 'service_zips' => '75201,75001', 'user_id' => null],
            ['name' => 'Riley Wire', 'email' => 'riley@electricaldemandlens.test', 'specialty' => 'residential_wiring', 'license_number' => 'EL-TX-6630', 'service_zips' => '75204,75024', 'user_id' => null],
        ])->map(function (array $data) {
            return Technician::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['status' => 'available', 'daily_capacity' => 6, 'rating' => 4.8, 'phone' => '555-0200'])
            );
        });

        $customers = collect([
            ['name' => 'Morgan Rivera', 'email' => 'morgan@example.com', 'phone' => '555-1001', 'address' => '120 Maple St', 'city' => 'Dallas', 'state' => 'TX', 'zip' => '75201', 'sms_consent' => true],
            ['name' => 'Avery Chen', 'email' => 'avery@example.com', 'phone' => '555-1002', 'address' => '88 Oak Ave', 'city' => 'Dallas', 'state' => 'TX', 'zip' => '75204', 'sms_consent' => true],
            ['name' => 'Blake Nguyen', 'email' => 'blake@example.com', 'phone' => '555-1003', 'address' => '41 Cedar Ln', 'city' => 'Plano', 'state' => 'TX', 'zip' => '75024', 'sms_consent' => false],
            ['name' => 'Drew Patel', 'email' => 'drew@example.com', 'phone' => '555-1004', 'address' => '9 Birch Rd', 'city' => 'Richardson', 'state' => 'TX', 'zip' => '75001', 'sms_consent' => true],
        ])->map(function (array $data) {
            return Customer::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['sms_consent_at' => $data['sms_consent'] ? now()->subDays(10) : null])
            );
        });

        CalendarIntegration::updateOrCreate(
            ['provider' => 'google_calendar'],
            ['label' => 'Ops Capacity Calendar', 'enabled' => true, 'config' => ['calendar_id' => 'primary'], 'last_synced_at' => now()->subMinutes(12)]
        );
        CalendarIntegration::updateOrCreate(
            ['provider' => 'jobber'],
            ['label' => 'Jobber History Mirror', 'enabled' => false, 'config' => ['account' => 'demo'], 'last_synced_at' => null]
        );

        $requests = [
            ['ticket_no' => 'SR-1001', 'customer' => 0, 'service_type' => 'Emergency Outage / No Power', 'priority' => 'high', 'source' => 'phone', 'hours' => 3, 'days' => 1],
            ['ticket_no' => 'SR-1002', 'customer' => 1, 'service_type' => 'Panel Upgrade', 'priority' => 'normal', 'source' => 'web', 'hours' => 6, 'days' => 2],
            ['ticket_no' => 'SR-1003', 'customer' => 2, 'service_type' => 'EV Charger Install', 'priority' => 'normal', 'source' => 'jobber', 'hours' => 4, 'days' => 3],
            ['ticket_no' => 'SR-1004', 'customer' => 3, 'service_type' => 'Lighting Retrofit', 'priority' => 'high', 'source' => 'servicetitan', 'hours' => 5, 'days' => 4],
            ['ticket_no' => 'SR-1005', 'customer' => 0, 'service_type' => 'Outlet / Switch Repair', 'priority' => 'low', 'source' => 'phone', 'hours' => 2, 'days' => 2],
            ['ticket_no' => 'SR-1006', 'customer' => 1, 'service_type' => 'Generator Install', 'priority' => 'normal', 'source' => 'web', 'hours' => 8, 'days' => 1],
        ];

        $created = [];
        foreach ($requests as $row) {
            $c = $customers[$row['customer']];
            $created[] = ServiceRequest::updateOrCreate(
                ['ticket_no' => $row['ticket_no']],
                [
                    'customer_id' => $c->id,
                    'service_type' => $row['service_type'],
                    'priority' => $row['priority'],
                    'status' => 'intake',
                    'source' => $row['source'],
                    'description' => 'Historical + open demand signal for weekly electrical capacity forecasting.',
                    'address' => $c->address,
                    'zip' => $c->zip,
                    'estimated_hours' => $row['hours'],
                    'preferred_start' => now()->addDays($row['days'])->setTime(10, 0),
                    'preferred_end' => now()->addDays($row['days'])->setTime(12, 0),
                    'assigned_technician_id' => $techs[$row['customer'] % $techs->count()]->id,
                    'created_by' => $staff->id,
                ]
            );
        }

        $ai = app(AiForecastService::class);
        $forecastSvc = app(ForecastService::class);

        $draft = $ai->draftWeeklyForecast($staff->id);
        $forecastId = $draft->meta['forecast_id'] ?? null;
        $forecast = $forecastId ? DemandForecast::find($forecastId) : DemandForecast::latest('id')->first();

        if ($forecast) {
            $ai->approveDraft($draft, $owner->id);
            $capacity = $ai->draftCapacityPlans($forecast, $staff->id);
            $ai->approveDraft($capacity['draft'], $owner->id);
            foreach (array_slice($capacity['plans'], 0, 2) as $plan) {
                $forecastSvc->approveCapacityPlan($plan, $owner->id);
            }
            $forecastSvc->publishForecast($forecast, $owner->id);

            foreach (array_slice($created, 0, 3) as $sr) {
                $forecastSvc->createJobFromRequest($sr, $forecast->id, $staff->id);
            }
        }

        AttendanceLog::create([
            'technician_id' => $techs[0]->id,
            'event' => 'check_in',
            'logged_at' => now()->subHours(2),
            'source' => 'mobile',
            'geo' => '32.7767,-96.7970',
        ]);

        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'system.seeded',
            'summary' => 'Demo dataset seeded for ElectricalDemandLens demand & capacity forecaster',
            'meta' => ['users' => 5, 'modules' => FeatureModule::count()],
        ]);
    }
}
