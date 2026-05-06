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
            $response = Http::get($this->apiUrl, [
                'api_key' => $this->apiKey,
                'type' => 'text',
                'number' => '88' . $phone,
                'senderid' => $this->senderId,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('SMS sent successfully', [
                    'phone' =>  '88' .$phone,
                    'message' => $message,
                    'response' => $response->body(),
                ]);
                return true;
            } else {
                Log::warning('SMS sending failed', [
                    'phone' => '88' .$phone,
                    'message' => $message,
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('SMS service error', [
                'phone' => '88' .$phone,
                'message' => $message,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
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
