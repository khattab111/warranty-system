<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">

    <title>معلومات الضمان</title>

    <style>
        @page {
            margin: 18mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            direction: rtl;
            color: #111827;
            font-family: "DejaVu Sans", sans-serif;
        }

        .document {
            width: 100%;
        }

        .header {
            margin-bottom: 24px;
            padding-bottom: 15px;
            border-bottom: 2px solid #111827;
            text-align: center;
        }

        .header h1 {
            margin: 0 0 6px;
            font-size: 21px;
        }

        .header p {
            margin: 0;
            color: #6b7280;
            font-size: 11px;
        }

        .reference-box {
            margin-bottom: 18px;
            padding: 12px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
            text-align: center;
        }

        .reference-label {
            margin-bottom: 5px;
            color: #6b7280;
            font-size: 10px;
        }

        .reference-value {
            direction: ltr;
            font-family: "DejaVu Sans Mono", monospace;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .info {
            overflow: hidden;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }

        .info table {
            width: 100%;
            border-collapse: collapse;
        }

        .info td {
            padding: 11px 14px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
            vertical-align: middle;
        }

        .info tr:last-child td {
            border-bottom: 0;
        }

        .info td:first-child {
            width: 38%;
            background: #f9fafb;
            color: #4b5563;
            font-weight: bold;
        }

        .ltr {
            direction: ltr;
            text-align: right;
        }

        .status {
            font-weight: bold;
        }

        .status-active {
            color: #15803d;
        }

        .status-expired {
            color: #b91c1c;
        }

        .status-inactive {
            color: #a16207;
        }

        .footer {
            margin-top: 25px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            color: #9ca3af;
            text-align: center;
            font-size: 9px;
        }
    </style>
</head>

<body>
    @php
        $statusLabel = match ($warranty->status) {
            'active' => 'ساري',
            'expired' => 'منتهي',
            default => 'غير مفعل',
        };
    @endphp

    <div class="document">
        <div class="header">
            <h1>شهادة ضمان الجهاز</h1>
            <p>متجر الهواتف</p>
        </div>

        <div class="reference-box">
            <div class="reference-label">
                الرقم المرجعي للضمان
            </div>

            <div class="reference-value">
                {{ $warranty->short_reference }}
            </div>
        </div>

        <div class="info">
            <table>
                <tr>
                    <td>نوع الجهاز</td>

                    <td>
                        {{ $warranty->device_type ?: '--' }}
                    </td>
                </tr>

                <tr>
                    <td>رقم IMEI</td>

                    <td class="ltr">
                        {{ $warranty->imei ?: '--' }}
                    </td>
                </tr>

                <tr>
                    <td>تاريخ التفعيل</td>

                    <td>
                        {{ $warranty->activated_at?->format('Y-m-d H:i') ?? '--' }}
                    </td>
                </tr>

                <tr>
                    <td>تاريخ انتهاء الضمان</td>

                    <td>
                        {{ $warranty->warranty_expires_at?->format('Y-m-d H:i') ?? '--' }}
                    </td>
                </tr>

                <tr>
                    <td>حالة الضمان</td>

                    <td class="status status-{{ $warranty->status }}">
                        {{ $statusLabel }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            تمت طباعة هذه الوثيقة بتاريخ
            {{ now()->format('Y-m-d H:i') }}
            — جميع الحقوق محفوظة
        </div>
    </div>
</body>
</html>
