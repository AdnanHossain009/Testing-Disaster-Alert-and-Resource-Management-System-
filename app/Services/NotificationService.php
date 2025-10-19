<?php

namespace App\Services;

use App\Models\InAppNotification;
use App\Models\Alert;
use App\Models\HelpRequest;
use App\Models\Assignment;

class NotificationService
{
    /**
     * Create notification for Alert Created
     */
    public function notifyAlertCreated(Alert $alert): void
    {
        // Admin notification
        InAppNotification::create([
            'recipient_type' => 'admin',
            'type' => 'alert_created',
            'title' => "🚨 {$alert->severity} Alert Created",
            'message' => "New {$alert->alert_type} alert '{$alert->title}' has been published for {$alert->location}",
            'icon' => '🚨',
            'color' => $this->getSeverityColor($alert->severity),
            'reference_id' => $alert->id,
            'reference_type' => 'Alert',
        ]);
    }

    /**
     * Create notification for Request Submitted
     */
    public function notifyRequestSubmitted(HelpRequest $request): void
    {
        // Admin notification
        InAppNotification::create([
            'recipient_type' => 'admin',
            'type' => 'request_submitted',
            'title' => "✅ New Emergency Request #{$request->id}",
            'message' => "{$request->name} submitted a {$request->request_type} request from {$request->location}" . ($request->urgency_level ? " (Urgency: {$request->urgency_level})" : ""),
            'icon' => '✅',
            'color' => $this->getUrgencyColor($request->urgency_level ?? 'medium'),
            'reference_id' => $request->id,
            'reference_type' => 'HelpRequest',
        ]);

        // Citizen notification (if they have an account)
        if ($request->user_id) {
            InAppNotification::create([
                'recipient_type' => 'citizen',
                'user_id' => $request->user_id,
                'type' => 'request_submitted',
                'title' => "✅ Your Request Was Submitted",
                'message' => "Request #{$request->id} for {$request->request_type} has been received. Our team will contact you soon.",
                'icon' => '✅',
                'color' => '#27ae60',
                'reference_id' => $request->id,
                'reference_type' => 'HelpRequest',
            ]);
        }
    }

    /**
     * Create notification for Shelter Assigned
     */
    public function notifyShelterAssigned(Assignment $assignment): void
    {
        $shelter = $assignment->shelter;
        $request = $assignment->request;

        // Admin notification
        InAppNotification::create([
            'recipient_type' => 'admin',
            'type' => 'shelter_assigned',
            'title' => "🏠 Shelter Assigned",
            'message' => "Request #{$request->id} assigned to {$shelter->name}. Citizen: {$request->name}",
            'icon' => '🏠',
            'color' => '#3498db',
            'reference_id' => $assignment->id,
            'reference_type' => 'Assignment',
        ]);

        // Citizen notification (if they have an account)
        if ($request->user_id) {
            InAppNotification::create([
                'recipient_type' => 'citizen',
                'user_id' => $request->user_id,
                'type' => 'shelter_assigned',
                'title' => "🏠 Shelter Assigned to You",
                'message' => "You've been assigned to {$shelter->name} at {$shelter->address}. Please report within 2 hours.",
                'icon' => '🏠',
                'color' => '#3498db',
                'reference_id' => $assignment->id,
                'reference_type' => 'Assignment',
            ]);
        }
    }

    /**
     * Create notification for Status Updated
     */
    public function notifyStatusUpdated(HelpRequest $request, string $oldStatus): void
    {
        // Admin notification
        InAppNotification::create([
            'recipient_type' => 'admin',
            'type' => 'status_updated',
            'title' => "📋 Status Updated: Request #{$request->id}",
            'message' => "Request #{$request->id} status changed from {$oldStatus} to {$request->status}",
            'icon' => '📋',
            'color' => '#9b59b6',
            'reference_id' => $request->id,
            'reference_type' => 'HelpRequest',
        ]);

        // Citizen notification (if they have an account)
        if ($request->user_id) {
            InAppNotification::create([
                'recipient_type' => 'citizen',
                'user_id' => $request->user_id,
                'type' => 'status_updated',
                'title' => "📋 Your Request Status Updated",
                'message' => "Request #{$request->id} status: {$oldStatus} → {$request->status}. " . $this->getStatusMessage($request->status),
                'icon' => '📋',
                'color' => '#9b59b6',
                'reference_id' => $request->id,
                'reference_type' => 'HelpRequest',
            ]);
        }
    }

    /**
     * Get unseen count for admin
     */
    public function getAdminUnseenCount(): int
    {
        return InAppNotification::forAdmin()->unseen()->count();
    }

    /**
     * Get unseen count for citizen
     */
    public function getCitizenUnseenCount(int $userId): int
    {
        return InAppNotification::forCitizen($userId)->unseen()->count();
    }

    /**
     * Mark all as seen for admin
     */
    public function markAllAdminAsSeen(): void
    {
        InAppNotification::forAdmin()->unseen()->update([
            'seen' => true,
            'seen_at' => now(),
        ]);
    }

    /**
     * Mark all as seen for citizen
     */
    public function markAllCitizenAsSeen(int $userId): void
    {
        InAppNotification::forCitizen($userId)->unseen()->update([
            'seen' => true,
            'seen_at' => now(),
        ]);
    }

    /**
     * Get severity color
     */
    private function getSeverityColor(string $severity): string
    {
        return match(strtolower($severity)) {
            'critical' => '#e74c3c',
            'high' => '#e67e22',
            'moderate' => '#f39c12',
            'low' => '#3498db',
            default => '#95a5a6',
        };
    }

    /**
     * Get urgency color
     */
    private function getUrgencyColor(string $urgency): string
    {
        return match(strtolower($urgency)) {
            'critical' => '#e74c3c',
            'high' => '#e67e22',
            'medium' => '#f39c12',
            'low' => '#3498db',
            default => '#95a5a6',
        };
    }

    /**
     * Get status message
     */
    private function getStatusMessage(string $status): string
    {
        return match($status) {
            'Assigned' => 'A relief worker has been assigned to your request.',
            'In Progress' => 'Our team is working on your request.',
            'Completed' => 'Your request has been successfully completed.',
            'Cancelled' => 'This request has been cancelled.',
            default => 'Your request status has been updated.',
        };
    }
}
