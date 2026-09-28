<?php

namespace Database\Seeders;

use App\Models\{Booking, PaymentMethod, Service, Slot, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──────────────────────────────────────────────
        $admin = User::create([
            'name'        => 'المدير العام',
            'national_id' => '900000001',
            'email'       => 'admin@booking.test',
            'password'    => Hash::make('password'),
            'role'        => 'admin',
            'phone'       => '0591234567',
        ]);

        // ── Customer ───────────────────────────────────────────
        $customer = User::create([
            'name'        => 'سارة احمد',
            'national_id' => '900000004',
            'email'       => 'customer@booking.test',
            'password'    => Hash::make('password'),
            'role'        => 'customer',
            'phone'       => '0591234567',
        ]);

        // ── العيادات الطبية (مزودو خدمة) ─────────────────────────
        $clinics = [
            [
                'national_id' => '900000005',
                'name'        => 'د. محمد الحلبي',
                'email'       => 'dr.halabi@clinic.test',
                'phone'       => '0599010101',
                'bio'         => 'طبيب أسنان، خبرة 8 سنوات في علاج وتجميل الأسنان',
                'service'     => [
                    'name'             => 'كشف وعلاج الأسنان',
                    'description'      => 'فحص شامل للأسنان مع خطة علاج مناسبة: حشوات، تنظيف، تبييض، وخلع بسيط.',
                    'duration_minutes' => 30,
                    'price'            => 100,
                    'location'         => 'عيادة الحلبي لطب الأسنان - غزة، حي الرمال',
                ],
            ],
            [
                'national_id' => '900000006',
                'name'        => 'د. رنا أبو شمالة',
                'email'       => 'dr.rana@clinic.test',
                'phone'       => '0599020202',
                'bio'         => 'طبيبة أطفال، خبرة 7 سنوات في طب حديثي الولادة والأطفال',
                'service'     => [
                    'name'             => 'كشف ومتابعة الأطفال',
                    'description'      => 'كشف دوري وتطعيمات ومتابعة نمو الأطفال من الولادة حتى 12 عاماً.',
                    'duration_minutes' => 20,
                    'price'            => 60,
                    'location'         => 'عيادة النور لطب الأطفال - غزة، حي النصر',
                ],
            ],
            [
                'national_id' => '900000007',
                'name'        => 'د. خالد النجار',
                'email'       => 'dr.khaled@clinic.test',
                'phone'       => '0599030303',
                'bio'         => 'طبيب عام، خبرة 10 سنوات في الرعاية الصحية الأولية',
                'service'     => [
                    'name'             => 'كشف طبي عام',
                    'description'      => 'كشف طبي شامل، متابعة الحالات المزمنة، والإسعافات الأولية.',
                    'duration_minutes' => 20,
                    'price'            => 40,
                    'location'         => 'المركز الطبي الشامل - غزة، حي الشجاعية',
                ],
            ],
            [
                'national_id' => '900000008',
                'name'        => 'د. هبة عاشور',
                'email'       => 'dr.heba@clinic.test',
                'phone'       => '0561010101',
                'bio'         => 'استشارية نساء وتوليد، خبرة 12 عاماً',
                'service'     => [
                    'name'             => 'كشف نساء وتوليد',
                    'description'      => 'متابعة الحمل، فحوصات دورية، وتشخيص وعلاج أمراض النساء.',
                    'duration_minutes' => 30,
                    'price'            => 80,
                    'location'         => 'عيادة الأمل لطب النساء والتوليد - غزة، حي تل الهوى',
                ],
            ],
            [
                'national_id' => '900000009',
                'name'        => 'د. يوسف بركة',
                'email'       => 'dr.yousef@clinic.test',
                'phone'       => '0561020202',
                'bio'         => 'طبيب جلدية وتجميل، خبرة 6 سنوات',
                'service'     => [
                    'name'             => 'كشف وعلاج الجلدية',
                    'description'      => 'تشخيص وعلاج الأمراض الجلدية، حب الشباب، والحساسية.',
                    'duration_minutes' => 25,
                    'price'            => 70,
                    'location'         => 'مركز بركة للجلدية - غزة، حي الزيتون',
                ],
            ],
            [
                'national_id' => '900000010',
                'name'        => 'د. سامر العطار',
                'email'       => 'dr.samer@clinic.test',
                'phone'       => '0561030303',
                'bio'         => 'استشاري عظام، خبرة 9 سنوات',
                'service'     => [
                    'name'             => 'كشف عظام ومفاصل',
                    'description'      => 'تشخيص وعلاج إصابات وآلام العظام والمفاصل والعمود الفقري.',
                    'duration_minutes' => 30,
                    'price'            => 90,
                    'location'         => 'عيادة العطار للعظام والمفاصل - غزة، حي الدرج',
                ],
            ],
        ];

        $firstService = null;

        foreach ($clinics as $clinic) {
            $provider = User::create([
                'name'        => $clinic['name'],
                'national_id' => $clinic['national_id'],
                'email'       => $clinic['email'],
                'password'    => Hash::make('password'),
                'role'        => 'provider',
                'phone'       => $clinic['phone'],
                'bio'         => $clinic['bio'],
            ]);

            PaymentMethod::create([
                'provider_id'    => $provider->id,
                'type'           => 'bank',
                'label'          => 'بنك فلسطين',
                'account_name'   => $provider->name,
                'account_number' => 'PS' . rand(10000000, 99999999) . 'PALS',
            ]);
            PaymentMethod::create([
                'provider_id'    => $provider->id,
                'type'           => 'palpay',
                'label'          => 'PalPay',
                'account_name'   => $provider->name,
                'account_number' => $provider->phone,
            ]);

            $service = Service::create([
                'provider_id'      => $provider->id,
                'is_active'        => true,
                ...$clinic['service'],
            ]);

            $this->createSlots($service, $service->duration_minutes);

            $firstService ??= $service;
        }

        // ── حجز تجريبي ─────────────────────────────────────────
        $slot = $firstService->slots()->where('is_booked', false)->first();
        if ($slot) {
            $slot->update(['is_booked' => true]);
            Booking::create([
                'customer_id' => $customer->id,
                'slot_id'     => $slot->id,
                'status'      => 'confirmed',
                'notes'       => 'أرجو الالتزام بالموعد',
            ]);
        }

        $this->command->info('✅ تم إنشاء البيانات التجريبية بنجاح!');
        $this->command->table(
            ['الدور', 'رقم الهوية', 'الإيميل', 'كلمة المرور'],
            [
                ['Admin',    '900000001', 'admin@booking.test',    'password'],
                ['Customer', '900000004', 'customer@booking.test', 'password'],
                ['Provider', '900000005', 'dr.halabi@clinic.test', 'password'],
                ['Provider', '900000006', 'dr.rana@clinic.test',   'password'],
                ['Provider', '900000007', 'dr.khaled@clinic.test', 'password'],
                ['Provider', '900000008', 'dr.heba@clinic.test',   'password'],
                ['Provider', '900000009', 'dr.yousef@clinic.test', 'password'],
                ['Provider', '900000010', 'dr.samer@clinic.test',  'password'],
            ]
        );
    }

    private function createSlots(Service $service, int $duration): void
    {
        $slots = [];
        for ($day = 1; $day <= 14; $day++) {
            $date = Carbon::today()->addDays($day);
            if ($date->isFriday()) continue;

            $start = $date->copy()->setHour(9)->setMinute(0);
            $end   = $date->copy()->setHour(17)->setMinute(0);

            while ($start->copy()->addMinutes($duration)->lte($end)) {
                $slots[] = [
                    'service_id' => $service->id,
                    'start_at'   => $start->toDateTimeString(),
                    'end_at'     => $start->copy()->addMinutes($duration)->toDateTimeString(),
                    'is_booked'  => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $start->addMinutes($duration + 10); // 10 دقائق استراحة
            }
        }
        Slot::insert($slots);
    }
}
