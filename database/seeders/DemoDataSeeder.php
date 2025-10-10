<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Alert;
use App\Models\Shelter;
use App\Models\Request as HelpRequest;
use App\Models\Assignment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data
        Assignment::truncate();
        HelpRequest::truncate();
        Alert::truncate();
        Shelter::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Keep users if they exist, otherwise create
        $admin = User::firstOrCreate(
            ['email' => 'admin@disaster.gov.bd'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '+880-1712345678',
                'is_active' => true
            ]
        );

        $citizen = User::firstOrCreate(
            ['email' => 'citizen@example.com'],
            [
                'name' => 'John Rahman',
                'password' => Hash::make('citizen123'),
                'role' => 'citizen',
                'phone' => '+880-1812345678',
                'is_active' => true
            ]
        );

        $reliefWorker = User::firstOrCreate(
            ['email' => 'relief@disaster.gov.bd'],
            [
                'name' => 'Relief Worker',
                'password' => Hash::make('relief123'),
                'role' => 'relief_worker',
                'phone' => '+880-1912345678',
                'is_active' => true
            ]
        );

        // Create Alerts
        $alert1 = Alert::create([
            'title' => 'Flood Warning - Dhaka',
            'description' => 'Heavy rainfall expected in Dhaka metropolitan area. Water levels rising in major rivers. Citizens are advised to stay indoors and avoid unnecessary travel. Emergency shelters are being prepared.',
            'severity' => 'High',
            'type' => 'Flood',
            'location' => 'Dhaka, Bangladesh',
            'latitude' => 23.8103,
            'longitude' => 90.4125,
            'status' => 'Active',
            'issued_at' => Carbon::now()->subHours(2),
            'expires_at' => Carbon::now()->addDays(2),
            'created_by' => $admin->id
        ]);

        $alert2 = Alert::create([
            'title' => 'Earthquake Alert - Chittagong',
            'description' => 'Minor earthquake of magnitude 4.2 detected in Chittagong region. No immediate structural damage reported. Citizens should remain calm and follow safety protocols.',
            'severity' => 'Moderate',
            'type' => 'Earthquake',
            'location' => 'Chittagong, Bangladesh',
            'latitude' => 22.3569,
            'longitude' => 91.7832,
            'status' => 'Active',
            'issued_at' => Carbon::now()->subHours(5),
            'expires_at' => Carbon::now()->addDay(),
            'created_by' => $admin->id
        ]);

        $alert3 = Alert::create([
            'title' => 'Cyclone Watch - Cox\'s Bazar',
            'description' => 'Severe cyclone approaching Cox\'s Bazar coastal areas. Wind speed expected to reach 120 km/h. Immediate evacuation to designated shelters is recommended.',
            'severity' => 'Critical',
            'type' => 'Cyclone',
            'location' => 'Cox\'s Bazar, Bangladesh',
            'latitude' => 21.4272,
            'longitude' => 92.0058,
            'status' => 'Active',
            'issued_at' => Carbon::now()->subHours(8),
            'expires_at' => Carbon::now()->addDays(3),
            'created_by' => $admin->id
        ]);

        // Create Shelters
        $shelter1 = Shelter::create([
            'name' => 'Dhaka Community Center',
            'description' => 'Large community center with modern facilities. Equipped with backup power and water supply.',
            'address' => '27 Dhanmondi Road',
            'city' => 'Dhaka',
            'state' => 'Dhaka Division',
            'postal_code' => '1209',
            'capacity' => 200,
            'current_occupancy' => 45,
            'status' => 'Active',
            'contact_phone' => '+880-1712345678',
            'contact_email' => 'dhaka.shelter@disaster.gov.bd',
            'facilities' => ['Food Service', 'Medical Aid', 'Sleeping Area', 'Children Play Area', 'Restrooms', 'Security'],
            'latitude' => 23.7465,
            'longitude' => 90.3784
        ]);

        $shelter2 = Shelter::create([
            'name' => 'Cox\'s Bazar Relief Center',
            'description' => 'Coastal relief center designed for cyclone and tsunami emergencies.',
            'address' => 'Beach Road',
            'city' => 'Cox\'s Bazar',
            'state' => 'Chittagong Division',
            'postal_code' => '4700',
            'capacity' => 150,
            'current_occupancy' => 130,
            'status' => 'Active',
            'contact_phone' => '+880-1812345678',
            'contact_email' => 'coxbazar.shelter@disaster.gov.bd',
            'facilities' => ['Food Service', 'Medical Aid', 'Sleeping Area', 'Restrooms'],
            'latitude' => 21.4272,
            'longitude' => 92.0058
        ]);

        $shelter3 = Shelter::create([
            'name' => 'Chittagong Sports Complex',
            'description' => 'Large sports complex converted to emergency shelter.',
            'address' => 'GEC Circle',
            'city' => 'Chittagong',
            'state' => 'Chittagong Division',
            'postal_code' => '4000',
            'capacity' => 300,
            'current_occupancy' => 0,
            'status' => 'Active',
            'contact_phone' => '+880-1912345678',
            'contact_email' => 'ctg.sports@disaster.gov.bd',
            'facilities' => ['Food Service', 'Medical Aid', 'Sleeping Area', 'Sports Facilities', 'Restrooms'],
            'latitude' => 22.3569,
            'longitude' => 91.7832
        ]);

        $shelter4 = Shelter::create([
            'name' => 'Sylhet City Hall',
            'description' => 'City hall building used as emergency shelter.',
            'address' => 'Zindabazar',
            'city' => 'Sylhet',
            'state' => 'Sylhet Division',
            'postal_code' => '3100',
            'capacity' => 100,
            'current_occupancy' => 95,
            'status' => 'Active',
            'contact_phone' => '+880-1612345678',
            'contact_email' => 'sylhet.hall@disaster.gov.bd',
            'facilities' => ['Food', 'Medical Aid', 'Sleeping Area'],
            'latitude' => 24.8949,
            'longitude' => 91.8687
        ]);

        // Create Help Requests
        $request1 = HelpRequest::create([
            'user_id' => $citizen->id,
            'name' => 'Citizen User',
            'phone' => $citizen->phone,
            'email' => $citizen->email,
            'request_type' => 'Shelter',
            'description' => 'Water level rising rapidly in our area. Need immediate evacuation for family of 3.',
            'location' => 'Dhanmondi, Dhaka',
            'latitude' => 23.7465,
            'longitude' => 90.3784,
            'people_count' => 3,
            'urgency' => 'High',
            'status' => 'Assigned',
            'special_needs' => null,
            'assigned_by' => $admin->id,
            'assigned_at' => now(),
            'admin_notes' => 'Verified emergency situation. Family safely relocated.'
        ]);

        $request2 = HelpRequest::create([
            'user_id' => $citizen->id,
            'name' => 'Citizen User',
            'phone' => $citizen->phone,
            'email' => $citizen->email,
            'request_type' => 'Shelter',
            'description' => 'Our building has cracks after earthquake. Family of 4 needs shelter.',
            'location' => 'Old Dhaka',
            'latitude' => 23.7104,
            'longitude' => 90.4074,
            'people_count' => 4,
            'urgency' => 'High',
            'status' => 'Pending',
            'special_needs' => null
        ]);

        $request3 = HelpRequest::create([
            'user_id' => $citizen->id,
            'name' => 'Citizen User',
            'phone' => $citizen->phone,
            'email' => $citizen->email,
            'request_type' => 'Rescue',
            'description' => 'Strong winds approaching. Elderly parents need safe shelter.',
            'location' => 'Chittagong',
            'latitude' => 22.3569,
            'longitude' => 91.7832,
            'people_count' => 2,
            'urgency' => 'Medium',
            'status' => 'Assigned',
            'special_needs' => 'Elderly, limited mobility',
            'assigned_by' => $admin->id,
            'assigned_at' => now(),
            'admin_notes' => 'Auto-assigned based on location proximity'
        ]);

        // Create Assignments
        Assignment::create([
            'request_id' => $request1->id,
            'shelter_id' => $shelter1->id,
            'assigned_by' => $admin->id,
            'assigned_at' => now(),
            'status' => 'Checked In',
            'checked_in_at' => now(),
            'notes' => 'Verified emergency situation. Family safely relocated.'
        ]);

        Assignment::create([
            'request_id' => $request3->id,
            'shelter_id' => $shelter3->id,
            'assigned_by' => $admin->id,
            'assigned_at' => now(),
            'status' => 'Assigned',
            'notes' => 'Auto-assigned to nearest available shelter with capacity.'
        ]);

        $this->command->info('✅ Demo data seeded successfully!');
        $this->command->info('📊 Created:');
        $this->command->info('   - 3 Users (admin, citizen, relief worker)');
        $this->command->info('   - 3 Alerts (Flood, Earthquake, Cyclone)');
        $this->command->info('   - 4 Shelters');
        $this->command->info('   - 3 Help Requests');
        $this->command->info('   - 2 Assignments');
    }
}
