<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS Gateway Configuration for Bangladesh Disaster Management System
    |--------------------------------------------------------------------------
    |
    | This configuration file contains settings for multiple SMS gateways
    | specifically designed for Bangladesh telecom operators.
    |
    */

    'default_provider' => env('SMS_DEFAULT_PROVIDER', 'ssl_wireless'),
    'fallback_provider' => env('SMS_FALLBACK_PROVIDER', 'banglalink'),
    
    // Test phone number for connectivity tests
    'test_number' => env('SMS_TEST_NUMBER', '8801700000000'),

    /*
    |--------------------------------------------------------------------------
    | SSL Wireless SMS Gateway (Primary Provider)
    |--------------------------------------------------------------------------
    |
    | SSL Wireless is one of the most reliable SMS gateways in Bangladesh
    | Often used for government and emergency services
    |
    */
    'ssl_wireless' => [
        'username' => env('SSL_SMS_USERNAME', ''),
        'password' => env('SSL_SMS_PASSWORD', ''),
        'sender_id' => env('SSL_SMS_SENDER_ID', 'DisasterBD'),
        'api_url' => 'https://sms.sslwireless.com/pushapi/dynamic/server.php',
        'enabled' => env('SSL_SMS_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Banglalink SMS Gateway
    |--------------------------------------------------------------------------
    |
    | Banglalink provides SMS gateway services for bulk messaging
    | Good coverage across Bangladesh
    |
    */
    'banglalink' => [
        'user_id' => env('BANGLALINK_SMS_USER_ID', ''),
        'password' => env('BANGLALINK_SMS_PASSWORD', ''),
        'sender_id' => env('BANGLALINK_SMS_SENDER_ID', 'DisasterBD'),
        'api_url' => 'http://www.banglalinksms.com/api/sendSMS',
        'enabled' => env('BANGLALINK_SMS_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Grameenphone SMS Gateway
    |--------------------------------------------------------------------------
    |
    | Grameenphone (GP) - Largest mobile operator in Bangladesh
    | High reliability and coverage
    |
    */
    'grameenphone' => [
        'api_key' => env('GP_SMS_API_KEY', ''),
        'sender_id' => env('GP_SMS_SENDER_ID', 'DisasterBD'),
        'api_url' => 'https://api.grameenphone.com/sms/send',
        'enabled' => env('GP_SMS_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Robi SMS Gateway
    |--------------------------------------------------------------------------
    |
    | Robi provides SMS gateway services
    | Good as fallback option
    |
    */
    'robi' => [
        'username' => env('ROBI_SMS_USERNAME', ''),
        'password' => env('ROBI_SMS_PASSWORD', ''),
        'sender_id' => env('ROBI_SMS_SENDER_ID', 'DisasterBD'),
        'api_url' => 'https://sms.robi.com.bd/api/send',
        'enabled' => env('ROBI_SMS_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Emergency SMS Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for emergency alert messaging
    |
    */
    'emergency' => [
        // Maximum SMS length (characters)
        'max_length' => 160,
        
        // Rate limiting (messages per minute)
        'rate_limit' => 60,
        
        // Retry attempts for failed messages
        'retry_attempts' => 3,
        
        // Delay between retries (seconds)
        'retry_delay' => 30,
        
        // Emergency contact numbers
        'emergency_contacts' => [
            'national_emergency' => '999',
            'police' => '100',
            'fire_service' => '9555555',
            'health_emergency' => '16263',
            'power_emergency' => '16162',
            'water_emergency' => '16163',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Keywords and Responses
    |--------------------------------------------------------------------------
    |
    | Define SMS keywords for automated responses
    |
    */
    'keywords' => [
        'help' => [
            'description' => 'Show available commands',
            'response_template' => 'sms.help'
        ],
        'flood' => [
            'description' => 'Get flood alerts',
            'response_template' => 'sms.flood_alerts'
        ],
        'status' => [
            'description' => 'Check request status',
            'response_template' => 'sms.status'
        ],
        'shelter' => [
            'description' => 'Find nearest shelters',
            'response_template' => 'sms.shelters'
        ],
        'emergency' => [
            'description' => 'Emergency contact numbers',
            'response_template' => 'sms.emergency_contacts'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Templates
    |--------------------------------------------------------------------------
    |
    | Pre-defined message templates for different alert types
    |
    */
    'templates' => [
        'flood_alert' => '🌊 FLOOD ALERT: {message} in {location}. Severity: {severity}. Stay safe! - Disaster Alert BD',
        'fire_alert' => '🔥 FIRE ALERT: {message} in {location}. Evacuate if necessary. Call 9555555. - Disaster Alert BD',
        'earthquake_alert' => '🏠 EARTHQUAKE ALERT: {message}. Magnitude: {severity}. Take cover! - Disaster Alert BD',
        'cyclone_alert' => '🌪️ CYCLONE ALERT: {message} approaching {location}. Prepare for evacuation. - Disaster Alert BD',
        'shelter_assignment' => '🏠 SHELTER ASSIGNED: {shelter_name}, {shelter_address}. Contact: {contact}. Report ASAP. - Disaster Alert BD',
        'request_received' => '✅ REQUEST RECEIVED: Your emergency request #{request_id} has been received. Help is on the way. - Disaster Alert BD',
        'status_update' => '📋 STATUS UPDATE: Request #{request_id} status changed to {status}. {additional_info} - Disaster Alert BD',
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configure webhook endpoints for receiving SMS responses
    |
    */
    'webhook' => [
        'enabled' => env('SMS_WEBHOOK_ENABLED', true),
        'url' => env('SMS_WEBHOOK_URL', '/api/sms/webhook'),
        'secret' => env('SMS_WEBHOOK_SECRET', ''),
        'verify_signature' => env('SMS_WEBHOOK_VERIFY_SIGNATURE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Development/Testing Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for development and testing environments
    |
    */
    'testing' => [
        // Disable actual SMS sending in testing
        'fake_sms' => env('SMS_FAKE', false),
        
        // Log all SMS instead of sending
        'log_only' => env('SMS_LOG_ONLY', false),
        
        // Test phone numbers that won't be charged
        'test_numbers' => [
            '8801700000000',
            '8801800000000',
            '8801900000000',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Bangladesh Specific Settings
    |--------------------------------------------------------------------------
    |
    | Configuration specific to Bangladesh telecommunications
    |
    */
    'bangladesh' => [
        // Country code
        'country_code' => '880',
        
        // Mobile operator prefixes
        'operator_prefixes' => [
            'grameenphone' => ['017', '013'],
            'banglalink' => ['019', '014'],
            'robi' => ['018'],
            'airtel' => ['016'],
            'teletalk' => ['015'],
        ],
        
        // Government approved sender IDs
        'approved_sender_ids' => [
            'DisasterBD',
            'EmergencyBD',
            'AlertBD',
            'DMB-GOV',
        ],
    ],
];