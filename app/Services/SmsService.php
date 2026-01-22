<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private $apiKey = 'COl4VLQ6YoiA4TDCNkzW';
    private $senderId = '8809617630930';
    private $apiUrl = 'http://bulksmsbd.net/api/smsapi';

    /**
     * Send SMS to a phone number
     *
     * @param string $phone - Phone number (receiver)
     * @param string $message - SMS message
     * @return bool - True if SMS sent successfully, false otherwise
     */
    public function sendSms($phone, $message)
    {
        try {
            $formattedPhone = $this->formatPhoneNumber($phone);
            
            $response = Http::get($this->apiUrl, [
                'api_key' => $this->apiKey,
                'type' => 'text',
                'number' => $formattedPhone,
                'senderid' => $this->senderId,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('SMS sent successfully', [
                    'phone' => $formattedPhone,
                    'original_phone' => $phone,
                    'message' => $message,
                    'response' => $response->body(),
                ]);
                return true;
            } else {
                Log::warning('SMS sending failed', [
                    'phone' => $formattedPhone,
                    'original_phone' => $phone,
                    'message' => $message,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('SMS service error', [
                'phone' => $phone,
                'message' => $message,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Format phone number to be compatible with BulkSMSBD API
     * (e.g., 017... -> 88017...)
     *
     * @param string $phone
     * @return string
     */
    private function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If it starts with 0 and is 11 digits (e.g. 01731278775), prefix with 88
        if (strpos($phone, '0') === 0 && strlen($phone) == 11) {
            $phone = '88' . $phone;
        }

        return $phone;
    }

    /**
     * Send order cancellation SMS to dropshipper
     *
     * @param string $phone - Dropshipper phone number
     * @param string $invoiceNumber - Order invoice number
     * @return bool
     */
    public function sendCancelNotification($phone, $invoiceNumber)
    {
        $message = "Order Invoice #{$invoiceNumber} has been cancelled. Please review your account for details.";
        return $this->sendSms($phone, $message);
    }

    /**
     * Send order delivered SMS to dropshipper
     *
     * @param string $phone - Dropshipper phone number
     * @param string $invoiceNumber - Order invoice number
     * @return bool
     */
    public function sendDeliveredNotification($phone, $invoiceNumber)
    {
        $message = "Order Invoice #{$invoiceNumber} has been marked as delivered. Please confirm receipt.";
        return $this->sendSms($phone, $message);
    }
}
