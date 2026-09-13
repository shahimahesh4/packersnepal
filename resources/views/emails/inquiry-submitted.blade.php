<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $staffCopy ? 'New website enquiry' : 'We received your request' }}</title>
</head>
<body style="margin:0;background:#f4f1ec;color:#0b3d46;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f1ec;padding:28px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:660px;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 12px 36px rgba(11,61,70,.10);">
                <tr>
                    <td style="background:#073b43;padding:26px 34px;text-align:center;">
                        <a href="{{ url('/') }}" style="text-decoration:none;">
                            <img src="{{ asset('images/packers-nepal-logo.png') }}" width="250" alt="Packers Nepal — Care in Every Layer" style="display:inline-block;max-width:100%;height:auto;border:0;">
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="padding:38px 38px 14px;">
                        <div style="font-size:12px;line-height:18px;letter-spacing:2px;text-transform:uppercase;color:#ff572d;font-weight:700;">{{ $inquiry->inquiry_type === 'contact' ? 'Contact message' : 'Packing quote request' }}</div>
                        <h1 style="margin:10px 0 12px;font-size:29px;line-height:38px;color:#073b43;">{{ $staffCopy ? 'A new request has arrived.' : 'Thank you, '.$inquiry->name.'.' }}</h1>
                        <p style="margin:0;font-size:16px;line-height:26px;color:#55757b;">{{ $staffCopy ? 'The complete customer details are below. Replying to this email will reply directly to the customer.' : 'We have received your request. Our packing team will review the details and contact you about availability and the next steps.' }}</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:12px 38px 4px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eaf2f1;border-radius:14px;">
                            <tr>
                                <td style="padding:18px 20px;font-size:13px;color:#55757b;">REFERENCE<br><strong style="display:block;margin-top:5px;font-size:18px;letter-spacing:.5px;color:#073b43;">{{ $inquiry->reference }}</strong></td>
                                <td style="padding:18px 20px;text-align:right;font-size:13px;color:#55757b;">STATUS<br><strong style="display:block;margin-top:5px;color:#ff572d;text-transform:capitalize;">{{ $inquiry->status ?? 'New' }}</strong></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:22px 38px 8px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                            @php
                                $details = [
                                    'Name' => $inquiry->name,
                                    'Email' => $inquiry->email,
                                    'Phone' => $inquiry->phone,
                                    'Service' => $inquiry->service?->name,
                                    'Preferred date' => $inquiry->preferred_date?->format('d M Y'),
                                    'Location' => $inquiry->address,
                                    'Subject' => $inquiry->subject,
                                ];
                            @endphp
                            @foreach ($details as $label => $value)
                                @if (filled($value))
                                    <tr>
                                        <td style="width:145px;padding:11px 0;border-bottom:1px solid #e5eceb;font-size:13px;font-weight:700;color:#073b43;vertical-align:top;">{{ $label }}</td>
                                        <td style="padding:11px 0;border-bottom:1px solid #e5eceb;font-size:15px;line-height:23px;color:#55757b;vertical-align:top;">{{ $value }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:18px 38px 12px;">
                        <h2 style="margin:0 0 10px;font-size:17px;color:#073b43;">{{ $inquiry->inquiry_type === 'contact' ? 'Message' : 'Packing details' }}</h2>
                        <div style="padding:18px 20px;border-left:4px solid #ff572d;background:#fff7f3;border-radius:4px 12px 12px 4px;font-size:15px;line-height:25px;color:#55757b;white-space:pre-line;">{{ $inquiry->details }}</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:20px 38px 38px;">
                        <a href="{{ $staffCopy ? url('/stnapanel/inquiries') : url('/') }}" style="display:inline-block;background:#ff572d;color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;padding:14px 22px;border-radius:10px;">{{ $staffCopy ? 'Open in dashboard' : 'Visit Packers Nepal' }}</a>
                    </td>
                </tr>
                <tr>
                    <td style="background:#073b43;padding:24px 34px;text-align:center;font-size:13px;line-height:21px;color:#b8cfd2;">
                        Packers Nepal · Newroad, Kathmandu · 9801010000<br>
                        <a href="mailto:info@packersnepal.com" style="color:#ffffff;text-decoration:none;">info@packersnepal.com</a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
