<?php

namespace App\Console\Commands;

use App\Services\WeatherService;
use App\Services\WeatherAlertService;
use Illuminate\Console\Command;

class CheckWeatherAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:check {--location= : Specific location to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check weather conditions and create alerts for dangerous weather';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌦️  Starting weather alert check...');
        $this->newLine();

        // Check if API is configured
        if (!config('weather.api_key')) {
            $this->error('❌ OpenWeather API key not configured!');
            $this->warn('Please add OPENWEATHER_API_KEY to your .env file');
            $this->info('Get your free API key from: https://openweathermap.org/api');
            return Command::FAILURE;
        }

        $weatherService = app(WeatherService::class);
        
        // Display current weather for default location
        $this->displayCurrentWeather($weatherService);
        
        $this->newLine();
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('   CHECKING ALL MONITORED LOCATIONS');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        $locations = config('weather.monitored_locations', []);
        
        if (empty($locations)) {
            $this->warn('⚠️  No locations configured for monitoring');
            return Command::SUCCESS;
        }

        $this->info("📍 Monitoring " . count($locations) . " locations:");
        foreach ($locations as $location) {
            $this->line("   • {$location['name']}");
        }
        
        $this->newLine();
        $this->info('🔍 Analyzing weather conditions...');
        $this->newLine();

        // Use WeatherAlertService to check all locations
        $weatherAlertService = app(WeatherAlertService::class);
        $results = $weatherAlertService->checkWeatherForLocations($locations);

        // Display results
        $this->displayResults($results);

        return empty($results['errors']) ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Display current weather for default location
     *
     * @param WeatherService $weatherService
     * @return void
     */
    protected function displayCurrentWeather(WeatherService $weatherService): void
    {
        $defaultLocation = config('weather.default_location');
        
        $this->info("📊 Current Weather for {$defaultLocation['city']}:");
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $weather = $weatherService->getCurrentWeather(
            $defaultLocation['latitude'],
            $defaultLocation['longitude']
        );

        if (!$weather) {
            $this->warn('⚠️  Unable to fetch weather data');
            return;
        }

        $temp = $weather['temperature']['current'];
        $feelsLike = $weather['temperature']['feels_like'];
        $weatherDesc = ucfirst($weather['weather']['description']);
        $humidity = $weather['conditions']['humidity'];
        $windSpeed = $weather['conditions']['wind_speed'];
        $pressure = $weather['conditions']['pressure'];

        $this->line("   Weather: {$weatherDesc}");
        $this->line("   Temperature: {$temp}°C (Feels like {$feelsLike}°C)");
        $this->line("   Humidity: {$humidity}%");
        $this->line("   Wind Speed: {$windSpeed} m/s");
        $this->line("   Pressure: {$pressure} hPa");

        if (!empty($weather['rain']['1h'])) {
            $this->line("   Rainfall: {$weather['rain']['1h']} mm/h");
        }

        // Check for threats
        $threats = $weatherService->analyzeDangerousConditions($weather);
        
        if (!empty($threats)) {
            $this->newLine();
            $this->warn("⚠️  {" . count($threats) . "} Threat(s) Detected:");
            foreach ($threats as $threat) {
                $severityColor = match($threat['severity']) {
                    'Emergency' => 'error',
                    'Warning' => 'warn',
                    default => 'info'
                };
                $this->$severityColor("   • [{$threat['severity']}] {$threat['description']}");
            }
        } else {
            $this->newLine();
            $this->info('✅ No dangerous conditions detected');
        }
    }

    /**
     * Display check results
     *
     * @param array $results
     * @return void
     */
    protected function displayResults(array $results): void
    {
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('   WEATHER CHECK RESULTS');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        $this->info("✅ Locations Checked: {$results['locations_checked']}");
        
        if ($results['alerts_created'] > 0) {
            $this->warn("⚠️  Alerts Created: {$results['alerts_created']}");
        } else {
            $this->info("✅ Alerts Created: 0 (No dangerous conditions)");
        }

        // Display created alerts
        if (!empty($results['conditions'])) {
            $this->newLine();
            $this->warn("📢 New Weather Alerts:");
            
            foreach ($results['conditions'] as $condition) {
                $this->line("   • {$condition['location']}: {$condition['alert_type']} ({$condition['severity']})");
                $this->line("     {$condition['description']}");
            }
        }

        // Display errors
        if (!empty($results['errors'])) {
            $this->newLine();
            $this->error("❌ Errors Encountered:");
            foreach ($results['errors'] as $error) {
                $this->line("   • {$error}");
            }
        }

        $this->newLine();
        $this->info("🕐 Checked At: {$results['checked_at']}");
        $this->newLine();

        if ($results['alerts_created'] > 0) {
            $this->warn('⚠️  Dangerous weather conditions detected!');
            $this->info('   Check the Alerts page for details and safety recommendations.');
        } else {
            $this->info('✅ All locations safe - No weather alerts needed');
        }
    }
}
