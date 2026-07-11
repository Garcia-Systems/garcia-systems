<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>We received your Garcia Systems inquiry</title>
</head>
<body style="margin:0;background:#f6f7fb;color:#172033;font-family:Arial,Helvetica,sans-serif;line-height:1.5;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f7fb;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e5e7eb;">
                    <tr>
                        <td style="background:#0f172a;color:#ffffff;padding:28px 32px;">
                            <p style="margin:0 0 8px;font-size:13px;letter-spacing:.08em;text-transform:uppercase;color:#cbd5e1;">Garcia Systems</p>
                            <h1 style="margin:0;font-size:26px;line-height:1.2;">Thanks, {{ $displayName }} — we received your inquiry.</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin:0 0 16px;">Thanks for reaching out to Garcia Systems. This confirms that your message was received.</p>
                            <p style="margin:0 0 24px;"><strong>We typically respond within 1–2 business days.</strong></p>

                            <h2 style="margin:0 0 12px;font-size:18px;">Your submission summary</h2>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                                <tr><td style="padding:10px 0;color:#64748b;width:38%;vertical-align:top;">Company</td><td style="padding:10px 0;">{{ $submission->company ?: 'Not provided' }}</td></tr>
                                <tr><td style="padding:10px 0;color:#64748b;vertical-align:top;">Service interest</td><td style="padding:10px 0;">{{ $submission->service_interest ?: 'Not provided' }}</td></tr>
                            </table>

                            <div style="margin:20px 0 0;padding:18px;border:1px solid #dbeafe;background:#eff6ff;border-radius:12px;">
                                <p style="margin:0 0 8px;color:#1e3a8a;font-weight:700;">Message</p>
                                <p style="margin:0;white-space:pre-wrap;">{{ $submission->message }}</p>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
