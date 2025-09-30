<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Alert;
use App\Models\Shelter;
use App\Models\Request;
use App\Models\Assignment;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@disaster-alert.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '+880-123-456-789',
            'address' => 'Disaster Management Office, Dhaka',
            'city' => 'Dhaka',
            'state' => 'Dhaka Division',
            'postal_code' => '1000',
            'latitude' => 23.8103,
            'longitude' => 90.4125,
            'is_active' => true
        ]);

        // Create Relief Worker
        $reliefWorker = User::create([
            'name' => 'Relief Worker',
            'email' => 'relief@disaster-alert.com',
            'password' => Hash::make('relief123'),
            'role' => 'relief_worker',
            'phone' => '+880-987-654-321',
            'address' => 'Relief Office, Chittagong',
            'city' => 'Chittagong',
            'state' => 'Chittagong Division',
            'postal_code' => '4000',
            'latitude' => 22.3569,
            'longitude' => 91.7832,
            'is_active' => true
        ]);

        // Create Test Citizens
        $citizen1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('citizen123'),
            'role' => 'citizen',
            'phone' => '+880-555-0001',
            'address' => 'House 15, Road 5, Dhanmondi',
            'city' => 'Dhaka',
            'state' => 'Dhaka Division',
            'postal_code' => '1205',
            'latitude' => 23.7465,
            'longitude' => 90.3769,
            'is_active' => true
        ]);

        $citizen2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('citizen123'),
            'role' => 'citizen',
            'phone' => '+880-555-0002',
            'address' => 'Flat 3B, Green View Apartment',
            'city' => 'Dhaka',
            'state' => 'Dhaka Division',
            'postal_code' => '1207',
            'latitude' => 23.7588,
            'longitude' => 90.3900,
            'is_active' => true
        ]);

        // Create Alerts
        Alert::create([
            'title' => 'Flash Flood Warning - Dhaka Metropolitan',
            'description' => 'Heavy rainfall expected in the next 6 hours. Water levels rising rapidly in low-lying areas. Residents are advised to move to higher ground immediately.',
            'severity' => 'Critical',
            'type' => 'Flood',
            'location' => 'Dhaka Metropolitan Area',
            'latitude' => 23.8103,
            'longitude' => 90.4125,
            'status' => 'Active',
            'issued_at' => now()->subHours(2),
            'expires_at' => now()->addHours(12),
            'created_by' => $admin->id
        ]);

        Alert::create([
            'title' => 'Earthquake Alert - Magnitude 5.2',
            'description' => 'Moderate earthquake detected. Minor structural damage possible. Check for injuries and evacuate damaged buildings.',
            'severity' => 'High',
            'type' => 'Earthquake',
            'location' => 'Sylhet Division',
            'latitude' => 24.9036,
            'longitude' => 91.8611,
            'status' => 'Active',
            'issued_at' => now()->subHours(1),
            'expires_at' => now()->addHours(6),
            'created_by' => $admin->id
        ]);

        Alert::create([
            'title' => 'Cyclone Watch - Bay of Bengal',
            'description' => 'Tropical cyclone forming in Bay of Bengal. Coastal areas should prepare for high winds and storm surge.',
            'severity' => 'Moderate',
            'type' => 'Cyclone',
            'location' => 'Coastal Bangladesh',
            'latitude' => 21.4272,
            'longitude' => 92.0058,
            'status' => 'Monitoring',
            'issued_at' => now()->subHours(4),
            'expires_at' => now()->addDays(2),
            'created_by' => $admin->id
        ]);

        // Create Shelters
        $shelter1 = Shelter::create([
            'name' => 'Dhaka University Emergency Shelter',
            'description' => 'Large capacity shelter in the heart of Dhaka with modern facilities.',
            'address' => 'Dhaka University Campus, Ramna',
            'city' => 'Dhaka',
            'state' => 'Dhaka Division',
            'postal_code' => '1000',
            'latitude' => 23.7281,
            'longitude' => 90.3930,
            'capacity' => 500,
            'current_occupancy' => 120,
            'facilities' => ['Medical Aid', 'Food Service', 'Clean Water', 'Restrooms', 'Security', 'WiFi'],
            'contact_phone' => '+880-2-9661900',
            'contact_email' => 'emergency@du.ac.bd',
            'status' => 'Active',
            'special_notes' => '24/7 medical staff available'
        ]);

        $shelter2 = Shelter::create([
            'name' => 'Gulshan Community Center',
            'description' => 'Modern community center converted to emergency shelter.',
            'address' => 'Road 11, Block C, Gulshan-1',
            'city' => 'Dhaka',
            'state' => 'Dhaka Division',
            'postal_code' => '1212',
            'latitude' => 23.7808,
            'longitude' => 90.4133,
            'capacity' => 200,
            'current_occupancy' => 180,
            'facilities' => ['Medical Aid', 'Food Service', 'Clean Water', 'Restrooms', 'Air Conditioning'],
            'contact_phone' => '+880-2-8824500',
            'contact_email' => 'info@gulshan-cc.org',
            'status' => 'Active',
            'special_notes' => 'Family-friendly environment with children\'s area'
        ]);

        $shelter3 = Shelter::create([
            'name' => 'Mirpur Sports Complex',
            'description' => 'Large sports facility converted for emergency housing.',
            'address' => 'Mirpur Stadium Road, Section 2',
            'city' => 'Dhaka',
            'state' => 'Dhaka Division',
            'postal_code' => '1216',
            'latitude' => 23.8030,
            'longitude' => 90.3620,
            'capacity' => 800,
            'current_occupancy' => 45,
            'facilities' => ['Medical Aid', 'Food Service', 'Clean Water', 'Restrooms', 'Security', 'Recreation Area'],
            'contact_phone' => '+880-2-8015432',
            'contact_email' => 'shelter@mirpur-sports.gov.bd',
            'status' => 'Active',
            'special_notes' => 'Large open spaces, suitable for families with pets'
        ]);

        $shelter4 = Shelter::create([
            'name' => 'Chittagong Relief Center',
            'description' => 'Coastal emergency shelter with cyclone-resistant construction.',
            'address' => 'CDA Avenue, Nasirabad',
            'city' => 'Chittagong',
            'state' => 'Chittagong Division',
            'postal_code' => '4000',
            'latitude' => 22.3351,
            'longitude' => 91.8325,
            'capacity' => 300,
            'current_occupancy' => 85,
            'facilities' => ['Medical Aid', 'Food Service', 'Clean Water', 'Restrooms', 'Security', 'Generator'],
            'contact_phone' => '+880-31-234567',
            'contact_email' => 'relief@chittagong.gov.bd',
            'status' => 'Active',
            'special_notes' => 'Storm-resistant building, backup power supply'
        ]);

        // Create Emergency Requests
        $request1 = Request::create([
            'user_id' => $citizen1->id,
            'name' => 'John Doe',
            'phone' => '+880-555-0001',
            'email' => 'john@example.com',
            'request_type' => 'Shelter',
            'description' => 'My house is flooded. Need immediate shelter for family of 4 including elderly mother.',
            'location' => 'Dhanmondi, Dhaka',
            'latitude' => 23.7465,
            'longitude' => 90.3769,
            'people_count' => 4,
            'urgency' => 'High',
            'status' => 'Pending',
            'special_needs' => 'Elderly person with mobility issues, requires ground floor access'
        ]);

        $request2 = Request::create([
            'user_id' => $citizen2->id,
            'name' => 'Jane Smith',
            'phone' => '+880-555-0002',
            'email' => 'jane@example.com',
            'request_type' => 'Medical',
            'description' => 'Child injured during earthquake, needs immediate medical attention.',
            'location' => 'Gulshan, Dhaka',
            'latitude' => 23.7588,
            'longitude' => 90.3900,
            'people_count' => 2,
            'urgency' => 'Critical',
            'status' => 'Assigned',
            'special_needs' => '8-year-old child with possible fracture',
            'assigned_at' => now(),
            'assigned_by' => $admin->id,
            'admin_notes' => 'Assigned to Gulshan Community Center with medical facilities'
        ]);

        // Create Assignment for the second request
        Assignment::create([
            'request_id' => $request2->id,
            'shelter_id' => $shelter2->id,
            'assigned_by' => $admin->id,
            'assigned_at' => now(),
            'status' => 'Assigned',
            'notes' => 'Family assigned to Gulshan Community Center due to proximity and medical facilities'
        ]);

        // Update shelter occupancy
        $shelter2->updateOccupancy();

        echo "✅ Database seeded successfully!\n";
        echo "🔐 Admin Login: admin@disaster-alert.com / admin123\n";
        echo "👤 Relief Worker: relief@disaster-alert.com / relief123\n";
        echo "👥 Test Citizens: john@example.com / citizen123\n";
        echo "                jane@example.com / citizen123\n";
    }
}
