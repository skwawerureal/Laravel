<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $campaign->subject }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse;">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #4F46E5; padding: 20px; text-align: center;">
                            <h1 style="color: white; margin: 0; font-size: 24px;">{{ $campaign->from_name }}</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px; background-color: #ffffff;">
                            {!! $content !!}
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; font-size: 12px; color: #6b7280;">
                                You received this email because you subscribed to our mailing list.
                            </p>
                            <p style="margin: 10px 0 0 0; font-size: 12px; color: #6b7280;">
                                <a href="{{ $unsubscribeUrl }}" style="color: #4F46E5; text-decoration: underline;">Unsubscribe</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    
    {!! $trackingPixel ?? '' !!}
</body>
</html>
