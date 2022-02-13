<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Password Reset</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        body,
        table,
        td,
        a {
            -ms-text-size-adjust: 100%;
            /* 1 */
            -webkit-text-size-adjust: 100%;
            /* 2 */
        }


        table,
        td {
            mso-table-rspace: 0pt;
            mso-table-lspace: 0pt;
        }


        img {
            -ms-interpolation-mode: bicubic;
        }


        a[x-apple-data-detectors] {
            font-family: inherit !important;
            font-size: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
            color: inherit !important;
            text-decoration: none !important;
        }


        div[style*="margin: 16px 0;"] {
            margin: 0 !important;
        }

        body {
            width: 100% !important;
            height: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }


        table {
            border-collapse: collapse !important;
        }

        a {
            color: #1a82e2;
        }

        img {
            height: auto;
            line-height: 100%;
            text-decoration: none;
            border: 0;
            outline: none;
        }

    </style>

</head>

<body style="background-color: #e9ecef;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" bgcolor="#e9ecef">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td align="center" valign="top" style="padding: 36px 24px;">
                            <a href="{{ $url ?? '' }}" target="_blank" style="display: inline-block;">
                                <img src="{{ Config::get("global.alumni_web_url") . "images/rsu-logo.png" }}" alt="Logo" border="0" width="120px"
                                    style="display: block; width: 120px; max-width: 120px; min-width: 120px;">
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center" bgcolor="#e9ecef">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td align="left" bgcolor="#ffffff"
                            style="padding: 36px 24px 0; font-family: rsu-rt, Helvetica, Arial, sans-serif; border-top: 3px solid #d4dadf;">
                            <h1
                                style="margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -1px; line-height: 48px;">
                                ยืนยันการลงทะเบียน</h1>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center" bgcolor="#e9ecef">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td align="left" bgcolor="#ffffff"
                            style="padding: 24px; font-family: rsu-rt, Helvetica, Arial, sans-serif; font-size: 12px; line-height: 24px;">
                            <p style="margin: 0;font-size: 16px">สวัสดี, คุณ {{ $fullname }} </p>
                            <p style="margin: 0;font-size: 16px">โปรดใช้รหัสผ่านด้านล่างนี้ ในการเข้าสู่ระบบ</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="left" bgcolor="#ffffff">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" bgcolor="#ffffff" style="padding: 12px;">
                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td align="center" style="border-radius: 6px;">
                                                    <h2>{{ $password }}</h2>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td align="center" bgcolor="#e9ecef" style="padding: 24px;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
                    <tr>
                        <td align="center" bgcolor="#e9ecef"
                            style="padding: 16px 24px; font-family: rsu-rt, Helvetica, Arial, sans-serif; font-size: 12px; line-height: 20px; color: #666;">
                            <p style="margin: 0;">
                                คุณได้รับอีเมลนี้เนื่องจากเราได้รับคำขอจากการลงทะเบียนสำหรับบัญชีของคุณ
                                หากคุณไม่ได้ร้องขอคุณสามารถลบอีเมลนี้ได้อย่างปลอดภัย</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
