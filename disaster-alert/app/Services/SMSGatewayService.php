<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * SMS Gateway Service for Bangladesh Disaster Management System
 * 
 * Supports multiple Bangladesh telecom providers:
 * - SSL Wireless (Primary)
 * - Banglalink
 * - Grameenphone
 * - Robi
 * 
 * This service enables emergency communication via SMS when internet is unavailable
 */
class SMSGatewayService
{
    private $config;
    private $provider;

    public function __construct()
    {
        $this->config = config('sms');
        $this->provider = $this->config['default_provider'] ?? 'ssl_wireless';
    }

    /**
     * Send emergency alert via SMS
     */
    public function sendEmergencyAlert(string $phoneNumber, string $message, string $severity = 'high'): bool
    {
        try {
            // Format phone number for Bangladesh
            $formattedNumber = $this->formatBangladeshPhoneNumber($phoneNumber);
            
            // Add emergency prefix based on severity
            $priorityMessage = $this->formatEmergencyMessage($message, $severity);
            
            // Send via primary provider
            $result = $this->sendSMS($formattedNumber, $priorityMessage);
            
            if (!$result) {
                // Fallback to secondary provider
                $this->provider = $this->config['fallback_provider'] ?? 'banglalink';
                $result = $this->sendSMS($formattedNumber, $priorityMessage);
            }
            
            // Log the SMS attempt
            Log::info('Emergency SMS sent', [
                'phone' => $formattedNumber,
                'severity' => $severity,
                'provider' => $this->provider,
                'success' => $result
            ]);
            
            return $result;
            
        } catch (Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send bulk emergency alerts to multiple numbers
     */
    public function sendBulkEmergencyAlert(array $phoneNumbers, string $message, string $severity = 'high'): array
    {
        $results = [];
        
        foreach ($phoneNumbers as $number) {
            $results[$number] = $this->sendEmergencyAlert($number, $message, $severity);
            
            // Small delay to prevent rate limiting
            usleep(500000); // 0.5 seconds
        }
        
        return $results;
    }

    /**
     * Handle incoming SMS keywords
     */
    public function handleIncomingSMS(string $phoneNumber, string $message): string
    {
        $keyword = strtoupper(trim($message));
        $response = '';

        switch ($keyword) {
            case 'HELP':
                $response = $this->getHelpResponse($phoneNumber);
                break;
                
            case 'FLOOD':
                $response = $this->getFloodAlerts();
                break;
                
            case 'STATUS':
                $response = $this->getRequestStatus($phoneNumber);
                break;
                
            case 'SHELTER':
                $response = $this->getNearestShelters($phoneNumber);
                break;
                
            case 'EMERGENCY':
                $response = $this->getEmergencyContacts();
                break;
                
            default:
                $response = $this->getDefaultResponse();
        }

        // Send response back
        $this->sendSMS($phoneNumber, $response);
        
        return $response;
    }

    /**
     * Send SMS via configured provider
     */
    private function sendSMS(string $phoneNumber, string $message): bool
    {
        switch ($this->provider) {
            case 'ssl_wireless':
                return $this->sendViaSSLWireless($phoneNumber, $message);
                
            case 'banglalink':
                return $this->sendViaBanglalink($phoneNumber, $message);
                
            case 'grameenphone':
                return $this->sendViaGrameenphone($phoneNumber, $message);
                
            case 'robi':
                return $this->sendViaRobi($phoneNumber, $message);
                
            default:
                return $this->sendViaSSLWireless($phoneNumber, $message);
        }
    }

    /**
     * SSL Wireless SMS Gateway (Primary for Bangladesh)
     */
    private function sendViaSSLWireless(string $phoneNumber, string $message): bool
    {
        try {
            $response = Http::timeout(30)->post('https://sms.sslwireless.com/pushapi/dynamic/server.php', [
                'user' => $this->config['ssl_wireless']['username'],
                'pass' => $this->config['ssl_wireless']['password'],
                'sid' => $this->config['ssl_wireless']['sender_id'],
                'sms' => $message,
                'msisdn' => $phoneNumber,
                'csmsid' => uniqid() // Unique SMS ID
            ]);

            return $response->successful() && str_contains($response->body(), 'ACCEPTD');
            
        } catch (Exception $e) {
            Log::error('SSL Wireless SMS failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Banglalink SMS Gateway
     */
    private function sendViaBanglalink(string $phoneNumber, string $message): bool
    {
        try {
            $response = Http::timeout(30)->post('http://www.banglalinksms.com/api/sendSMS', [
                'userID' => $this->config['banglalink']['user_id'],
                'passwd' => $this->config['banglalink']['password'],
                'sender' => $this->config['banglalink']['sender_id'],
                'message' => $message,
                'msisdn' => $phoneNumber
            ]);

            return $response->successful();
            
        } catch (Exception $e) {
            Log::error('Banglalink SMS failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Grameenphone SMS Gateway
     */
    private function sendViaGrameenphone(string $phoneNumber, string $message): bool
    {
        try {
            $response = Http::timeout(30)->post('https://api.grameenphone.com/sms/send', [
                'api_key' => $this->config['grameenphone']['api_key'],
                'sender_id' => $this->config['grameenphone']['sender_id'],
                'message' => $message,
                'recipient' => $phoneNumber
            ]);

            return $response->successful();
            
        } catch (Exception $e) {
            Log::error('Grameenphone SMS failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Robi SMS Gateway
     */
    private function sendViaRobi(string $phoneNumber, string $message): bool
    {
        try {
            $response = Http::timeout(30)->post('https://sms.robi.com.bd/api/send', [
                'username' => $this->config['robi']['username'],
                'password' => $this->config['robi']['password'],
                'from' => $this->config['robi']['sender_id'],
                'to' => $phoneNumber,
                'text' => $message
            ]);

            return $response->successful();
            
        } catch (Exception $e) {
            Log::error('Robi SMS failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format Bangladesh phone number
     */
    private function formatBangladeshPhoneNumber(string $phoneNumber): string
    {
        // Remove all non-digits
        $number = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Add Bangladesh country code if missing
        if (strlen($number) == 11 && substr($number, 0, 2) == '01') {
            $number = '880' . substr($number, 1);
        } elseif (strlen($number) == 10 && substr($number, 0, 1) == '1') {
            $number = '880' . $number;
        } elseif (!str_starts_with($number, '880')) {
            $number = '880' . $number;
        }
        
        return $number;
    }

    /**
     * Format emergency message with priority
     */
    private function formatEmergencyMessage(string $message, string $severity): string
    {
        $prefixes = [
            'critical' => '🚨 CRITICAL EMERGENCY: ',
            'high' => '⚠️ URGENT ALERT: ',
            'medium' => '📢 IMPORTANT: ',
            'low' => 'ℹ️ NOTICE: '
        ];
        
        $prefix = $prefixes[strtolower($severity)] ?? $prefixes['medium'];
        
        // Truncate message to SMS limit (160 characters)
        $maxLength = 160 - strlen($prefix) - 20; // Reserve space for source
        $truncatedMessage = substr($message, 0, $maxLength);
        
        return $prefix . $truncatedMessage . "\n\n- Bangladesh Disaster Alert";
    }

    /**
     * Get help response for SMS
     */
    private function getHelpResponse(string $phoneNumber): string
    {
        return "🆘 EMERGENCY HELP\n\nSMS Commands:\nHELP - This menu\nFLOOD - Flood alerts\nSHELTER - Find shelters\nSTATUS - Request status\nEMERGENCY - Emergency contacts\n\nCall 999 for immediate help\n\n- Disaster Alert System";
    }

    /**
     * Get current flood alerts
     */
    private function getFloodAlerts(): string
    {
        // Get latest flood alerts from database
        $alerts = \App\Models\Alert::where('type', 'Flood')
            ->where('status', 'active')
            ->orderBy('severity')
            ->limit(3)
            ->get(['title', 'location', 'severity'])
            ->map(function($alert) {
                return "{$alert->severity}: {$alert->title} - {$alert->location}";
            })
            ->toArray();

        if (empty($alerts)) {
            return "✅ No active flood alerts.\nStay safe and monitor updates.\n\n- Disaster Alert";
        }

        return "🌊 FLOOD ALERTS:\n\n" . implode("\n", $alerts) . "\n\nStay safe!\n- Disaster Alert";
    }

    /**
     * Get request status
     */
    private function getRequestStatus(string $phoneNumber): string
    {
        // Format phone number for search
        $formattedNumber = $this->formatBangladeshPhoneNumber($phoneNumber);
        
        // Find latest request by phone number
        $request = \App\Models\Request::where('phone', 'LIKE', '%' . substr($formattedNumber, -10))
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$request) {
            return "❓ No emergency request found for this number.\n\nTo submit: Visit our website or call 999\n\n- Disaster Alert";
        }

        return "📋 REQUEST STATUS\n\nID: #{$request->id}\nType: {$request->request_type}\nStatus: {$request->status}\nSubmitted: " . $request->created_at->format('M j, g:i A') . "\n\n- Disaster Alert";
    }

    /**
     * Get nearest shelters
     */
    private function getNearestShelters(string $phoneNumber): string
    {
        // Get available shelters
        $shelters = \App\Models\Shelter::where('is_active', true)
            ->where('current_occupancy', '<', \Illuminate\Support\Facades\DB::raw('capacity'))
            ->limit(3)
            ->get(['name', 'city', 'contact_phone'])
            ->map(function($shelter) {
                return "{$shelter->name}, {$shelter->city}" . ($shelter->contact_phone ? " - {$shelter->contact_phone}" : "");
            })
            ->toArray();

        if (empty($shelters)) {
            return "🏠 No available shelters found.\n\nContact local authorities:\nCall 999 for assistance\n\n- Disaster Alert";
        }

        return "🏠 AVAILABLE SHELTERS:\n\n" . implode("\n", $shelters) . "\n\nContact directly or call 999\n- Disaster Alert";
    }

    /**
     * Get emergency contacts
     */
    private function getEmergencyContacts(): string
    {
        return "🚨 EMERGENCY CONTACTS\n\n🚑 National Emergency: 999\n🚓 Police: 100\n🔥 Fire Service: 9555555\n⚡ Power: 16162\n💧 Water: 16163\n🏥 Health: 16263\n\nText HELP for more options\n\n- Disaster Alert";
    }

    /**
     * Get default response for unknown commands
     */
    private function getDefaultResponse(): string
    {
        return "❓ Unknown command.\n\nAvailable commands:\nHELP, FLOOD, SHELTER, STATUS, EMERGENCY\n\nSend HELP for full menu\nCall 999 for immediate assistance\n\n- Disaster Alert";
    }

    /**
     * Test SMS connectivity
     */
    public function testConnection(): array
    {
        $results = [];
        
        // Test each provider
        foreach (['ssl_wireless', 'banglalink', 'grameenphone', 'robi'] as $provider) {
            $this->provider = $provider;
            
            try {
                // Send test SMS to configured test number
                $testNumber = $this->config['test_number'] ?? '8801700000000';
                $testMessage = 'Test message from Disaster Alert System - ' . now()->format('Y-m-d H:i:s');
                
                $result = $this->sendSMS($testNumber, $testMessage);
                $results[$provider] = $result;
                
            } catch (Exception $e) {
                $results[$provider] = false;
                Log::error("SMS test failed for {$provider}: " . $e->getMessage());
            }
        }
        
        return $results;
    }
}