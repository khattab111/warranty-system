<x-filament-panels::page>
    <style>
        .qr-page {
            --qr-border: #e5e7eb;
            --qr-muted: #6b7280;
            --qr-heading: #111827;
            --qr-background: #ffffff;
            --qr-soft-background: #f9fafb;
            --qr-primary: #f59e0b;
            --qr-success: #16a34a;
            --qr-warning: #d97706;
            --qr-info: #2563eb;

            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .dark .qr-page {
            --qr-border: rgba(255, 255, 255, 0.1);
            --qr-muted: #9ca3af;
            --qr-heading: #f9fafb;
            --qr-background: #111827;
            --qr-soft-background: rgba(255, 255, 255, 0.04);
        }

        .qr-panel {
            overflow: hidden;
            border: 1px solid var(--qr-border);
            border-radius: 0.75rem;
            background: var(--qr-background);
            box-shadow:
                0 1px 2px rgba(0, 0, 0, 0.04),
                0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .qr-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--qr-border);
            background: var(--qr-soft-background);
        }

        .qr-panel-title {
            margin: 0;
            color: var(--qr-heading);
            font-size: 1rem;
            font-weight: 700;
        }

        .qr-panel-description {
            margin: 0.25rem 0 0;
            color: var(--qr-muted);
            font-size: 0.8125rem;
        }

        .qr-panel-body {
            padding: 1.25rem;
        }

        .qr-generate-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: end;
            gap: 1rem;
        }

        .qr-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }

        .qr-stat {
            padding: 1rem 1.125rem;
            border: 1px solid var(--qr-border);
            border-radius: 0.75rem;
            background: var(--qr-background);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .qr-stat-label {
            color: var(--qr-muted);
            font-size: 0.8125rem;
            font-weight: 500;
        }

        .qr-stat-value {
            margin-top: 0.4rem;
            color: var(--qr-heading);
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1;
        }

        .qr-stat-value.primary {
            color: var(--qr-info);
        }

        .qr-stat-value.success {
            color: var(--qr-success);
        }

        .qr-actions-panel {
            position: sticky;
            top: 1rem;
            z-index: 20;
            padding: 1rem;
            border: 1px solid var(--qr-border);
            border-radius: 0.75rem;
            background: color-mix(in srgb, var(--qr-background) 96%, transparent);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(10px);
        }

        .qr-actions-layout {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .qr-actions-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
        }

        .qr-loading {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-top: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 0.625rem;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .dark .qr-loading {
            background: rgba(37, 99, 235, 0.12);
            color: #93c5fd;
        }

        .qr-table-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            min-height: 4.5rem;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--qr-border);
        }

        .qr-table-toolbar-title {
            margin: 0;
            color: var(--qr-heading);
            font-size: 1rem;
            font-weight: 700;
        }

        .qr-table-toolbar-text {
            margin: 0.25rem 0 0;
            color: var(--qr-muted);
            font-size: 0.8125rem;
        }

        .qr-count-badge {
            display: inline-flex;
            align-items: center;
            min-height: 1.75rem;
            padding: 0.25rem 0.7rem;
            border-radius: 999px;
            background: var(--qr-soft-background);
            color: var(--qr-muted);
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .qr-table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .qr-table {
            width: 100%;
            min-width: 850px;
            border-collapse: separate;
            border-spacing: 0;
            color: var(--qr-heading);
            text-align: right;
            font-size: 0.875rem;
        }

        .qr-table thead th {
            height: 3.5rem;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--qr-border);
            background: var(--qr-soft-background);
            color: #374151;
            font-size: 0.75rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .dark .qr-table thead th {
            color: #d1d5db;
        }

        .qr-table tbody td {
            height: 4.25rem;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--qr-border);
            vertical-align: middle;
        }

        .qr-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .qr-table tbody tr {
            transition:
                background-color 150ms ease,
                box-shadow 150ms ease;
        }

        .qr-table tbody tr:hover {
            background: var(--qr-soft-background);
        }

        .qr-table tbody tr.is-selected {
            background: #fffbeb;
            box-shadow: inset -3px 0 0 var(--qr-primary);
        }

        .dark .qr-table tbody tr.is-selected {
            background: rgba(245, 158, 11, 0.08);
        }

        .qr-checkbox {
            width: 1rem;
            height: 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.25rem;
            accent-color: var(--qr-primary);
            cursor: pointer;
        }

        .qr-reference-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .qr-reference-icon {
            display: flex;
            width: 2.35rem;
            height: 2.35rem;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border-radius: 0.6rem;
            background: var(--qr-soft-background);
            color: var(--qr-muted);
        }

        .qr-reference {
            margin: 0;
            color: var(--qr-heading);
            font-family: monospace;
            font-size: 0.875rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            direction: ltr;
            text-align: right;
        }

        .qr-record-id {
            margin-top: 0.2rem;
            color: var(--qr-muted);
            font-size: 0.7rem;
        }

        .qr-date {
            color: var(--qr-muted);
            direction: ltr;
            text-align: right;
            white-space: nowrap;
        }

        .qr-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            min-height: 1.65rem;
            padding: 0.2rem 0.6rem;
            border: 1px solid transparent;
            border-radius: 0.45rem;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1;
            white-space: nowrap;
        }

        .qr-badge::before {
            width: 0.4rem;
            height: 0.4rem;
            border-radius: 999px;
            background: currentColor;
            content: "";
            opacity: 0.8;
        }

        .qr-badge-gray {
            border-color: #e5e7eb;
            background: #f9fafb;
            color: #4b5563;
        }

        .qr-badge-warning {
            border-color: #fde68a;
            background: #fffbeb;
            color: #b45309;
        }

        .qr-badge-success {
            border-color: #bbf7d0;
            background: #f0fdf4;
            color: #15803d;
        }

        .dark .qr-badge-gray {
            border-color: rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.05);
            color: #d1d5db;
        }

        .dark .qr-badge-warning {
            border-color: rgba(245, 158, 11, 0.25);
            background: rgba(245, 158, 11, 0.1);
            color: #fbbf24;
        }

        .dark .qr-badge-success {
            border-color: rgba(34, 197, 94, 0.25);
            background: rgba(34, 197, 94, 0.1);
            color: #4ade80;
        }

        .qr-action-cell {
            text-align: center;
        }

        .qr-empty {
            padding: 4rem 1rem;
            border: 1px dashed var(--qr-border);
            border-radius: 0.75rem;
            background: var(--qr-background);
            color: var(--qr-muted);
            text-align: center;
        }

        .qr-empty h3 {
            margin: 0;
            color: var(--qr-heading);
            font-size: 1rem;
        }

        .qr-empty p {
            margin: 0.5rem 0 0;
            font-size: 0.875rem;
        }

        @media (max-width: 1100px) {
            .qr-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .qr-actions-layout {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        @media (max-width: 700px) {
            .qr-generate-layout {
                grid-template-columns: 1fr;
            }

            .qr-stats {
                grid-template-columns: 1fr;
            }

            .qr-table-toolbar {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>

    <div class="qr-page">

        {{-- إنشاء الرموز --}}
        <section class="qr-panel">
            <div class="qr-panel-header">
                <div>
                    <h2 class="qr-panel-title">توليد رموز الضمان</h2>

                    <p class="qr-panel-description">
                        أنشئ رموز ضمان جديدة وحدد الرموز المطلوبة للطباعة.
                    </p>
                </div>

                <x-filament::icon
                    icon="heroicon-o-qr-code"
                    style="width: 1.5rem; height: 1.5rem; color: #f59e0b;"
                />
            </div>

            <form wire:submit="generate" class="qr-panel-body">
                <div class="qr-generate-layout">
                    <div>
                        {{ $this->form }}
                    </div>

                    <x-filament::button
                        type="submit"
                        icon="heroicon-o-plus"
                        size="lg"
                        wire:loading.attr="disabled"
                        wire:target="generate"
                    >
                        <span wire:loading.remove wire:target="generate">
                            توليد الرموز
                        </span>

                        <span wire:loading wire:target="generate">
                            جارٍ التوليد...
                        </span>
                    </x-filament::button>
                </div>
            </form>
        </section>

        @if($generatedWarranties?->isNotEmpty())
            @php
                $totalCount = $generatedWarranties->count();
                $selectedCount = count($selectedForPrint);

                $printedCount = $generatedWarranties
                    ->filter(fn ($warranty) => $warranty->printed_at !== null)
                    ->count();

                $unprintedCount = $totalCount - $printedCount;

                $linkedCount = $generatedWarranties
                    ->filter(
                        fn ($warranty) =>
                            $warranty->workflow_status === 'linked_completed'
                    )
                    ->count();
            @endphp

            {{-- الإحصائيات --}}
            <section class="qr-stats">
                <div class="qr-stat">
                    <div class="qr-stat-label">إجمالي الرموز</div>
                    <div class="qr-stat-value">{{ $totalCount }}</div>
                </div>

                <div class="qr-stat">
                    <div class="qr-stat-label">الرموز المحددة</div>
                    <div class="qr-stat-value primary">
                        {{ $selectedCount }}
                    </div>
                </div>

                <div class="qr-stat">
                    <div class="qr-stat-label">غير مطبوع</div>
                    <div class="qr-stat-value">{{ $unprintedCount }}</div>
                </div>

                <div class="qr-stat">
                    <div class="qr-stat-label">مرتبط ومكتمل</div>
                    <div class="qr-stat-value success">
                        {{ $linkedCount }}
                    </div>
                </div>
            </section>

            {{-- الإجراءات --}}
            <section class="qr-actions-panel">
                <div class="qr-actions-layout">
                    <div class="qr-actions-group">
                        <x-filament::button
                            wire:click="downloadPdf"
                            wire:loading.attr="disabled"
                            wire:target="downloadPdf"
                            icon="heroicon-o-printer"
                            color="warning"
                        >
                            طباعة الكل PDF
                        </x-filament::button>

                        <x-filament::button
                            wire:click="downloadSelectedPdf"
                            wire:loading.attr="disabled"
                            wire:target="downloadSelectedPdf"
                            icon="heroicon-o-document-arrow-down"
                            color="info"
                            :disabled="$selectedCount === 0"
                        >
                            طباعة المحدد PDF
                        </x-filament::button>

                        <x-filament::button
                            wire:click="downloadZip"
                            wire:loading.attr="disabled"
                            wire:target="downloadZip"
                            icon="heroicon-o-archive-box-arrow-down"
                            color="success"
                        >
                            تنزيل الكل ZIP
                        </x-filament::button>

                        <x-filament::button
                            wire:click="downloadSelectedZip"
                            wire:loading.attr="disabled"
                            wire:target="downloadSelectedZip"
                            icon="heroicon-o-archive-box"
                            color="success"
                            outlined
                            :disabled="$selectedCount === 0"
                        >
                            تنزيل المحدد ZIP
                        </x-filament::button>
                    </div>

                    <div class="qr-actions-group">
                        <x-filament::button
                            wire:click="selectAll"
                            icon="heroicon-o-check"
                            color="gray"
                            outlined
                        >
                            تحديد الكل
                        </x-filament::button>

                        <x-filament::button
                            wire:click="clearSelection"
                            icon="heroicon-o-x-mark"
                            color="gray"
                            outlined
                        >
                            إلغاء التحديد
                        </x-filament::button>

                        <x-filament::button
                            wire:click="refreshList"
                            icon="heroicon-o-arrow-path"
                            color="gray"
                            outlined
                        >
                            تحديث
                        </x-filament::button>

                        <x-filament::button
                            wire:click="markSelectedAsPrinted"
                            wire:confirm="هل تريد تعيين الرموز المحددة كمطبوعة؟"
                            icon="heroicon-o-printer"
                            color="gray"
                            :disabled="$selectedCount === 0"
                        >
                            تعيين المحدد كمطبوع
                        </x-filament::button>
                    </div>
                </div>

                <div
                    wire:loading
                    wire:target="downloadPdf,downloadSelectedPdf,downloadZip,downloadSelectedZip"
                    class="qr-loading"
                >
                    <x-filament::loading-indicator
                        style="width: 1.25rem; height: 1.25rem;"
                    />

                    جارٍ إنشاء الملف، يرجى الانتظار...
                </div>
            </section>

            {{-- جدول الرموز --}}
            <section class="qr-panel">
                <div class="qr-table-toolbar">
                    <div>
                        <h3 class="qr-table-toolbar-title">
                            قائمة رموز الضمان
                        </h3>

                        <p class="qr-table-toolbar-text">
                            تُعرض بيانات الرموز فقط، وتظهر صور QR عند الطباعة أو التنزيل.
                        </p>
                    </div>

                    <span class="qr-count-badge">
                        عدد السجلات: {{ $totalCount }}
                    </span>
                </div>

                <div class="qr-table-wrapper">
                    <table class="qr-table">
                        <thead>
                            <tr>
                                <th style="width: 64px;">تحديد</th>
                                <th>المرجع</th>
                                <th>حالة الطباعة</th>
                                <th>حالة الربط</th>
                                <th>تاريخ الإنشاء</th>
                                <th style="width: 150px; text-align: center;">
                                    الإجراء
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($generatedWarranties as $warranty)
                                @php
                                    $isPrinted =
                                        $warranty->printed_at !== null;

                                    $isLinked =
                                        $warranty->workflow_status ===
                                        'linked_completed';

                                    $isSelected = in_array(
                                        (int) $warranty->id,
                                        array_map(
                                            'intval',
                                            $selectedForPrint
                                        ),
                                        true
                                    );
                                @endphp

                                <tr
                                    wire:key="warranty-row-{{ $warranty->id }}"
                                    @class([
                                        'is-selected' => $isSelected,
                                    ])
                                >
                                    <td>
                                        <input
                                            type="checkbox"
                                            wire:model.live="selectedForPrint"
                                            value="{{ $warranty->id }}"
                                            class="qr-checkbox"
                                            aria-label="تحديد الرمز {{ $warranty->short_reference }}"
                                        >
                                    </td>

                                    <td>
                                        <div class="qr-reference-cell">
                                            <div class="qr-reference-icon">
                                                <x-filament::icon
                                                    icon="heroicon-o-qr-code"
                                                    style="width: 1.2rem; height: 1.2rem;"
                                                />
                                            </div>

                                            <div>
                                                <p class="qr-reference">
                                                    {{ $warranty->short_reference }}
                                                </p>

                                                <div class="qr-record-id">
                                                    السجل رقم {{ $warranty->id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        @if($isPrinted)
                                            <span class="qr-badge qr-badge-warning">
                                                مطبوع
                                            </span>
                                        @else
                                            <span class="qr-badge qr-badge-gray">
                                                غير مطبوع
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($isLinked)
                                            <span class="qr-badge qr-badge-success">
                                                مرتبط ومكتمل
                                            </span>
                                        @else
                                            <span class="qr-badge qr-badge-gray">
                                                غير مرتبط
                                            </span>
                                        @endif
                                    </td>

                                    <td class="qr-date">
                                        {{ $warranty->created_at?->format('Y-m-d H:i') }}
                                    </td>

                                    <td class="qr-action-cell">
                                        <x-filament::button
                                            wire:click="downloadQr({{ $warranty->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="downloadQr({{ $warranty->id }})"
                                            icon="heroicon-o-arrow-down-tray"
                                            size="xs"
                                            color="gray"
                                            outlined
                                        >
                                            تنزيل PNG
                                        </x-filament::button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @else
            <section class="qr-empty">
                <h3>لا توجد رموز ضمان</h3>

                <p>
                    أدخل العدد المطلوب واضغط على زر توليد الرموز.
                </p>
            </section>
        @endif
    </div>
</x-filament-panels::page>
