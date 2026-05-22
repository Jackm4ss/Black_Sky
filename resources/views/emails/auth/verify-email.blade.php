@php
    $safeName = trim((string) ($userName ?? 'Black Sky Member')) ?: 'Black Sky Member';
    $expiresInMinutes = (int) ($expiresInMinutes ?? 60);
    $logoUrl = (string) ($logoUrl ?? asset('images/black-sky-logo.png'));
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>Verify your Black Sky account</title>
</head>
<body style="margin:0; padding:0; width:100%; background:#f4f4f5; font-family:Arial, Helvetica, sans-serif; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%;">
    <div style="display:none; overflow:hidden; line-height:1px; opacity:0; max-height:0; max-width:0;">
        Confirm your email to activate your Black Sky account.
    </div>

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; border-collapse:collapse; background:#f4f4f5;">
        <tr>
            <td align="center" style="padding:24px 12px;">
                <!--[if mso]>
                <table role="presentation" width="560" cellspacing="0" cellpadding="0" border="0" align="center">
                <tr>
                <td>
                <![endif]-->
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; max-width:560px; border-collapse:collapse; background:#ffffff; border:1px solid #e5e7eb;">
                    <tr>
                        <td style="padding:18px 24px; background:#050505; border-bottom:1px solid #111827;">
                            <img src="{{ $logoUrl }}" width="64" alt="Black Sky" border="0" style="display:block; width:64px; height:auto; border:0; outline:none; text-decoration:none;">
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:26px 24px 28px; color:#111827;">
                            <h1 style="margin:0 0 14px; color:#111827; font-family:Arial, Helvetica, sans-serif; font-size:24px; font-weight:700; line-height:30px;">
                                Welcome to Black Sky
                            </h1>

                            <p style="margin:0 0 16px; color:#374151; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px;">
                                Hi {{ $safeName }},
                            </p>

                            <p style="margin:0 0 24px; color:#374151; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px;">
                                Please verify your email address to activate your Black Sky member account.
                            </p>

                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;">
                                <tr>
                                    <td bgcolor="#111827" style="background:#111827;">
                                        <a href="{{ $verificationUrl }}" target="_blank" style="display:inline-block; padding:13px 20px; color:#ffffff; font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:700; line-height:18px; text-decoration:none;">
                                            Verify Email Address
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:24px 0 0; color:#6b7280; font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:21px;">
                                This link expires in {{ $expiresInMinutes }} minutes. If you did not create a Black Sky account, you can ignore this email.
                            </p>

                            <p style="margin:18px 0 0; color:#6b7280; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:20px;">
                                If the button does not work, copy and paste this link into your browser:
                            </p>

                            <p style="margin:6px 0 0; color:#2563eb; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:20px; word-break:break-all;">
                                <a href="{{ $verificationUrl }}" target="_blank" style="color:#2563eb; text-decoration:underline; word-break:break-all;">{{ $verificationUrl }}</a>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:18px 24px; border-top:1px solid #e5e7eb;">
                            <p style="margin:0; color:#6b7280; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:19px;">
                                Sent by {{ $appName }}. Need help? Contact {{ $supportEmail }}.
                            </p>
                            <p style="margin:4px 0 0; color:#9ca3af; font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:17px;">
                                {{ $siteHost }}
                            </p>
                        </td>
                    </tr>
                </table>
                <!--[if mso]>
                </td>
                </tr>
                </table>
                <![endif]-->
            </td>
        </tr>
    </table>
</body>
</html>
