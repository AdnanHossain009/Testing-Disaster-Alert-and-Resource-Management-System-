<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WeatherAlertService
{
    private $apiKey;
    private $baseUrl = 'https://api.openweathermap.org/data/2.5';
    
    // Danger thresholds for weather conditions
    private const DANGER_THRESHOLDS = [
        'wind_speed' => 20, // m/s (72 km/h) - Storm threshold
        'rain_1h' => 50, // mm - Heavy rain
        'rain_3h' => 100, // mm - Very heavy rain/flood risk
        'temp_high' => 40, // °C - Heat wave
        'temp_low' => 5, // °C - Cold wave
        'humidity_high' => 90, // % - High humidity with heat
    ];

    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
        $this->apiKey = config('weather.api_key', env('OPENWEATHER_API_KEY'));
    }

    /**
     * Check weather for multiple locations and create alerts if dangerous conditions detected
     * 
     * @param array $locations Array of [name, lat, lon]
     * @return array Results of weather checks
     */
    public function checkWeatherForLocations(array $locations): array
    {
        $results = [
            'checked_at' => now()->toDateTimeString(),
            'locations_checked' => 0,
            'alerts_created' => 0,
            'conditions' => [],
            'errors' => []
        ];

        if (!$this->apiKey) {
            $results['errors'][] = 'OpenWeather API key not configured';
            Log::error('OpenWeather API key not configured');
            return $results;
        }

        foreach ($locations as $location) {
            try {
                // Support both 'lat'/'lon' and 'latitude'/'longitude' keys
                $lat = $location['lat'] ?? $location['latitude'] ?? null;
                $lon = $location['lon'] ?? $location['longitude'] ?? null;
                
                if (!$lat || !$lon) {
                    $results['errors'][] = "Missing coordinates for {$location['name']}";
                    continue;
                }
                
                $weatherData = $this->getWeatherData($lat, $lon);
                
                if ($weatherData) {
                    $results['locations_checked']++;
                    
                    // Analyze weather and create alert if dangerous
                    $alert = $this->analyzeWeatherAndCreateAlert(
                        $weatherData, 
                        $location['name'],
                        $lat,
                        $lon
                    );
                    
                    if ($alert) {
                        $results['alerts_created']++;
                        $results['conditions'][] = [
                            'location' => $location['name'],
                            'alert_type' => $alert->type,
                            'severity' => $alert->severity,
                            'description' => $alert->description
                        ];
                        
                        Log::info("Weather alert created for {$location['name']}", [
                            'type' => $alert->type,
                            'severity' => $alert->severity
                        ]);
                    }
                }
                
            } catch (\Exception $e) {
                $results['errors'][] = "Failed to check weather for {$location['name']}: {$e->getMessage()}";
                Log::error("Weather check failed for {$location['name']}", [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $results;
    }

    /**
     * Get current weather data from OpenWeatherMap API
     * 
     * @param float $lat Latitude
     * @param float $lon Longitude
     * @return array|null Weather data
     */
    private function getWeatherData(float $lat, float $lon): ?array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/weather", [
                'lat' => $lat,
                'lon' => $lon,
                'appid' => $this->apiKey,
                'units' => 'metric' // Celsius
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('OpenWeather API request failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('OpenWeather API request exception', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Analyze weather data and create alert if dangerous conditions detected
     * 
     * @param array $weatherData Weather data from API
     * @param string $locationName Location name
     * @param float $lat Latitude
     * @param float $lon Longitude
     * @return Alert|null Created alert or null
     */
    private function analyzeWeatherAndCreateAlert(
        array $weatherData, 
        string $locationName,
        float $lat,
        float $lon
    ): ?Alert {
        $dangers = $this->detectDangerousConditions($weatherData);
        
        if (empty($dangers)) {
            return null;
        }

        // Determine alert type and severity based on detected dangers
        $alertData = $this->determineAlertTypeAndSeverity($dangers, $weatherData);
        
        // Check if similar alert already exists (within last 3 hours)
        $existingAlert = Alert::where('location', $locationName)
            ->where('type', $alertData['type'])
            ->where('status', 'Active')
            ->where('issued_at', '>=', now()->subHours(3))
            ->first();

        if ($existingAlert) {
            Log::info("Similar alert already exists for {$locationName}");
            return null;
        }

        // Get system admin for alert creation
        $systemAdmin = User::where('role', 'admin')->first();
        
        if (!$systemAdmin) {
            Log::error('No admin user found for weather alert creation');
            return null;
        }

        // Create the alert
        $alert = Alert::create([
            'title' => $alertData['title'],
            'description' => $alertData['description'],
            'severity' => $alertData['severity'],
            'type' => $alertData['type'],
            'location' => $locationName,
            'latitude' => $lat,
            'longitude' => $lon,
            'status' => 'Active',
            'issued_at' => now(),
            'expires_at' => now()->addHours(6), // Alert expires in 6 hours
            'created_by' => $systemAdmin->id
        ]);

        return $alert;
    }

    /**
     * Detect dangerous weather conditions
     * 
     * @param array $weatherData Weather data from API
     * @return array Detected dangers
     */
    private function detectDangerousConditions(array $weatherData): array
    {
        $dangers = [];

        // Extract weather parameters
        $temp = $weatherData['main']['temp'] ?? null;
        $humidity = $weatherData['main']['humidity'] ?? null;
        $windSpeed = $weatherData['wind']['speed'] ?? null;
        $rain1h = $weatherData['rain']['1h'] ?? 0;
        $rain3h = $weatherData['rain']['3h'] ?? 0;
        $weatherMain = $weatherData['weather'][0]['main'] ?? '';
        $weatherDesc = $weatherData['weather'][0]['description'] ?? '';

        // Check for cyclone/storm conditions
        if ($windSpeed >= self::DANGER_THRESHOLDS['wind_speed']) {
            $dangers[] = [
                'type' => 'high_wind',
                'value' => $windSpeed,
                'description' => "Very high wind speed: {$windSpeed} m/s"
            ];
        }

        // Check for flood conditions (heavy rain)
        if ($rain1h >= self::DANGER_THRESHOLDS['rain_1h'] || 
            $rain3h >= self::DANGER_THRESHOLDS['rain_3h']) {
            $dangers[] = [
                'type' => 'heavy_rain',
                'value' => max($rain1h, $rain3h),
                'description' => "Heavy rainfall detected: {$rain3h}mm in 3 hours"
            ];
        }

        // Check for heat wave
        if ($temp >= self::DANGER_THRESHOLDS['temp_high']) {
            $dangers[] = [
                'type' => 'heat_wave',
                'value' => $temp,
                'description' => "Extreme heat: {$temp}°C"
            ];
        }

        // Check for cold wave
        if ($temp <= self::DANGER_THRESHOLDS['temp_low']) {
            $dangers[] = [
                'type' => 'cold_wave',
                'value' => $temp,
                'description' => "Extreme cold: {$temp}°C"
            ];
        }

        // Check for thunderstorm
        if (in_array($weatherMain, ['Thunderstorm', 'Squall'])) {
            $dangers[] = [
                'type' => 'thunderstorm',
                'value' => $weatherMain,
                'description' => "Thunderstorm conditions: {$weatherDesc}"
            ];
        }

        // Check for combined heat + humidity (dangerous for health)
        if ($temp >= 35 && $humidity >= self::DANGER_THRESHOLDS['humidity_high']) {
            $dangers[] = [
                'type' => 'heat_humidity',
                'value' => $temp,
                'description' => "Dangerous heat with high humidity: {$temp}°C, {$humidity}%"
            ];
        }

        return $dangers;
    }

    /**
     * Determine alert type and severity based on detected dangers
     * 
     * @param array $dangers Detected dangerous conditions
     * @param array $weatherData Original weather data
     * @return array Alert type, severity, title, description
     */
    private function determineAlertTypeAndSeverity(array $dangers, array $weatherData): array
    {
        $highestSeverity = 'Low';
        $primaryDanger = $dangers[0];
        $alertType = 'Other';
        
        // Map danger types to alert types
        foreach ($dangers as $danger) {
            switch ($danger['type']) {
                case 'high_wind':
                    $alertType = 'Cyclone';
                    $highestSeverity = $danger['value'] >= 30 ? 'Critical' : 'High';
                    break;
                    
                case 'heavy_rain':
                    $alertType = 'Flood';
                    $highestSeverity = $danger['value'] >= 100 ? 'Critical' : 'High';
                    break;
                    
                case 'heat_wave':
                case 'heat_humidity':
                    $alertType = 'Health Emergency';
                    $highestSeverity = $danger['value'] >= 42 ? 'Critical' : 'High';
                    break;
                    
                case 'cold_wave':
                    $alertType = 'Health Emergency';
                    $highestSeverity = $danger['value'] <= 0 ? 'High' : 'Moderate';
                    break;
                    
                case 'thunderstorm':
                    $alertType = 'Other';
                    $highestSeverity = 'Moderate';
                    break;
            }
        }

        // Generate title and description
        $title = $this->generateAlertTitle($alertType, $highestSeverity);
        $description = $this->generateAlertDescription($dangers, $weatherData, $alertType);

        return [
            'type' => $alertType,
            'severity' => $highestSeverity,
            'title' => $title,
            'description' => $description
        ];
    }

    /**
     * Generate alert title
     * 
     * @param string $type Alert type
     * @param string $severity Severity level
     * @return string Alert title
     */
    private function generateAlertTitle(string $type, string $severity): string
    {
        return match($severity) {
            'Critical' => "⚠️ CRITICAL {$type} WARNING",
            'High' => "🚨 {$type} Alert - High Severity",
            'Moderate' => "⚡ {$type} Advisory",
            default => "{$type} Notice"
        };
    }

    /**
     * Generate detailed alert description
     * 
     * @param array $dangers Detected dangers
     * @param array $weatherData Original weather data
     * @param string $alertType Alert type
     * @return string Alert description
     */
    private function generateAlertDescription(array $dangers, array $weatherData, string $alertType): string
    {
        $temp = $weatherData['main']['temp'] ?? 'N/A';
        $humidity = $weatherData['main']['humidity'] ?? 'N/A';
        $windSpeed = $weatherData['wind']['speed'] ?? 'N/A';
        $weatherDesc = $weatherData['weather'][0]['description'] ?? 'Unknown';

        $description = "AI-Generated Weather Alert\n\n";
        $description .= "Current Conditions:\n";
        $description .= "• Weather: " . ucfirst($weatherDesc) . "\n";
        $description .= "• Temperature: {$temp}°C\n";
        $description .= "• Wind Speed: {$windSpeed} m/s\n";
        $description .= "• Humidity: {$humidity}%\n\n";

        $description .= "Detected Dangers:\n";
        foreach ($dangers as $danger) {
            $description .= "• {$danger['description']}\n";
        }

        $description .= "\nRecommended Actions:\n";
        $description .= $this->getRecommendedActions($alertType);

        $description .= "\n\nThis alert was automatically generated based on current weather conditions.";

        return $description;
    }

    /**
     * Get recommended actions based on alert type
     * 
     * @param string $alertType Alert type
     * @return string Recommended actions
     */
    private function getRecommendedActions(string $alertType): string
    {
        return match($alertType) {
            'Cyclone' => "• Stay indoors and secure loose objects\n• Avoid coastal areas\n• Prepare emergency supplies\n• Monitor official weather updates",
            'Flood' => "• Move to higher ground immediately\n• Do not walk or drive through flood water\n• Secure important documents\n• Contact emergency services if trapped",
            'Health Emergency' => "• Stay hydrated\n• Avoid outdoor activities\n• Check on elderly and vulnerable individuals\n• Seek medical help if experiencing symptoms",
            default => "• Stay alert and monitor updates\n• Follow official instructions\n• Keep emergency contacts handy"
        };
    }

    /**
     * Get default locations to monitor (can be expanded)
     * 
     * @return array Default locations
     */
    public static function getDefaultLocations(): array
    {
        return [
            ['name' => 'Dhaka', 'lat' => 23.8103, 'lon' => 90.4125],
            ['name' => 'Chittagong', 'lat' => 22.3569, 'lon' => 91.7832],
            ['name' => 'Khulna', 'lat' => 22.8456, 'lon' => 89.5403],
            ['name' => 'Sylhet', 'lat' => 24.8949, 'lon' => 91.8687],
            ['name' => 'Rajshahi', 'lat' => 24.3745, 'lon' => 88.6042],
        ];
    }
}
