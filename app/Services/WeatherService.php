<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    protected $apiKey;
    protected $apiUrl;
    protected $cacheDuration;

    public function __construct()
    {
        $this->apiKey = config('weather.api_key');
        $this->apiUrl = config('weather.api_url');
        $this->cacheDuration = config('weather.cache_duration', 1800);
    }

    /**
     * Get current weather for a location
     *
     * @param float $latitude
     * @param float $longitude
     * @return array|null
     */
    public function getCurrentWeather(float $latitude, float $longitude): ?array
    {
        try {
            $cacheKey = "weather_current_{$latitude}_{$longitude}";

            return Cache::remember($cacheKey, $this->cacheDuration, function () use ($latitude, $longitude) {
                $response = Http::get("{$this->apiUrl}/weather", [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'appid' => $this->apiKey,
                    'units' => 'metric', // Celsius
                ]);

                if ($response->successful()) {
                    return $this->parseWeatherData($response->json());
                }

                Log::error('Weather API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return null;
            });
        } catch (\Exception $e) {
            Log::error('Weather service error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get weather forecast (5 day / 3 hour)
     *
     * @param float $latitude
     * @param float $longitude
     * @return array|null
     */
    public function getForecast(float $latitude, float $longitude): ?array
    {
        try {
            $cacheKey = "weather_forecast_{$latitude}_{$longitude}";

            return Cache::remember($cacheKey, $this->cacheDuration, function () use ($latitude, $longitude) {
                $response = Http::get("{$this->apiUrl}/forecast", [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'appid' => $this->apiKey,
                    'units' => 'metric',
                ]);

                if ($response->successful()) {
                    return $response->json();
                }

                return null;
            });
        } catch (\Exception $e) {
            Log::error('Weather forecast error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Parse weather data into standardized format
     *
     * @param array $data
     * @return array
     */
    protected function parseWeatherData(array $data): array
    {
        return [
            'location' => [
                'name' => $data['name'] ?? 'Unknown',
                'country' => $data['sys']['country'] ?? '',
                'latitude' => $data['coord']['lat'] ?? 0,
                'longitude' => $data['coord']['lon'] ?? 0,
            ],
            'weather' => [
                'main' => $data['weather'][0]['main'] ?? 'Unknown',
                'description' => $data['weather'][0]['description'] ?? '',
                'icon' => $data['weather'][0]['icon'] ?? '',
            ],
            'temperature' => [
                'current' => $data['main']['temp'] ?? 0,
                'feels_like' => $data['main']['feels_like'] ?? 0,
                'min' => $data['main']['temp_min'] ?? 0,
                'max' => $data['main']['temp_max'] ?? 0,
            ],
            'conditions' => [
                'pressure' => $data['main']['pressure'] ?? 0, // hPa
                'humidity' => $data['main']['humidity'] ?? 0,  // %
                'visibility' => $data['visibility'] ?? 10000,  // meters
                'wind_speed' => $data['wind']['speed'] ?? 0,   // m/s
                'wind_direction' => $data['wind']['deg'] ?? 0, // degrees
                'clouds' => $data['clouds']['all'] ?? 0,       // %
            ],
            'rain' => [
                '1h' => $data['rain']['1h'] ?? 0,  // mm
                '3h' => $data['rain']['3h'] ?? 0,
            ],
            'timestamp' => $data['dt'] ?? time(),
            'sunrise' => $data['sys']['sunrise'] ?? 0,
            'sunset' => $data['sys']['sunset'] ?? 0,
        ];
    }

    /**
     * Analyze weather data for dangerous conditions
     *
     * @param array $weatherData
     * @return array Detected threats
     */
    public function analyzeDangerousConditions(array $weatherData): array
    {
        $threats = [];
        $thresholds = config('weather.thresholds');

        // Check temperature extremes
        $temp = $weatherData['temperature']['current'];
        if ($temp >= $thresholds['extreme_heat']) {
            $threats[] = [
                'type' => 'Heat Wave',
                'severity' => 'Warning',
                'description' => "Extreme heat detected: {$temp}°C",
                'recommendation' => 'Stay hydrated, avoid outdoor activities during peak hours.',
            ];
        } elseif ($temp <= $thresholds['extreme_cold']) {
            $threats[] = [
                'type' => 'Cold Wave',
                'severity' => 'Warning',
                'description' => "Extreme cold detected: {$temp}°C",
                'recommendation' => 'Wear warm clothing, stay indoors if possible.',
            ];
        }

        // Check wind speed
        $windSpeed = $weatherData['conditions']['wind_speed'];
        if ($windSpeed >= $thresholds['storm_wind']) {
            $threats[] = [
                'type' => 'Storm',
                'severity' => 'Emergency',
                'description' => "Cyclone/Storm detected: Wind speed {$windSpeed} m/s",
                'recommendation' => 'Seek immediate shelter. Avoid going outside. Secure loose objects.',
            ];
        } elseif ($windSpeed >= $thresholds['strong_wind']) {
            $threats[] = [
                'type' => 'Storm',
                'severity' => 'Warning',
                'description' => "Strong winds detected: {$windSpeed} m/s",
                'recommendation' => 'Secure outdoor items. Avoid high-rise areas.',
            ];
        }

        // Check rainfall
        $rain1h = $weatherData['rain']['1h'] ?? 0;
        if ($rain1h >= $thresholds['extreme_rain']) {
            $threats[] = [
                'type' => 'Flood',
                'severity' => 'Emergency',
                'description' => "Extreme rainfall detected: {$rain1h} mm/h - Flood risk",
                'recommendation' => 'Move to higher ground. Avoid flooded areas. Do not drive through water.',
            ];
        } elseif ($rain1h >= $thresholds['heavy_rain']) {
            $threats[] = [
                'type' => 'Flood',
                'severity' => 'Warning',
                'description' => "Heavy rainfall detected: {$rain1h} mm/h",
                'recommendation' => 'Stay alert for potential flooding. Avoid low-lying areas.',
            ];
        }

        // Check pressure (low pressure = cyclone indicator)
        $pressure = $weatherData['conditions']['pressure'];
        if ($pressure < $thresholds['low_pressure']) {
            $threats[] = [
                'type' => 'Storm',
                'severity' => 'Warning',
                'description' => "Low atmospheric pressure detected: {$pressure} hPa - Cyclone risk",
                'recommendation' => 'Monitor weather updates. Prepare emergency supplies.',
            ];
        }

        // Check visibility
        $visibility = $weatherData['conditions']['visibility'];
        if ($visibility < $thresholds['poor_visibility']) {
            $threats[] = [
                'type' => 'Other',
                'severity' => 'Advisory',
                'description' => "Poor visibility: {$visibility}m - Fog/Haze",
                'recommendation' => 'Reduce travel speed. Use headlights. Avoid non-essential travel.',
            ];
        }

        // Check weather main condition
        $weatherMain = $weatherData['weather']['main'];
        $disasterMapping = config('weather.disaster_mapping');

        if (isset($disasterMapping[$weatherMain]) && $disasterMapping[$weatherMain]) {
            // Additional threat based on weather condition
            if (!collect($threats)->where('type', $disasterMapping[$weatherMain])->count()) {
                $threats[] = [
                    'type' => $disasterMapping[$weatherMain],
                    'severity' => 'Advisory',
                    'description' => "{$weatherMain} conditions detected: {$weatherData['weather']['description']}",
                    'recommendation' => 'Stay informed. Follow local weather advisories.',
                ];
            }
        }

        return $threats;
    }

    /**
     * Get weather icon URL
     *
     * @param string $iconCode
     * @return string
     */
    public function getWeatherIconUrl(string $iconCode): string
    {
        return "https://openweathermap.org/img/wn/{$iconCode}@2x.png";
    }

    /**
     * Check if API key is configured
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
