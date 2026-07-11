<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Garcia Systems inquiry</title>
</head>
<body style="margin:0;background:#f6f7fb;color:#172033;font-family:Arial,Helvetica,sans-serif;line-height:1.5;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f7fb;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e5e7eb;">
                    <tr>
                        <td style="background:#0f172a;color:#ffffff;padding:28px 32px;">
                            <p style="margin:0 0 8px;font-size:13px;letter-spacing:.08em;text-transform:uppercase;color:#cbd5e1;">Garcia Systems lead alert</p>
                            <h1 style="margin:0;font-size:26px;line-height:1.2;">New inquiry from {{ $submission->name }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin:0 0 20px;">A visitor submitted the Garcia Systems contact form. Reply directly to this email to follow up with {{ $submission->name }}.</p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                                <tr><td style="padding:10px 0;color:#64748b;width:38%;vertical-align:top;">Name</td><td style="padding:10px 0;font-weight:700;">{{ $submission->name }}</td></tr>
                                <tr><td style="padding:10px 0;color:#64748b;vertical-align:top;">Email</td><td style="padding:10px 0;"><a href="mailto:{{ $submission->email }}" style="color:#2563eb;">{{ $submission->email }}</a></td></tr>
                                <tr><td style="padding:10px 0;color:#64748b;vertical-align:top;">Company</td><td style="padding:10px 0;">{{ $submission->company ?: 'Not provided' }}</td></tr>
                                <tr><td style="padding:10px 0;color:#64748b;vertical-align:top;">Service interest</td><td style="padding:10px 0;">{{ $submission->service_interest ?: 'Not provided' }}</td></tr>
                                <tr><td style="padding:10px 0;color:#64748b;vertical-align:top;">Submitted</td><td style="padding:10px 0;">{{ optional($submission->created_at)->format('M j, Y g:i A T') }}</td></tr>
                                <tr><td style="padding:10px 0;color:#64748b;vertical-align:top;">Lead ID</td><td style="padding:10px 0;">{{ $lead->id }}</td></tr>
                            </table>

                            <div style="margin:24px 0;padding:18px;border:1px solid #dbeafe;background:#eff6ff;border-radius:12px;">
                                <p style="margin:0 0 8px;color:#1e3a8a;font-weight:700;">Message</p>
                                <p style="margin:0;white-space:pre-wrap;">{{ $submission->message }}</p>
                            </div>

                            <p style="margin:28px 0 0;"><a href="{{ $leadUrl }}" style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;font-weight:700;padding:12px 18px;border-radius:10px;">View lead in admin dashboard</a></p>
                            <p style="margin:16px 0 0;font-size:13px;color:#64748b;word-break:break-all;">{{ $leadUrl }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
