Verify your Black Sky account

Hi {{ trim((string) ($userName ?? 'Black Sky Member')) ?: 'Black Sky Member' }},

Confirm this email address to activate your Black Sky member account and continue to the member dashboard.

Verify email:
{{ $verificationUrl }}

This verification link expires in {{ (int) ($expiresInMinutes ?? 60) }} minutes.

If you did not create a Black Sky account, you can safely ignore this email.

{{ $appName }}
{{ $siteHost }}
