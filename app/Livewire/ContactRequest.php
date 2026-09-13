<?php

namespace App\Livewire;

use App\Actions\SendInquiryEmails;
use App\Models\Inquiry;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ContactRequest extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $subject = '';

    public string $message = '';

    public bool $consent = false;

    public string $website = '';

    #[Locked]
    public string $submissionToken;

    #[Locked]
    public ?string $reference = null;

    public function mount(): void
    {
        $this->submissionToken = (string) Str::uuid();
    }

    public function submit(): void
    {
        if ($this->reference) {
            return;
        }
        if ($this->website !== '') {
            throw ValidationException::withMessages(['name' => 'Unable to submit this message.']);
        }

        $key = 'contact-request:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['name' => 'Too many messages. Please try again in a minute.']);
        }

        $data = $this->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:255',
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+()\s-]{7,30}$/'],
            'subject' => 'required|string|max:180',
            'message' => 'required|string|min:10|max:5000',
            'consent' => 'accepted',
        ]);

        RateLimiter::hit($key, 60);
        unset($data['consent']);
        $data['details'] = $data['message'];
        unset($data['message']);

        $inquiry = Inquiry::firstOrCreate(['submission_token' => $this->submissionToken], $data + [
            'reference' => 'CT-'.Str::upper((string) Str::ulid()),
            'inquiry_type' => 'contact',
            'consented_at' => now(),
        ]);

        if ($inquiry->wasRecentlyCreated) {
            app(SendInquiryEmails::class)->handle($inquiry);
        }

        $this->reference = $inquiry->reference;
        $this->reset(['name', 'email', 'phone', 'subject', 'message', 'consent']);
    }

    public function render()
    {
        return view('livewire.contact-request')->layout('components.layouts.site', [
            'title' => 'Contact Our Packing Specialists',
            'metaDescription' => 'Contact Packers Nepal for household, office, fragile-item and business packing enquiries in Kathmandu Valley and across Nepal.',
        ]);
    }
}
