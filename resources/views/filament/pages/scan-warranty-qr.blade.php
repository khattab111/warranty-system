<x-filament-panels::page>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .scan-page {
            --scan-border: #e5e7eb;
            --scan-background: #ffffff;
            --scan-soft-background: #f9fafb;
            --scan-heading: #111827;
            --scan-muted: #6b7280;
            --scan-primary: #f59e0b;
            --scan-success: #16a34a;
            --scan-danger: #dc2626;

            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .dark .scan-page {
            --scan-border: rgba(255, 255, 255, 0.1);
            --scan-background: #111827;
            --scan-soft-background: rgba(255, 255, 255, 0.04);
            --scan-heading: #f9fafb;
            --scan-muted: #9ca3af;
        }

        .scan-panel {
            overflow: hidden;
            border: 1px solid var(--scan-border);
            border-radius: 0.75rem;
            background: var(--scan-background);
            box-shadow:
                0 1px 2px rgba(0, 0, 0, 0.04),
                0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .scan-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--scan-border);
            background: var(--scan-soft-background);
        }

        .scan-panel-title {
            margin: 0;
            color: var(--scan-heading);
            font-size: 1rem;
            font-weight: 700;
        }

        .scan-panel-description {
            margin: 0.3rem 0 0;
            color: var(--scan-muted);
            font-size: 0.8125rem;
        }

        .scan-panel-body {
            padding: 1.25rem;
        }

        .scan-methods {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .scan-method-card {
            padding: 1.25rem;
            border: 1px solid var(--scan-border);
            border-radius: 0.75rem;
            background: var(--scan-background);
        }

        .scan-method-card.camera-card {
            border-style: dashed;
        }

        .scan-method-heading {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .scan-method-icon {
            display: flex;
            width: 2.75rem;
            height: 2.75rem;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border-radius: 0.7rem;
            background: #fff7ed;
            color: #ea580c;
        }

        .dark .scan-method-icon {
            background: rgba(234, 88, 12, 0.12);
            color: #fb923c;
        }

        .scan-method-title {
            margin: 0;
            color: var(--scan-heading);
            font-size: 0.9375rem;
            font-weight: 700;
        }

        .scan-method-text {
            margin: 0.2rem 0 0;
            color: var(--scan-muted);
            font-size: 0.75rem;
        }

        .scan-camera-container {
            position: relative;
            overflow: hidden;
            min-height: 280px;
            border: 1px solid var(--scan-border);
            border-radius: 0.75rem;
            background: #111827;
        }

        .scan-camera-placeholder {
            display: flex;
            min-height: 280px;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 0.75rem;
            padding: 2rem;
            color: #d1d5db;
            text-align: center;
        }

        .scan-camera-placeholder p {
            margin: 0;
            font-size: 0.8125rem;
        }

        #qr-reader {
            width: 100%;
            min-height: 280px;
            background: #111827;
        }

        #qr-reader video {
            width: 100% !important;
            border-radius: 0.75rem;
        }

        #qr-reader__dashboard_section_csr button,
        #qr-reader__dashboard_section_swaplink {
            font-family: inherit !important;
        }

        .scan-camera-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.6rem;
            margin-top: 1rem;
        }

        .scan-file-label {
            display: inline-flex;
            min-height: 2.25rem;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.45rem 0.8rem;
            border: 1px solid var(--scan-border);
            border-radius: 0.5rem;
            background: var(--scan-background);
            color: var(--scan-heading);
            font-size: 0.8125rem;
            font-weight: 600;
            cursor: pointer;
            transition:
                background-color 150ms ease,
                border-color 150ms ease;
        }

        .scan-file-label:hover {
            border-color: var(--scan-primary);
            background: var(--scan-soft-background);
        }

        .scan-file-label input {
            display: none;
        }

        .scan-manual-box {
            display: flex;
            min-height: 280px;
            flex-direction: column;
            justify-content: center;
        }

        .scan-manual-fields {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 0.75rem;
        }

        .scan-manual-input {
            width: 100%;
            min-height: 2.5rem;
            padding: 0.6rem 0.85rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            background: var(--scan-background);
            color: var(--scan-heading);
            font-family: monospace;
            font-size: 0.875rem;
            direction: ltr;
            text-align: left;
            outline: none;
            transition:
                border-color 150ms ease,
                box-shadow 150ms ease;
        }

        .scan-manual-input:focus {
            border-color: var(--scan-primary);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.14);
        }

        .dark .scan-manual-input {
            border-color: #4b5563;
        }

        .scan-help {
            margin: 0.75rem 0 0;
            color: var(--scan-muted);
            font-size: 0.75rem;
            line-height: 1.7;
        }

        .scan-alert {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem;
            border: 1px solid;
            border-radius: 0.75rem;
        }

        .scan-alert-content {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .scan-alert-danger {
            border-color: #fecaca;
            background: #fef2f2;
            color: #b91c1c;
        }

        .dark .scan-alert-danger {
            border-color: rgba(239, 68, 68, 0.3);
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
        }

        .scan-alert-success {
            border-color: #bbf7d0;
            background: #f0fdf4;
            color: #15803d;
        }

        .dark .scan-alert-success {
            border-color: rgba(34, 197, 94, 0.3);
            background: rgba(34, 197, 94, 0.1);
            color: #86efac;
        }

        .scan-alert-title {
            margin: 0;
            font-size: 0.875rem;
            font-weight: 700;
        }

        .scan-alert-text {
            margin: 0.25rem 0 0;
            font-size: 0.8125rem;
        }

        .scan-result-layout {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            gap: 1rem;
        }

        .scan-info-card {
            padding: 1rem;
            border: 1px solid var(--scan-border);
            border-radius: 0.75rem;
            background: var(--scan-soft-background);
        }

        .scan-info-title {
            margin: 0 0 1rem;
            color: var(--scan-heading);
            font-size: 0.875rem;
            font-weight: 700;
        }

        .scan-reference-box {
            padding: 1rem;
            border-radius: 0.65rem;
            background: var(--scan-background);
            text-align: center;
        }

        .scan-reference-label {
            color: var(--scan-muted);
            font-size: 0.6875rem;
        }

        .scan-reference-value {
            margin-top: 0.35rem;
            color: var(--scan-heading);
            font-family: monospace;
            font-size: 1.125rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            direction: ltr;
        }

        .scan-info-list {
            display: flex;
            flex-direction: column;
            gap: 0;
            margin-top: 1rem;
            border: 1px solid var(--scan-border);
            border-radius: 0.65rem;
            background: var(--scan-background);
        }

        .scan-info-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.75rem;
            border-bottom: 1px solid var(--scan-border);
            font-size: 0.75rem;
        }

        .scan-info-row:last-child {
            border-bottom: 0;
        }

        .scan-info-label {
            color: var(--scan-muted);
        }

        .scan-info-value {
            color: var(--scan-heading);
            font-weight: 600;
            text-align: left;
        }

        .scan-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            min-height: 1.65rem;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            font-size: 0.6875rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .scan-badge::before {
            width: 0.4rem;
            height: 0.4rem;
            border-radius: 999px;
            background: currentColor;
            content: "";
        }

        .scan-badge-gray {
            background: #f3f4f6;
            color: #4b5563;
        }

        .scan-badge-warning {
            background: #fffbeb;
            color: #b45309;
        }

        .scan-badge-success {
            background: #f0fdf4;
            color: #15803d;
        }

        .scan-badge-danger {
            background: #fef2f2;
            color: #b91c1c;
        }

        .dark .scan-badge-gray {
            background: rgba(255, 255, 255, 0.08);
            color: #d1d5db;
        }

        .dark .scan-badge-warning {
            background: rgba(245, 158, 11, 0.12);
            color: #fbbf24;
        }

        .dark .scan-badge-success {
            background: rgba(34, 197, 94, 0.12);
            color: #4ade80;
        }

        .dark .scan-badge-danger {
            background: rgba(239, 68, 68, 0.12);
            color: #f87171;
        }

        .scan-form-card {
            padding: 1.25rem;
            border: 1px solid var(--scan-border);
            border-radius: 0.75rem;
            background: var(--scan-background);
        }

        .scan-form-title {
            margin: 0 0 1rem;
            color: var(--scan-heading);
            font-size: 0.9375rem;
            font-weight: 700;
        }

        .scan-form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            margin-top: 1.25rem;
            padding-top: 1rem;
            border-top: 1px solid var(--scan-border);
        }

        @media (max-width: 950px) {
            .scan-methods,
            .scan-result-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 650px) {
            .scan-manual-fields {
                grid-template-columns: 1fr;
            }

            .scan-panel-header,
            .scan-alert {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>

    <div
        class="scan-page"
        x-data="{
            scanning: false,
            html5QrCode: null,
            cameraError: '',

            async startCamera() {
                this.cameraError = '';

                if (this.scanning) {
                    return;
                }

                if (typeof Html5Qrcode === 'undefined') {
                    this.cameraError =
                        'لم يتم تحميل مكتبة قراءة QR. حدّث الصفحة ثم أعد المحاولة.';

                    return;
                }

                const reader = document.getElementById('qr-reader');

                if (! reader) {
                    return;
                }

                reader.style.display = 'block';

                try {
                    this.html5QrCode = new Html5Qrcode('qr-reader');

                    await this.html5QrCode.start(
                        {
                            facingMode: 'environment',
                        },
                        {
                            fps: 10,
                            qrbox: {
                                width: 250,
                                height: 250,
                            },
                        },
                        async (decodedText) => {
                            await this.stopCamera();
                            await this.submitScannedCode(decodedText);
                        },
                        () => {}
                    );

                    this.scanning = true;
                } catch (error) {
                    this.scanning = false;
                    this.html5QrCode = null;
                    reader.style.display = 'none';

                    this.cameraError =
                        'تعذر تشغيل الكاميرا. تأكد من السماح بالوصول إليها واستخدام HTTPS.';
                }
            },

            async stopCamera() {
                if (! this.html5QrCode) {
                    this.scanning = false;

                    return;
                }

                try {
                    if (this.html5QrCode.isScanning) {
                        await this.html5QrCode.stop();
                    }

                    this.html5QrCode.clear();
                } catch (error) {
                    // لا نعرض خطأ عند محاولة الإيقاف.
                }

                this.html5QrCode = null;
                this.scanning = false;

                const reader = document.getElementById('qr-reader');

                if (reader) {
                    reader.style.display = 'none';
                }
            },

            async scanFromFile(event) {
                this.cameraError = '';

                const file = event.target.files?.[0];

                if (! file) {
                    return;
                }

                if (typeof Html5Qrcode === 'undefined') {
                    this.cameraError =
                        'لم يتم تحميل مكتبة قراءة QR. حدّث الصفحة ثم أعد المحاولة.';

                    event.target.value = '';

                    return;
                }

                const reader = document.getElementById('qr-reader');

                if (! reader) {
                    return;
                }

                await this.stopCamera();

                reader.style.display = 'block';

                const fileScanner = new Html5Qrcode('qr-reader');

                try {
                    const decodedText = await fileScanner.scanFile(file, true);

                    fileScanner.clear();
                    reader.style.display = 'none';

                    await this.submitScannedCode(decodedText);
                } catch (error) {
                    try {
                        fileScanner.clear();
                    } catch (clearError) {
                        // تجاهل خطأ التنظيف.
                    }

                    reader.style.display = 'none';

                    this.cameraError =
                        'تعذر قراءة رمز QR من الصورة المحددة.';
                } finally {
                    event.target.value = '';
                }
            },

            async submitScannedCode(decodedText) {
                this.cameraError = '';

                await $wire.processScan(decodedText);
            },

            async resetScanner() {
                await this.stopCamera();
                this.cameraError = '';
                await $wire.resetScan();
            },
        }"
        x-on:livewire:navigating.window="stopCamera()"
    >
        @if(! $scannedWarranty)
            <section class="scan-panel">
                <div class="scan-panel-header">
                    <div>
                        <h2 class="scan-panel-title">
                            مسح رمز QR
                        </h2>

                        <p class="scan-panel-description">
                            امسح رمز الضمان بالكاميرا، اختر صورة، أو أدخل الرمز يدويًا.
                        </p>
                    </div>

                    <x-filament::icon
                        icon="heroicon-o-camera"
                        style="width: 1.5rem; height: 1.5rem; color: #ea580c;"
                    />
                </div>

                <div class="scan-panel-body">
                    <div class="scan-methods">

                        {{-- الكاميرا --}}
                        <section class="scan-method-card camera-card">
                            <div class="scan-method-heading">
                                <div class="scan-method-icon">
                                    <x-filament::icon
                                        icon="heroicon-o-camera"
                                        style="width: 1.35rem; height: 1.35rem;"
                                    />
                                </div>

                                <div>
                                    <h3 class="scan-method-title">
                                        المسح بالكاميرا
                                    </h3>

                                    <p class="scan-method-text">
                                        وجّه الكاميرا نحو رمز QR.
                                    </p>
                                </div>
                            </div>

                            <div class="scan-camera-container">
                                <div
                                    x-show="! scanning"
                                    class="scan-camera-placeholder"
                                >
                                    <x-filament::icon
                                        icon="heroicon-o-qr-code"
                                        style="width: 3rem; height: 3rem;"
                                    />

                                    <p>
                                        اضغط على تشغيل الكاميرا لبدء المسح.
                                    </p>
                                </div>

                                <div
                                    id="qr-reader"
                                    style="display: none;"
                                    wire:ignore
                                ></div>
                            </div>

                            <div class="scan-camera-actions">
                                <x-filament::button
                                    type="button"
                                    x-show="! scanning"
                                    x-on:click="startCamera()"
                                    icon="heroicon-o-camera"
                                    color="warning"
                                >
                                    تشغيل الكاميرا
                                </x-filament::button>

                                <x-filament::button
                                    type="button"
                                    x-show="scanning"
                                    x-cloak
                                    x-on:click="stopCamera()"
                                    icon="heroicon-o-stop"
                                    color="danger"
                                >
                                    إيقاف الكاميرا
                                </x-filament::button>

                                <label class="scan-file-label">
                                    <x-filament::icon
                                        icon="heroicon-o-photo"
                                        style="width: 1rem; height: 1rem;"
                                    />

                                    اختيار صورة

                                    <input
                                        type="file"
                                        accept="image/*"
                                        x-on:change="scanFromFile($event)"
                                    >
                                </label>
                            </div>

                            <div
                                x-show="cameraError"
                                x-cloak
                                class="scan-alert scan-alert-danger"
                                style="margin-top: 1rem;"
                            >
                                <div class="scan-alert-content">
                                    <x-filament::icon
                                        icon="heroicon-o-exclamation-triangle"
                                        style="width: 1.25rem; height: 1.25rem;"
                                    />

                                    <p
                                        class="scan-alert-text"
                                        x-text="cameraError"
                                    ></p>
                                </div>
                            </div>
                        </section>

                        {{-- الإدخال اليدوي --}}
                        <section class="scan-method-card">
                            <div class="scan-method-heading">
                                <div class="scan-method-icon">
                                    <x-filament::icon
                                        icon=""
                                        style="width: 1.35rem; height: 1.35rem;"
                                    />
                                </div>

                                <div>
                                    <h3 class="scan-method-title">
                                        البحث اليدوي
                                    </h3>

                                    <p class="scan-method-text">
                                        أدخل UUID أو رابط الضمان كاملًا.
                                    </p>
                                </div>
                            </div>

                            <div class="scan-manual-box">
                                <div class="scan-manual-fields">
                                    <input
                                        type="text"
                                        wire:model="manualToken"
                                        wire:keydown.enter.prevent="manualLookup"
                                        class="scan-manual-input"
                                        placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                                        autocomplete="off"
                                    >

                                    <x-filament::button
                                        type="button"
                                        wire:click="manualLookup"
                                        wire:loading.attr="disabled"
                                        wire:target="manualLookup"
                                        icon="heroicon-o-magnifying-glass"
                                        color="gray"
                                    >
                                        <span
                                            wire:loading.remove
                                            wire:target="manualLookup"
                                        >
                                            بحث
                                        </span>

                                        <span
                                            wire:loading
                                            wire:target="manualLookup"
                                        >
                                            جارٍ البحث...
                                        </span>
                                    </x-filament::button>
                                </div>

                                <p class="scan-help">
                                    يمكنك لصق رابط صفحة الضمان، الرمز الكامل، أو UUID الموجود داخل QR.
                                </p>
                            </div>
                        </section>
                    </div>
                </div>
            </section>
        @endif

        {{-- رسالة الخطأ --}}
        @if($errorMessage !== '')
            <section class="scan-alert scan-alert-danger">
                <div class="scan-alert-content">
                    <x-filament::icon
                        icon="heroicon-o-exclamation-triangle"
                        style="width: 1.3rem; height: 1.3rem;"
                    />

                    <div>
                        <h3 class="scan-alert-title">
                            تعذر معالجة الرمز
                        </h3>

                        <p class="scan-alert-text">
                            {{ $errorMessage }}
                        </p>
                    </div>
                </div>

                <x-filament::button
                    type="button"
                    x-on:click="resetScanner()"
                    color="gray"
                    size="sm"
                    outlined
                >
                    إعادة المحاولة
                </x-filament::button>
            </section>
        @endif

        {{-- نتيجة المسح --}}
        @if($scannedWarranty)
            @php
                $isActivated = $scannedWarranty->activated_at !== null;
                $isExpired = $scannedWarranty->status === 'expired';

                $workflowLabel = match (
                    $scannedWarranty->workflow_status
                ) {
                    'unprinted_unlinked' => 'غير مطبوع / غير مرتبط',
                    'printed_pending' => 'مطبوع / غير مرتبط',
                    'linked_completed' => 'مرتبط ومكتمل',
                    default => 'غير معروف',
                };

                $workflowClass = match (
                    $scannedWarranty->workflow_status
                ) {
                    'unprinted_unlinked' => 'scan-badge-gray',
                    'printed_pending' => 'scan-badge-warning',
                    'linked_completed' => 'scan-badge-success',
                    default => 'scan-badge-danger',
                };
            @endphp

            <section class="scan-alert scan-alert-success">
                <div class="scan-alert-content">
                    <x-filament::icon
                        icon="heroicon-o-check-circle"
                        style="width: 1.3rem; height: 1.3rem;"
                    />

                    <div>
                        <h3 class="scan-alert-title">
                            تم العثور على رمز الضمان
                        </h3>

                        <p class="scan-alert-text">
                            المرجع:
                            <strong dir="ltr">
                                {{ $scannedWarranty->short_reference }}
                            </strong>
                        </p>
                    </div>
                </div>
            </section>

            <section class="scan-result-layout">

                {{-- معلومات الرمز --}}
                <aside class="scan-info-card">
                    <h3 class="scan-info-title">
                        معلومات الرمز
                    </h3>

                    <div class="scan-reference-box">
                        <div class="scan-reference-label">
                            الرقم المرجعي
                        </div>

                        <div class="scan-reference-value">
                            {{ $scannedWarranty->short_reference }}
                        </div>
                    </div>

                    <div class="scan-info-list">
                        <div class="scan-info-row">
                            <span class="scan-info-label">
                                حالة التفعيل
                            </span>

                            @if($isActivated)
                                <span class="scan-badge scan-badge-success">
                                    مفعل
                                </span>
                            @else
                                <span class="scan-badge scan-badge-gray">
                                    غير مفعل
                                </span>
                            @endif
                        </div>

                        <div class="scan-info-row">
                            <span class="scan-info-label">
                                مرحلة العمل
                            </span>

                            <span class="scan-badge {{ $workflowClass }}">
                                {{ $workflowLabel }}
                            </span>
                        </div>

                        @if($isActivated)
                            <div class="scan-info-row">
                                <span class="scan-info-label">
                                    حالة الضمان
                                </span>

                                @if($isExpired)
                                    <span class="scan-badge scan-badge-danger">
                                        منتهي
                                    </span>
                                @else
                                    <span class="scan-badge scan-badge-success">
                                        ساري
                                    </span>
                                @endif
                            </div>
                        @endif

                        <div class="scan-info-row">
                            <span class="scan-info-label">
                                تاريخ الإنشاء
                            </span>

                            <span class="scan-info-value">
                                {{ $scannedWarranty->created_at?->format('Y-m-d H:i') }}
                            </span>
                        </div>

                        @if($scannedWarranty->printed_at)
                            <div class="scan-info-row">
                                <span class="scan-info-label">
                                    تاريخ الطباعة
                                </span>

                                <span class="scan-info-value">
                                    {{ $scannedWarranty->printed_at->format('Y-m-d H:i') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </aside>

                {{-- نموذج التفعيل أو التحديث --}}
                <section class="scan-form-card">
                    <h3 class="scan-form-title">
                        {{ $isActivated
                            ? 'تحديث بيانات الضمان'
                            : 'تفعيل الضمان'
                        }}
                    </h3>

                 @php
    $isActivated = $scannedWarranty->activated_at !== null;

    $submitAction = $isActivated
        ? 'updateWarranty'
        : 'activate';
@endphp

<form wire:submit="{{ $submitAction }}">
    {{ $this->form }}

    <div class="scan-form-actions">
        <x-filament::button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="{{ $submitAction }}"
            icon="{{ $isActivated
                ? 'heroicon-o-check'
                : 'heroicon-o-shield-check'
            }}"
            color="success"
        >
            <span
                wire:loading.remove
                wire:target="{{ $submitAction }}"
            >
                {{ $isActivated
                    ? 'حفظ التعديلات'
                    : 'تفعيل الضمان'
                }}
            </span>

            <span
                wire:loading
                wire:target="{{ $submitAction }}"
            >
                جارٍ الحفظ...
            </span>
        </x-filament::button>

        <x-filament::button
            type="button"
            x-on:click="resetScanner()"
            icon="heroicon-o-arrow-path"
            color="gray"
            outlined
        >
            مسح رمز آخر
        </x-filament::button>
    </div>
</form>
                </section>
            </section>
        @endif
    </div>

    @once
        <script
            src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"
            defer
        ></script>
    @endonce
</x-filament-panels::page>
