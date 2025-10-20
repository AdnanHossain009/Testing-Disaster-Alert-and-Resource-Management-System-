<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OpenWeatherMap API Configuration
    |--------------------------------------------------------------------------
    |
    | Get your free API key from: https://openweathermap.org/api
    | Free tier: 1,000 API calls/day, 60 calls/minute
    |
    */

    'api_key' => env('OPENWEATHER_API_KEY', ''),

    'api_url' => 'https://api.openweathermap.org/data/2.5',

    /*
    |--------------------------------------------------------------------------
    | Default Location (Dhaka, Bangladesh)
    |--------------------------------------------------------------------------
    */

    'default_location' => [
        'latitude' => env('WEATHER_DEFAULT_LAT', 23.8103),
        'longitude' => env('WEATHER_DEFAULT_LON', 90.4125),
        'city' => env('WEATHER_DEFAULT_CITY', 'Dhaka'),
        'country' => env('WEATHER_DEFAULT_COUNTRY', 'BD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Weather Alert Thresholds
    |--------------------------------------------------------------------------
    |
    | Define dangerous weather conditions that should trigger alerts
    |
    */

    'thresholds' => [
        // Temperature (Celsius)
        'extreme_heat' => 40, // Heat wave
        'extreme_cold' => 5,  // Cold wave

        // Wind speed (m/s)
        'strong_wind' => 15,   // Strong wind
        'storm_wind' => 25,    // Storm/Cyclone

        // Rain (mm per hour)
        'heavy_rain' => 7.5,   // Heavy rain
        'extreme_rain' => 16,  // Extreme rain/flood risk

        // Humidity (%)
        'high_humidity' => 90,

        // Pressure (hPa)
        'low_pressure' => 1000, // Cyclone indicator

        // Visibility (meters)
        'poor_visibility' => 1000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Weather Condition Mappings to Disaster Types
    |--------------------------------------------------------------------------
    */

    'disaster_mapping' => [
        'Thunderstorm' => 'Storm',
        'Drizzle' => 'Flood',
        'Rain' => 'Flood',
        'Snow' => 'Cold Wave',
        'Atmosphere' => 'Other', // Mist, Smoke, Haze, Dust, Fog, Sand, Ash, Squall, Tornado
        'Clear' => null,
        'Clouds' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Alert Severity Levels
    |--------------------------------------------------------------------------
    */

    'severity_levels' => [
        'Advisory' => 1,     // Minor weather event
        'Watch' => 2,        // Potentially dangerous
        'Warning' => 3,      // Dangerous conditions
        'Emergency' => 4,    // Severe/life-threatening
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    */

    'cache_duration' => 1800, // Cache weather data for 30 minutes (1800 seconds)

    /*
    |--------------------------------------------------------------------------
    | Multiple Locations to Monitor
    |--------------------------------------------------------------------------
    |
    | Strategic locations including disaster-prone areas in Bangladesh:
    | - Coastal areas: Cyclone & flood risk (Cox's Bazar, Khulna, Barisal)
    | - Flood-prone: Sylhet, Sunamganj (heavy monsoon rainfall)
    | - Heat-prone: Rajshahi, Jessore (extreme temperatures)
    | - Major cities: Dhaka, Chittagong (population centers)
    |
    */

    'monitored_locations' => [
        // Major Cities
        [
            'name' => 'Dhaka',
            'latitude' => 23.8103,
            'longitude' => 90.4125,
            'risk_type' => 'Urban flooding, Heat waves',
        ],
        [
            'name' => 'Chittagong',
            'latitude' => 22.3569,
            'longitude' => 91.7832,
            'risk_type' => 'Cyclone, Heavy rainfall',
        ],
        
        // Coastal Areas (High Cyclone Risk)
        [
            'name' => 'Cox\'s Bazar',
            'latitude' => 21.4272,
            'longitude' => 92.0058,
            'risk_type' => 'Cyclone, Storm surge, Coastal flooding',
        ],
        [
            'name' => 'Khulna',
            'latitude' => 22.8456,
            'longitude' => 89.5403,
            'risk_type' => 'Cyclone, Flood, Salinity',
        ],
        [
            'name' => 'Barisal',
            'latitude' => 22.7010,
            'longitude' => 90.3535,
            'risk_type' => 'Cyclone, Flood, River erosion',
        ],
        
        // Flood-Prone Areas
        [
            'name' => 'Sylhet',
            'latitude' => 24.8949,
            'longitude' => 91.8687,
            'risk_type' => 'Flash floods, Heavy monsoon rain',
        ],
        [
            'name' => 'Sunamganj',
            'latitude' => 25.0658,
            'longitude' => 91.3950,
            'risk_type' => 'Flash floods, Riverine floods',
        ],
        [
            'name' => 'Kurigram',
            'latitude' => 25.8073,
            'longitude' => 89.6360,
            'risk_type' => 'River flooding, Erosion',
        ],
        
        // Heat-Prone & Drought Areas
        [
            'name' => 'Rajshahi',
            'latitude' => 24.3745,
            'longitude' => 88.6042,
            'risk_type' => 'Heat waves, Drought',
        ],
        [
            'name' => 'Jessore',
            'latitude' => 23.1697,
            'longitude' => 89.2131,
            'risk_type' => 'Heat waves, Water scarcity',
        ],
        
        // Additional Strategic Locations
        [
            'name' => 'Rangpur',
            'latitude' => 25.7439,
            'longitude' => 89.2752,
            'risk_type' => 'Flood, Cold waves',
        ],
        [
            'name' => 'Mymensingh',
            'latitude' => 24.7471,
            'longitude' => 90.4203,
            'risk_type' => 'Flood, Heavy rainfall',
        ],
        [
            'name' => 'Patuakhali',
            'latitude' => 22.3596,
            'longitude' => 90.3298,
            'risk_type' => 'Cyclone, Storm surge',
        ],
        [
            'name' => 'Satkhira',
            'latitude' => 22.7185,
            'longitude' => 89.0705,
            'risk_type' => 'Cyclone, Salinity intrusion',
        ],
        [
            'name' => 'Bhola',
            'latitude' => 22.6859,
            'longitude' => 90.6482,
            'risk_type' => 'Cyclone, Flood, Erosion',
        ],
    ],

];
