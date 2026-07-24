@extends('layouts.public')

@section('title', 'معلومات الضمان')

@push('styles')
    <style>
        .warranty-page {
            width: 100%;
            min-height: 100vh;
            padding: 34px 20px 50px;
        }

        .warranty-container {
            width: 100%;
            max-width: 960px;
            margin: 0 auto;
        }

        .brand-header {
            margin-bottom: 26px;
            text-align: center;
        }

        .brand-logo-image {
            display: block;
            width: auto;
            max-width: 310px;
            max-height: 130px;
            margin: 0 auto 8px;
            object-fit: contain;
        }

        .brand-fallback {
            margin: 0;
            color: var(--brand-primary);
            font-size: clamp(44px, 8vw, 82px);
            font-weight: 900;
            line-height: 0.95;
            letter-spacing: -3px;
            direction: ltr;
        }

        .brand-subtitle {
            margin-top: 8px;
            color: var(--brand-primary);
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 8px;
            text-transform: uppercase;
            direction: ltr;
        }

        .top-meta {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px 24px;
            margin-top: 22px;
            color: #3f3f46;
            font-size: 14px;
        }

        .meta-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .meta-label {
            color: var(--text-muted);
        }

        .meta-value {
            display: inline-flex;
            align-items: center;
            min-height: 30px;
            padding: 4px 12px;
            border-radius: 7px;
            background: var(--brand-primary);
            color: #fff;
            font-weight: 700;
            direction: ltr;
        }

        .warranty-card {
            overflow: hidden;
            margin-top: 28px;
            border: 1px solid rgba(84, 37, 121, 0.1);
            border-radius: 14px;
            background: #fff;
            box-shadow:
                0 20px 45px rgba(24, 24, 27, 0.08),
                0 2px 8px rgba(24, 24, 27, 0.04);
        }

        .card-accent {
            height: 6px;
            background:
                linear-gradient(
                    90deg,
                    var(--brand-primary),
                    var(--brand-secondary)
                );
        }

        .card-body {
            padding: 30px;
        }

        .status-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 22px;
            border-bottom: 1px solid var(--border);
        }

        .status-heading {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .status-icon {
            display: flex;
            width: 58px;
            height: 58px;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .status-icon svg {
            width: 29px;
            height: 29px;
        }

        .status-icon.active {
            background: #dcfce7;
            color: #15803d;
        }

        .status-icon.expired {
            background: #fee2e2;
            color: #b91c1c;
        }

        .status-icon.inactive {
            background: #fef3c7;
            color: #b45309;
        }

        .status-title {
            margin: 0;
            color: var(--text-main);
            font-size: 22px;
            font-weight: 800;
        }

        .status-description {
            margin: 5px 0 0;
            color: var(--text-muted);
            font-size: 14px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-height: 34px;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 800;
            white-space: nowrap;
        }

        .status-badge::before {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            content: "";
        }

        .status-badge.active {
            background: #dcfce7;
            color: #15803d;
        }

        .status-badge.expired {
            background: #fee2e2;
            color: #b91c1c;
        }

        .status-badge.inactive {
            background: #fef3c7;
            color: #b45309;
        }

        .warranty-layout {
            display: grid;
            grid-template-columns: 250px minmax(0, 1fr);
            gap: 28px;
        }

        .device-symbol-panel {
            display: flex;
            min-height: 300px;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 24px;
            border-radius: 12px;
            background:
                linear-gradient(
                    145deg,
                    #fafafa,
                    #f4f0f7
                );
            text-align: center;
        }

        .apple-symbol {
            width: 150px;
            height: 180px;
            color: #050505;
        }

        .device-brand {
            margin-top: 16px;
            color: var(--brand-primary);
            font-size: 18px;
            font-weight: 800;
            direction: ltr;
        }

        .device-subtitle {
            margin-top: 4px;
            color: var(--text-muted);
            font-size: 12px;
        }

        .details-title {
            margin: 0 0 12px;
            color: var(--text-main);
            font-size: 18px;
            font-weight: 800;
        }

        .details-box {
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fff;
        }

        .detail-row {
            display: grid;
            grid-template-columns: 155px minmax(0, 1fr);
            gap: 20px;
            align-items: center;
            min-height: 66px;
            padding: 13px 16px;
            border-bottom: 1px solid var(--border);
        }

        .detail-row:last-child {
            border-bottom: 0;
        }

        .detail-label {
            color: #3f3f46;
            font-size: 13px;
            font-weight: 800;
        }

        .detail-value {
            min-width: 0;
            color: #27272a;
            font-size: 15px;
            font-weight: 600;
            word-break: break-word;
        }

        .detail-value.ltr {
            direction: ltr;
            text-align: right;
            font-family:
                "Courier New",
                monospace;
        }

        .duration-panel {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid rgba(84, 37, 121, 0.14);
            border-radius: 12px;
            background:
                linear-gradient(
                    135deg,
                    #faf7fc,
                    #f4edf8
                );
        }

        .duration-label {
            margin-bottom: 8px;
            color: var(--text-muted);
            font-size: 13px;
            text-align: center;
        }

        .duration-value {
            color: var(--brand-primary);
            font-size: clamp(20px, 4vw, 29px);
            font-weight: 900;
            line-height: 1.5;
            text-align: center;
        }

        .duration-progress {
            overflow: hidden;
            height: 7px;
            margin-top: 14px;
            border-radius: 999px;
            background: #e9dff0;
        }

        .duration-progress-bar {
            width: 100%;
            height: 100%;
            border-radius: inherit;
            background:
                linear-gradient(
                    90deg,
                    var(--brand-primary),
                    var(--brand-secondary)
                );
        }

        .inactive-message,
        .expired-message {
            margin-top: 20px;
            padding: 16px;
            border-radius: 10px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
        }

        .inactive-message {
            border: 1px solid #fde68a;
            background: #fffbeb;
            color: #92400e;
        }

        .expired-message {
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #991b1b;
        }

        .security-note {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 24px;
            padding: 14px 16px;
            border: 1px solid #dbeafe;
            border-radius: 10px;
            background: #eff6ff;
            color: #1e40af;
            font-size: 12px;
            line-height: 1.8;
        }

        .security-note svg {
            width: 20px;
            height: 20px;
            flex: 0 0 auto;
            margin-top: 1px;
        }

        .page-footer {
            margin-top: 22px;
            color: #a1a1aa;
            font-size: 11px;
            line-height: 1.8;
            text-align: center;
        }

        @media (max-width: 760px) {
            .warranty-page {
                padding: 22px 12px 32px;
            }

            .brand-logo-image {
                max-width: 240px;
                max-height: 100px;
            }

            .brand-fallback {
                font-size: 50px;
            }

            .brand-subtitle {
                font-size: 11px;
                letter-spacing: 5px;
            }

            .card-body {
                padding: 20px;
            }

            .status-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .warranty-layout {
                grid-template-columns: 1fr;
            }

            .device-symbol-panel {
                min-height: 230px;
            }

            .apple-symbol {
                width: 105px;
                height: 130px;
            }

            .detail-row {
                grid-template-columns: 1fr;
                gap: 6px;
                min-height: auto;
            }

            .detail-value.ltr {
                text-align: left;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $isActivated = $warranty->activated_at !== null;
        $isExpired = $isActivated && $warranty->is_expired;

        $status = match (true) {
            ! $isActivated => 'inactive',
            $isExpired => 'expired',
            default => 'active',
        };

        $statusTitle = match ($status) {
            'active' => 'الضمان ساري',
            'expired' => 'انتهت مدة الضمان',
            default => 'الرمز غير مفعل',
        };

        $statusDescription = match ($status) {
            'active' => 'هذا الجهاز مشمول بضمان iPhone Azaz.',
            'expired' => 'انتهت مدة تغطية الضمان لهذا الجهاز.',
            default => 'هذا الرمز صالح ولكنه لم يُربط بجهاز بعد.',
        };

        $statusLabel = match ($status) {
            'active' => 'ساري',
            'expired' => 'منتهي',
            default => 'غير مفعل',
        };

        $activationDate = $warranty->activated_at?->format('Y-m-d');
        $expiryDate = $warranty->warranty_expires_at?->format('Y-m-d');

        $durationText = '--';

        if ($isActivated && $warranty->warranty_expires_at) {
            if ($isExpired) {
                $durationText = 'انتهت الكفالة بتاريخ '.$expiryDate;
            } else {
                $durationText =
                    ($remaining['days'] ?? 0).' يوم، '.
                    ($remaining['hours'] ?? 0).' ساعة، '.
                    ($remaining['minutes'] ?? 0).' دقيقة';
            }
        }
    @endphp

    <div class="warranty-page">
        <div class="warranty-container">

            {{-- الشعار --}}
            <header class="brand-header">
                @if(file_exists(public_path('images/iphone-azaz-logo.png')))
                    <img
                        src="{{ asset('images/iphone-azaz-logo.png') }}"
                        alt="iPhone Azaz"
                        class="brand-logo-image"
                    >
                @else
                    <h1 class="brand-fallback">
                        iPhone Azaz
                    </h1>

                    <div class="brand-subtitle">
                        WARRANTY SERVICE
                    </div>
                @endif

                <div class="top-meta">
                    <div class="meta-item">
                        <span class="meta-label">
                            رقم الكفالة:
                        </span>

                        <span class="meta-value">
                            {{ $warranty->short_reference }}
                        </span>
                    </div>

                    <div class="meta-item">
                        <span class="meta-label">
                            تاريخ التفعيل:
                        </span>

                        <span class="meta-value">
                            {{ $activationDate ?: '--' }}
                        </span>
                    </div>
                </div>
            </header>

            <section class="warranty-card">
                <div class="card-accent"></div>

                <div class="card-body">
                    <div class="status-header">
                        <div class="status-heading">
                            <div class="status-icon {{ $status }}">
                                @if($status === 'active')
                                    <svg
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                @elseif($status === 'expired')
                                    <svg
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                @else
                                    <svg
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L4.23 16.5c-.77.833.192 2.5 1.732 2.5z"
                                        />
                                    </svg>
                                @endif
                            </div>

                            <div>
                                <h2 class="status-title">
                                    {{ $statusTitle }}
                                </h2>

                                <p class="status-description">
                                    {{ $statusDescription }}
                                </p>
                            </div>
                        </div>

                        <span
                            id="status-badge"
                            class="status-badge {{ $status }}"
                        >
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="warranty-layout">

                        {{-- رمز Apple --}}
                        <aside class="device-symbol-panel">
                            <svg
                                class="apple-symbol"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                aria-label="Apple"
                            >
                                <path
                                    d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.34-.07 2.27.74 3.05.8 1.16-.24 2.27-.93 3.51-.84 1.48.12 2.6.7 3.34 1.78-3.05 1.83-2.33 5.85.47 6.98-.56 1.47-1.29 2.93-2.37 4.25zM12.03 7.25C11.88 5.07 13.65 3.27 15.68 3.1c.28 2.52-2.29 4.4-3.65 4.15z"
                                />
                            </svg>

                            <div class="device-brand">
                                iPhone Azaz
                            </div>

                            <div class="device-subtitle">
                                Device Warranty
                            </div>
                        </aside>

                        {{-- بيانات الجهاز --}}
                        <div>
                            <h3 class="details-title">
                                معلومات الجهاز
                            </h3>

                            <div class="details-box">
                                <div class="detail-row">
                                    <div class="detail-label">
                                        نوع الجهاز
                                    </div>

                                    <div class="detail-value">
                                        {{ $warranty->device_type ?: 'غير محدد' }}
                                    </div>
                                </div>

                                <div class="detail-row">
                                    <div class="detail-label">
                                        IMEI الجهاز
                                    </div>

                                    <div class="detail-value ltr">
                                        {{ $warranty->masked_imei ?: 'غير محدد' }}
                                    </div>
                                </div>

                                <div class="detail-row">
                                    <div class="detail-label">
                                        بداية الكفالة
                                    </div>

                                    <div class="detail-value">
                                        {{ $activationDate ?: '--' }}
                                    </div>
                                </div>

                                <div class="detail-row">
                                    <div class="detail-label">
                                        نهاية الكفالة
                                    </div>

                                    <div class="detail-value">
                                        {{ $expiryDate ?: '--' }}
                                    </div>
                                </div>
                            </div>

                            @if($isActivated)
                                <div class="duration-panel">
                                    <div class="duration-label">
                                        مدة الكفالة المتبقية
                                    </div>

                                    <div
                                        id="countdown"
                                        class="duration-value"
                                    >
                                        {{ $durationText }}
                                    </div>

                                    @if(! $isExpired)
                                        <div class="duration-progress">
                                            <div
                                                class="duration-progress-bar"
                                            ></div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="inactive-message">
                                    يرجى مراجعة متجر iPhone Azaz لربط الرمز بالجهاز وتفعيل الكفالة.
                                </div>
                            @endif

                            @if($isExpired)
                                <div
                                    id="expired-message"
                                    class="expired-message"
                                >
                                    انتهت الكفالة بتاريخ
                                    {{ $expiryDate }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="security-note">
                        <svg
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 11c0 1.105-.895 2-2 2s-2-.895-2-2 .895-2 2-2 2 .895 2 2zm0 0v1a2 2 0 002 2h1m4-9a9 9 0 11-14 0"
                            />
                        </svg>

                        <span>
                            هذه الصفحة مخصصة للتحقق من حالة الكفالة فقط. لا تشارك رمز الضمان أو بيانات الجهاز مع أي جهة غير موثوقة.
                        </span>
                    </div>
                </div>
            </section>

            <footer class="page-footer">
                <div>
                    الرقم المرجعي:
                    <span dir="ltr">
                        {{ $warranty->short_reference }}
                    </span>
                </div>

                <div>
                    جميع الحقوق محفوظة © {{ date('Y') }}
                    iPhone Azaz
                </div>
            </footer>
        </div>
    </div>
@endsection

@push('scripts')
    @if(
        $warranty->activated_at &&
        $warranty->warranty_expires_at &&
        ! $warranty->is_expired
    )
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const expiresAt =
                    @json(
                        $warranty->warranty_expires_at
                            ->utc()
                            ->format('Y-m-d\TH:i:s\Z')
                    );

                const countdownElement =
                    document.getElementById('countdown');

                const statusBadge =
                    document.getElementById('status-badge');

                let countdownTimer = null;

                function updateCountdown() {
                    const expiryTimestamp =
                        new Date(expiresAt).getTime();

                    const currentTimestamp =
                        Date.now();

                    const difference =
                        expiryTimestamp - currentTimestamp;

                    if (difference <= 0) {
                        if (countdownElement) {
                            countdownElement.textContent =
                                'انتهت مدة الكفالة';
                        }

                        if (statusBadge) {
                            statusBadge.className =
                                'status-badge expired';

                            statusBadge.textContent =
                                'منتهي';
                        }

                        if (countdownTimer) {
                            clearInterval(countdownTimer);
                        }

                        return;
                    }

                    const days = Math.floor(
                        difference / (1000 * 60 * 60 * 24)
                    );

                    const hours = Math.floor(
                        (
                            difference %
                            (1000 * 60 * 60 * 24)
                        ) /
                        (1000 * 60 * 60)
                    );

                    const minutes = Math.floor(
                        (
                            difference %
                            (1000 * 60 * 60)
                        ) /
                        (1000 * 60)
                    );

                    const seconds = Math.floor(
                        (
                            difference %
                            (1000 * 60)
                        ) /
                        1000
                    );

                    if (countdownElement) {
                        countdownElement.textContent =
                            `${days} يوم، ` +
                            `${hours} ساعة، ` +
                            `${minutes} دقيقة، ` +
                            `${seconds} ثانية`;
                    }
                }

                updateCountdown();

                countdownTimer = setInterval(
                    updateCountdown,
                    1000
                );
            });
        </script>
    @endif
@endpush
