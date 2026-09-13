<?php

namespace App\Livewire;

use App\Actions\SendInquiryEmails;
use App\Models\Inquiry;
use App\Models\Service;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

class QuoteRequest extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $address = '';

    public string $service_id = '';

    public string $preferred_date = '';

    public string $details = '';

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
            throw ValidationException::withMessages(['name' => 'Unable to submit this request.']);
        }
        $key = 'quote-request:'.request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['name' => 'Too many requests. Please try again in a minute.']);
        }
        $data = $this->validate([
            'name' => 'required|string|max:120', 'email' => 'required|email|max:255',
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+()\s-]{7,30}$/'],
            'address' => 'required|string|max:1000',
            'service_id' => ['required', Rule::exists('services', 'id')->where('is_active', true)],
            'preferred_date' => 'nullable|date_format:Y-m-d|after_or_equal:'.now('Asia/Kathmandu')->toDateString(),
            'details' => 'required|string|min:10|max:5000', 'consent' => 'accepted',
        ]);
        RateLimiter::hit($key, 60);
        unset($data['consent']);
        $data['preferred_date'] = $data['preferred_date'] ?: null;
        $inquiry = Inquiry::firstOrCreate(['submission_token' => $this->submissionToken], $data + [
            'reference' => 'PK-'.Str::upper((string) Str::ulid()), 'consented_at' => now(),
        ]);
        if ($inquiry->wasRecentlyCreated) {
            app(SendInquiryEmails::class)->handle($inquiry);
        }
        $this->reference = $inquiry->reference;
        $this->reset(['name', 'email', 'phone', 'address', 'service_id', 'preferred_date', 'details', 'consent']);
    }

    public function render()
    {
        return view('livewire.quote-request', ['services' => Service::where('is_active', true)->orderBy('sort_order')->get()])
            ->layout('components.layouts.site', ['title' => 'Request a packing quote']);
    }
}
