<?php

namespace App\Actions;

use App\Mail\InquirySubmitted;
use App\Models\Inquiry;
use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendInquiryEmails
{
    public function handle(Inquiry $inquiry): void
    {
        $inquiry->loadMissing('service');

        $businessEmail = WebsiteSetting::query()->value('email') ?: config('mail.from.address');

        try {
            Mail::to($inquiry->email)->send(new InquirySubmitted($inquiry, staffCopy: false));
        } catch (Throwable $exception) {
            report($exception);
        }

        try {
            Mail::to($businessEmail)->send(new InquirySubmitted($inquiry, staffCopy: true));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
