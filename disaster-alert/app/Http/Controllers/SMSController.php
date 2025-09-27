<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SMSGatewayService;
use App\Models\Alert;
use App\Models\Shelter;
use App\Models\Request as EmergencyRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

// Just a demo to check if smscontroller will work or not
/**
 * SMS Controller for Bangladesh Disaster Management System
 * 
 * it will handle : just a Demo to check
 * - Incoming SMS webhooks
 * - SMS keyword processing
 * - Emergency alerts via SMS
 * - Bulk SMS notifications
 */

class SMSController extends Controller
{
    protected $smsService;

    public function __construct(SMSGatewayService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Handle incoming SMS webhook
     * Route: POST /api/sms/webhook
     */

    public function webhook(Request $request)
    {
        try {

            // Log incoming webhook
            Log::info('SMS Webhook received', $request->all());

            // Verify webhook signature if enabled
            if (config('sms.webhook.verify_signature')) {
                if (!$this->verifyWebhookSignature($request)) {
                    return response()->json(['error' => 'Invalid signature'], 401);
                }
            }

            // Extract SMS data (format may vary by provider)
            $phoneNumber = $request->input('from') ?? $request->input('msisdn') ?? $request->input('sender');
            $message = $request->input('text') ?? $request->input('message') ?? $request->input('sms');
            $provider = $request->input('provider', 'unknown');

            if (!$phoneNumber || !$message) {
                return response()->json(['error' => 'Missing required fields'], 400);
            }

            // Rate limiting to prevent spam
            $key = 'sms:' . $phoneNumber;
            if (RateLimiter::tooManyAttempts($key, 5)) {
                Log::warning('SMS rate limit exceeded', ['phone' => $phoneNumber]);
                return response()->json(['error' => 'Rate limit exceeded'], 429);
            }
            RateLimiter::hit($key, 60); // 5 attempts per minute

            // Process the incoming SMS
            $response = $this->smsService->handleIncomingSMS($phoneNumber, $message);

            // Log the interaction
            Log::info('SMS processed', [
                'phone' => $phoneNumber,
                'message' => $message,
                'response' => $response,
                'provider' => $provider
            ]);

            return response()->json(['status' => 'success', 'response' => $response]);

        } catch (\Exception $e) {
            Log::error('SMS webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Send emergency alert to multiple recipients
     * Route: POST /admin/sms/send-alert
     */
    public function sendEmergencyAlert(Request $request)
    {
        $request->validate([
            'alert_id' => 'required|exists:alerts,id',
            'phone_numbers' => 'required|array',
            'phone_numbers.*' => 'required|string',
            'severity' => 'required|in:low,medium,high,critical'
        ]);

        try {
            // Get alert details
            $alert = Alert::findOrFail($request->alert_id);
            
            // Prepare SMS message
            $message = $this->formatAlertMessage($alert);
            
            // Send bulk SMS
            $results = $this->smsService->sendBulkEmergencyAlert(
                $request->phone_numbers,
                $message,
                $request->severity
            );

            // Count successful sends
            $successCount = count(array_filter($results));
            $totalCount = count($results);

            Log::info('Bulk emergency SMS sent', [
                'alert_id' => $alert->id,
                'success_count' => $successCount,
                'total_count' => $totalCount
            ]);

            return response()->json([
                'status' => 'success',
                'message' => "SMS sent to {$successCount} of {$totalCount} recipients",
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Emergency SMS sending failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send emergency SMS'], 500);
        }
    }

    /**
     * Send shelter assignment notification
     * Route: POST /admin/sms/shelter-assignment
     */

    public function sendShelterAssignment(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:requests,id',
            'shelter_id' => 'required|exists:shelters,id',
            'phone_number' => 'required|string'
        ]);

        try {
            $emergencyRequest = EmergencyRequest::findOrFail($request->request_id);
            $shelter = Shelter::findOrFail($request->shelter_id);

            // Prepare assignment message
            $message = "🏠 SHELTER ASSIGNED\n\n" .
                      "Request #{$emergencyRequest->id}\n" .
                      "Shelter: {$shelter->name}\n" .
                      "Address: {$shelter->address}, {$shelter->city}\n" .
                      "Contact: {$shelter->contact_phone}\n\n" .
                      "Please report to the shelter immediately.\n\n" .
                      "- Bangladesh Disaster Management";

            // Send SMS
            $success = $this->smsService->sendEmergencyAlert(
                $request->phone_number,
                $message,
                'high'
            );

            if ($success) {
                Log::info('Shelter assignment SMS sent', [
                    'request_id' => $emergencyRequest->id,
                    'shelter_id' => $shelter->id,
                    'phone' => $request->phone_number
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Shelter assignment notification sent successfully'
                ]);
            } else {
                return response()->json(['error' => 'Failed to send SMS'], 500);
            }

        } catch (\Exception $e) {
            Log::error('Shelter assignment SMS failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send shelter assignment SMS'], 500);
        }
    }

    /**
     * Send status update to citizen
     * Route: POST /admin/sms/status-update
     */
    public function sendStatusUpdate(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:requests,id',
            'status' => 'required|string',
            'additional_info' => 'nullable|string'
        ]);

        try {
            $emergencyRequest = EmergencyRequest::findOrFail($request->request_id);
            
            $message = "📋 STATUS UPDATE\n\n" .
                      "Request #{$emergencyRequest->id}\n" .
                      "New Status: {$request->status}\n";
                      
            if ($request->additional_info) {
                $message .= "\nInfo: {$request->additional_info}\n";
            }
            
            $message .= "\nFor questions, call 999\n\n- Bangladesh Disaster Management";

            // Send SMS
            $success = $this->smsService->sendEmergencyAlert(
                $emergencyRequest->phone,
                $message,
                'medium'
            );

            if ($success) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Status update sent successfully'
                ]);
            } else {
                return response()->json(['error' => 'Failed to send SMS'], 500);
            }

        } catch (\Exception $e) {
            Log::error('Status update SMS failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send status update'], 500);
        }
    }

    /**
     * Test SMS connectivity
     * Route: GET /admin/sms/test
     */
    public function testConnectivity()
    {
        try {
            $results = $this->smsService->testConnection();
            
            return response()->json([
                'status' => 'success',
                'message' => 'SMS connectivity test completed',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('SMS connectivity test failed: ' . $e->getMessage());
            return response()->json(['error' => 'Connectivity test failed'], 500);
        }
    }

    /**
     * Get SMS statistics
     * Route: GET /admin/sms/statistics
     */
    public function getStatistics()
    {
        try {
            // Get SMS stats from logs (you might want to implement proper tracking)
            $stats = [
                'total_sent_today' => $this->getSMSCountToday(),
                'emergency_alerts_sent' => $this->getEmergencyAlertsCount(),
                'keyword_responses_sent' => $this->getKeywordResponsesCount(),
                'failed_attempts' => $this->getFailedAttemptsCount(),
                'active_providers' => $this->getActiveProviders(),
            ];

            return response()->json([
                'status' => 'success',
                'statistics' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('SMS statistics error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get statistics'], 500);
        }
    }

    /**
     * Manual SMS sending interface
     * Route: POST /admin/sms/send-manual
     */
    public function sendManualSMS(Request $request)
    {
        $request->validate([
            'phone_numbers' => 'required|array',
            'phone_numbers.*' => 'required|string',
            'message' => 'required|string|max:160',
            'severity' => 'required|in:low,medium,high,critical'
        ]);

        try {
            $results = $this->smsService->sendBulkEmergencyAlert(
                $request->phone_numbers,
                $request->message,
                $request->severity
            );

            $successCount = count(array_filter($results));
            $totalCount = count($results);

            return response()->json([
                'status' => 'success',
                'message' => "Manual SMS sent to {$successCount} of {$totalCount} recipients",
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Manual SMS sending failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send manual SMS'], 500);
        }
    }

    /**
     * Verify webhook signature
     */
    private function verifyWebhookSignature(Request $request): bool
    {
        $providedSignature = $request->header('X-SMS-Signature') ?? $request->input('signature');
        $secret = config('sms.webhook.secret');
        
        if (!$secret || !$providedSignature) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $request->getContent(), $secret);
        
        return hash_equals($expectedSignature, $providedSignature);
    }

    /**
     * Format alert message for SMS
     */
    private function formatAlertMessage(Alert $alert): string
    {
        $typeEmojis = [
            'Flood' => '🌊',
            'Fire' => '🔥',
            'Earthquake' => '🏠',
            'Cyclone' => '🌪️',
            'Health Emergency' => '🏥',
            'Other' => '⚠️'
        ];

        $emoji = $typeEmojis[$alert->type] ?? '⚠️';
        
        return "{$emoji} {$alert->type} ALERT\n\n" .
               "{$alert->title}\n" .
               "Location: {$alert->location}\n" .
               "Severity: {$alert->severity}\n\n" .
               "Stay safe and follow instructions.\n\n" .
               "- Bangladesh Disaster Management";
    }

    // Get SMS count sent today (placeholder)
    private function getSMSCountToday(): int
    {
        // Implement based on your SMS tracking table or logs
        return 0;
    }

    // Get emergency alerts count (placeholder)

    private function getEmergencyAlertsCount(): int
    {
        // Implement based on your tracking
        return 0;
    }

   // Get keyword responses count (placeholder)

    private function getKeywordResponsesCount(): int
    {
        // Implement based on your tracking
        return 0;
    }

    // Get failed attempts count (placeholder)

    private function getFailedAttemptsCount(): int
    {
        // Implement based on your error logs
        return 0;
    }

    // Get list of active SMS providers

    private function getActiveProviders(): array
    {
        $providers = [];
        
        if (config('sms.ssl_wireless.enabled')) $providers[] = 'SSL Wireless';
        if (config('sms.banglalink.enabled')) $providers[] = 'Banglalink';
        if (config('sms.grameenphone.enabled')) $providers[] = 'Grameenphone';
        if (config('sms.robi.enabled')) $providers[] = 'Robi';
        
        return $providers;
    }
}