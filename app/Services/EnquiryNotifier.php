<?php

namespace App\Services;

use App\Models\ContactMessage;
use App\Models\QuoteRequest;
use App\Models\SeoSetting;
use Illuminate\Support\Facades\Mail;
use Throwable;

final class EnquiryNotifier
{
    public function contactMessageCreated(ContactMessage $message): void
    {
        $body = implode("\n", [
            'A new contact message was submitted on ENCAP.',
            '',
            'Name: '.$message->name,
            'Email: '.$message->email,
            'Company: '.($message->company_name ?: '—'),
            'Phone: '.($message->phone ?: '—'),
            'Subject: '.$message->subject,
            '',
            'Message:',
            $message->message,
        ]);

        $this->send('New website contact message', $body);
    }

    public function quoteRequestCreated(QuoteRequest $request): void
    {
        $body = implode("\n", [
            'A new export quote request was submitted on ENCAP.',
            '',
            'Contact: '.$request->contact_name,
            'Company: '.($request->company_name ?: '—'),
            'Email: '.$request->email,
            'Country: '.$request->country,
            'Category: '.($request->category?->name_en ?: 'Not specified'),
            'Quantity: '.($request->quantity !== null ? $request->quantity.' '.($request->quantity_unit ?: '') : 'Not specified'),
            'Product form: '.($request->product_form ?: 'Not specified'),
            'Packaging: '.($request->packaging_requirements ?: 'Not specified'),
            '',
            'Message:',
            $request->message,
        ]);

        $this->send('New export quote request', $body);
    }

    private function send(string $subject, string $body): void
    {
        $recipient = trim((string) SeoSetting::current()->contact_email);
        $mailer = (string) config('mail.default', 'log');

        if ($recipient === '' || in_array($mailer, ['log', 'array', 'null'], true)) {
            logger()->notice('ENCAP enquiry email notification was skipped because a recipient or real mail transport is not configured.');

            return;
        }

        try {
            Mail::raw($body, static function ($message) use ($recipient, $subject): void {
                $message->to($recipient)->subject('[ENCAP] '.$subject);
            });
        } catch (Throwable $exception) {
            // The enquiry is already safely stored; a mail failure must not turn a successful form into an error page.
            logger()->warning('ENCAP enquiry was saved, but its email notification failed.', [
                'exception' => get_class($exception),
            ]);
        }
    }
}
