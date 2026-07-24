<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">

    <title>رموز QR للضمان</title>

    <style>
        @page {
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: "DejaVu Sans", sans-serif;
            color: #111827;
            direction: rtl;
        }

        .page {
            width: 100%;
        }

        .page-break {
            page-break-after: always;
        }

        .header {
            margin-bottom: 14px;
            text-align: center;
        }

        .header h1 {
            margin: 0 0 4px;
            font-size: 18px;
            font-weight: bold;
        }

        .header p {
            margin: 0;
            color: #6b7280;
            font-size: 10px;
        }

        .qr-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            table-layout: fixed;
            direction: rtl;
        }

        .qr-table td {
            width: 33.333%;
            height: 215px;
            padding: 0;
            vertical-align: top;
        }

        .qr-card {
            height: 205px;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            text-align: center;
            page-break-inside: avoid;
        }

        .qr-image {
            display: block;
            width: 145px;
            height: 145px;
            margin: 0 auto 8px;
        }

        .reference-label {
            margin-bottom: 3px;
            color: #6b7280;
            font-size: 8px;
        }

        .reference {
            direction: ltr;
            color: #111827;
            font-family: "DejaVu Sans Mono", monospace;
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .page-number {
            margin-top: 6px;
            color: #9ca3af;
            text-align: center;
            font-size: 8px;
        }
    </style>
</head>

<body>
    @foreach(array_chunk($qrCodes, 9) as $pageIndex => $pageQrCodes)
        <div class="page">
            <div class="header">
                <h1>رموز QR للضمان</h1>

                <p>
                    عدد الرموز في هذه الصفحة:
                    {{ count($pageQrCodes) }}
                </p>
            </div>

            <table class="qr-table">
                <tbody>
                    @foreach(array_chunk($pageQrCodes, 3) as $row)
                        <tr>
                            @foreach($row as $qr)
                                <td>
                                    <div class="qr-card">
                                        <img
                                            class="qr-image"
                                            src="{{ $qr['image'] }}"
                                            alt="QR"
                                        >

                                        <div class="reference-label">
                                            الرقم المرجعي
                                        </div>

                                        <div class="reference">
                                            {{ $qr['reference'] }}
                                        </div>
                                    </div>
                                </td>
                            @endforeach

                            @for($empty = count($row); $empty < 3; $empty++)
                                <td></td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="page-number">
                صفحة {{ $pageIndex + 1 }}
                من {{ count(array_chunk($qrCodes, 9)) }}
            </div>
        </div>

        @if(! $loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
