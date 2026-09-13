PACKERS NEPAL — CARE IN EVERY LAYER

{{ $staffCopy ? 'A new request has arrived.' : 'Thank you, '.$inquiry->name.'. We have received your request.' }}

Reference: {{ $inquiry->reference }}
Request type: {{ $inquiry->inquiry_type === 'contact' ? 'Contact message' : 'Packing quote request' }}
Name: {{ $inquiry->name }}
Email: {{ $inquiry->email }}
Phone: {{ $inquiry->phone }}
@if ($inquiry->service)
Service: {{ $inquiry->service->name }}
@endif
@if ($inquiry->preferred_date)
Preferred date: {{ $inquiry->preferred_date->format('d M Y') }}
@endif
@if ($inquiry->address)
Location: {{ $inquiry->address }}
@endif
@if ($inquiry->subject)
Subject: {{ $inquiry->subject }}
@endif

{{ $inquiry->inquiry_type === 'contact' ? 'Message' : 'Packing details' }}:
{{ $inquiry->details }}

Packers Nepal
Newroad, Kathmandu · 9801010000 · info@packersnepal.com
