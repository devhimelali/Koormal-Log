<!-- Base64 Image Rendering -->
@php
    function getBase64Image($path)
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
@endphp
<!DOCTYPE html>
<html>

<head>
    <title>Supervisors Shift Log - {{ $date }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 9px;
            margin: 8px;
        }

        .logo-title-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .logo-title-table td {
            text-align: center;
            vertical-align: middle;
        }

        .logo-title-table img {
            width: 90px;
        }

        .title-text {
            font-size: 11px;
            font-weight: bold;
        }

        .labour-box {
            margin-bottom: 4px;
            padding: 4px;
            border: 1px solid #4CAF50;
            background-color: #f9f9f9;
            font-size: 8px;
            line-height: 1.2;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #333;
            padding: 2px 3px;
            text-align: left;
            vertical-align: top;
            font-size: 8px;
            line-height: 1.1;
        }

        table.data-table th {
            background-color: #eee;
        }

        .supervisor-notes {
            min-height: 30px;
            overflow-wrap: break-word;
            font-size: 8px;
            padding-top: 2px;
        }
    </style>

</head>

<body>
    <table class="logo-title-table" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 15%;">
                <img src="{{ public_path('assets/logos/koormal-logo.png') }}" alt="Koormal Logo" style="max-width: 100px;">
            </td>
            <td style="width: 70%; text-align: center;">
                <div class="title-text" style="font-size: 18px; font-weight: bold;">
                    SUPERVISORS SHIFT LOG {{ \Carbon\Carbon::parse($date)->format('d-m-y') }}
                </div>
            </td>
            <td style="width: 15%; text-align: right;">
                <img src="{{ public_path('assets/logos/4emus-logo.png') }}" alt="4EMUS Logo" style="max-width: 100px;">
            </td>
        </tr>
        <tr>
            <td colspan="3" style="padding-top: 8px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 30%; padding-right: 5px; vertical-align: top; border-right: 1px solid #ccc;">
                            <div class="labour-box" style="margin-bottom: 5px;">
                                <strong>Supervisor for Dayshift:</strong>
                                <strong
                                    style="margin-left: 5px; color: red;">{{ $supervisorDayYesPercentage }}%</strong><br>
                                {{ $daySupervisor ? implode(', ', $daySupervisor) : 'N/A' }}
                            </div>
                            <div class="labour-box">
                                <strong>Supervisor for Nightshift:</strong><strong
                                    style="margin-left: 5px; color: red;">{{ $supervisorNightYesPercentage }}%</strong><br>
                                {{ $nightSupervisor ? implode(', ', $nightSupervisor) : 'N/A' }}
                            </div>
                        </td>
                        <td style="width: 70%; padding-left: 5px; vertical-align: top;">
                            <div class="labour-box" style="margin-bottom: 5px;">
                                <strong>Labour for Dayshift:</strong><br>
                                {{ $dayLabour->name ?? 'N/A' }}
                            </div>
                            <div class="labour-box">
                                <strong>Labour for Nightshift:</strong><br>
                                {{ $nightLabour->name ?? 'N/A' }}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    @if ($shift == 'both')
        @if ($supervisorDayShiftNotes)
            <!-- Day shift supervisor notes -->
            <div style="margin-bottom: 10px; margin-top: -10px;">
                <h3 style="margin-bottom: 2px;">Supervisor Day Shift Notes</h3>

                <!-- Note Text -->
                <div style="margin-bottom: 10px;">
                    {!! nl2br(e($supervisorDayShiftNotes->note)) !!}
                </div>

                @if ($supervisorDayShiftNotes?->media && $supervisorDayShiftNotes->media->count())
                    <div style="margin-top: 20px;">
                        @foreach ($supervisorDayShiftNotes->media as $media)
                            @php
                                $path = public_path($media->url); // e.g. 'uploads/images/x.jpg'
                                $base64 = file_exists($path) ? getBase64Image($path) : '';
                            @endphp
                            @if ($base64)
                                <img src="{{ $base64 }}"
                                    style="max-width: 100px; max-height: 80px; margin: 5px; border: 1px solid #ccc;">
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
        <table class="data-table" style="margin-bottom: 30px">
            <thead>
                <tr>
                    <th style="vertical-align: middle;">#</th>
                    <th style="vertical-align: middle;">Shift</th>
                    <th style="vertical-align: middle;">WO Number</th>
                    <th style="vertical-align: middle;">Asset No</th>
                    <th style="vertical-align: middle;">Asset Description</th>
                    <th style="vertical-align: middle;">Work Description</th>
                    <th style="width: 100px; vertical-align: middle;">Labour</th>
                    <th style="vertical-align: middle;">Notes</th>
                    <th style="vertical-align: middle;">Req</th>
                    <th style="vertical-align: middle;">
                        <div style="line-height: 1.2;">Scheduled</div>
                        <div style="line-height: 1.2; color:red;">Day: {{ $dayLogPercentage }}%</div>
                    </th>
                    <th style="text-align: center; vertical-align: middle;">Complete (%)</th>
                    <th style="vertical-align: middle;">Duration</th>
                    <th style="vertical-align: middle;">Completed</th>
                </tr>
            </thead>
            <tbody>
                <!-- Day shift logs -->
                @forelse($dayLogs as $log)
                    @php
                        $background = $log->mark_as_complete == 1 ? 'background-color: #ffef3bc2;' : '';
                    @endphp
                    <tr style="{{ $background }}">
                        <td style="vertical-align: middle;">{{ $loop->iteration }}</td>
                        <td style="vertical-align: middle;">{{ Str::ucfirst($log->shift_name) }}</td>
                        <td style="vertical-align: middle;">{{ $log->wo_number }}</td>
                        <td style="vertical-align: middle;">{{ $log->asset_no }}</td>
                        <td style="vertical-align: middle;">{{ $log->asset_description }}</td>
                        <td style="vertical-align: middle;">{{ $log->work_description }}</td>
                        <td style="vertical-align: middle;">{{ $log->labour }}</td>
                        <td style="vertical-align: middle;">{{ $log->note->note ?? '' }}</td>
                        <td style="vertical-align: middle;">{{ Str::ucfirst($log->requisition) }}</td>
                        <td style="text-align: center; vertical-align: middle;">{{ ucfirst($log->scheduled) }}</td>
                        <td style="text-align: center; vertical-align: middle;">{{ $log->progress }}</td>
                        <td style="text-align: center; vertical-align: middle;">{{ $log->duration }}</td>
                        <td style="text-align: center; vertical-align: middle;">
                            {{ $log->mark_as_complete == 1 ? 'Yes' : 'No' }}</td>
                    </tr>
                    <tr>
                        <td colspan="12">
                            <div
                                style="{{ $log->supervisor_notes == null ? 'min-height: 5px;' : 'overflow-wrap: break-word;' }}">
                                {!! nl2br(e($log->supervisor_notes)) !!}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" style="text-align: center">No data found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($supervisorNightShiftNotes)
            <!-- Night shift supervisor notes -->
            <div style="margin-bottom: 10px; margin-top: -10px;">
                <h3 style="margin-bottom: 2px;">Supervisor Night Shift Notes</h3>

                <!-- Note Text -->
                <div style="margin-bottom: 10px;">
                    {!! nl2br(e($supervisorNightShiftNotes->note)) !!}
                </div>

                @if ($supervisorNightShiftNotes?->media && $supervisorNightShiftNotes->media->count())
                    <div style="margin-top: 20px;">
                        @foreach ($supervisorNightShiftNotes->media as $media)
                            @php
                                $path = public_path($media->url); // e.g. 'uploads/images/x.jpg'
                                $base64 = file_exists($path) ? getBase64Image($path) : '';
                            @endphp
                            @if ($base64)
                                <img src="{{ $base64 }}"
                                    style="max-width: 100px; max-height: 80px; margin: 5px; border: 1px solid #ccc;">
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
        <table class="data-table">
            <thead>
                <tr>
                    <th style="vertical-align: middle">#</th>
                    <th style="vertical-align: middle">Shift</th>
                    <th style="vertical-align: middle">WO Number</th>
                    <th style="vertical-align: middle">Asset No</th>
                    <th style="vertical-align: middle">Asset Description</th>
                    <th style="vertical-align: middle">Work Description</th>
                    <th style="width: 100px; vertical-align: middle;">Labour</th>
                    <th style="vertical-align: middle">Notes</th>
                    <th style="vertical-align: middle">Req</th>
                    <th style="vertical-align: middle">
                        <div style="line-height: 1.2;">Scheduled</div>
                        <div style="line-height: 1.2; color:red;">Night: {{ $nightLogPercentage }}%</div>
                    </th>
                    <th style="text-align: center; vertical-align: middle;">Complete (%)</th>
                    <th style="vertical-align: middle">Duration</th>
                    <th style="vertical-align: middle">Completed</th>
                </tr>
            </thead>
            <tbody>
                @forelse($nightLogs as $log)
                    @php
                        $background =
                            $log->mark_as_complete == 1
                                ? 'background-color: #4a91e29d;'
                                : 'background-color: #939393a8;';
                    @endphp
                    <tr style="{{ $background }}">
                        <td style="vertical-align: middle;">{{ $loop->iteration }}</td>
                        <td style="vertical-align: middle;">{{ Str::ucfirst($log->shift_name) }}</td>
                        <td style="vertical-align: middle;">{{ $log->wo_number }}</td>
                        <td style="vertical-align: middle;">{{ $log->asset_no }}</td>
                        <td style="vertical-align: middle;">{{ $log->asset_description }}</td>
                        <td style="vertical-align: middle;">{{ $log->work_description }}</td>
                        <td style="vertical-align: middle;">{{ $log->labour }}</td>
                        <td style="vertical-align: middle;">{{ $log->note->note ?? '' }}</td>
                        <td style="vertical-align: middle;">{{ Str::ucfirst($log->requisition) }}</td>
                        <td style="text-align: center; vertical-align: middle;">{{ ucfirst($log->scheduled) }}</td>
                        <td style="text-align: center; vertical-align: middle;">{{ $log->progress }}</td>
                        <td style="text-align: center; vertical-align: middle;">{{ $log->duration }}</td>
                        <td style="text-align: center; vertical-align: middle;">
                            {{ $log->mark_as_complete == 1 ? 'Yes' : 'No' }}</td>
                    </tr>
                    <tr>
                        <td colspan="12">
                            <div
                                style="{{ $log->supervisor_notes == null ? 'min-height: 5px;' : 'overflow-wrap: break-word;' }}">
                                {!! nl2br(e($log->supervisor_notes)) !!}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" style="text-align: center">No data found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        @if ($supervisorNotes)
            <div style="margin-bottom: 10px; margin-top: -10px;">
                <h3 style="margin-bottom: 2px;">Supervisor {{ ucfirst($shift) }} Shift Notes</h3>

                <!-- Note Text -->
                <div style="margin-bottom: 10px;">
                    {!! nl2br(e($supervisorNotes->note)) !!}
                </div>

                @if ($supervisorNotes?->media && $supervisorNotes->media->count())
                    <div style="margin-top: 20px;">
                        @foreach ($supervisorNotes->media as $media)
                            @php
                                $path = public_path($media->url);
                                $base64 = file_exists($path) ? getBase64Image($path) : '';
                            @endphp
                            @if ($base64)
                                <img src="{{ $base64 }}"
                                    style="max-width: 100px; max-height: 80px; margin: 5px; border: 1px solid #ccc;">
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
        <table class="data-table">
            <thead>
                <tr>
                    <th style="vertical-align: middle;">#</th>
                    <th style="vertical-align: middle;">Shift</th>
                    <th style="vertical-align: middle;">WO Number</th>
                    <th style="vertical-align: middle;">Asset No</th>
                    <th style="vertical-align: middle;">Asset Description</th>
                    <th style="vertical-align: middle;">Work Description</th>
                    <th style="width: 100px; vertical-align: middle;">Labour</th>
                    <th style="vertical-align: middle;">Notes</th>
                    <th style="vertical-align: middle;">Req</th>
                    <th style="vertical-align: middle;">
                        <div style="line-height: 1.2;">Scheduled</div>
                        <div style="line-height: 1.2; color: red;">{{ ucfirst($shift) }}: {{ $logPercentage }}%</div>
                    </th>
                    <th style="text-align: center; vertical-align: middle;">Complete (%)</th>
                    <th style="vertical-align: middle;">Duration</th>
                    <th style="vertical-align: middle;">Completed</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $index => $log)
                    @php
                        $background = '';

                        if ($log->mark_as_complete == 1 && $log->shift_name === 'day') {
                            $background = 'background-color: #ffef3bc2;';
                        } elseif ($log->mark_as_complete === 1 && $log->shift_name === 'night') {
                            $background = 'background-color: #4a91e29d;';
                        } elseif ($log->shift_name === 'night') {
                            $background = 'background-color: #939393a8;';
                        }
                    @endphp
                    <tr style="{{ $background }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ Str::ucfirst($log->shift_name) }}</td>
                        <td>{{ $log->wo_number }}</td>
                        <td>{{ $log->asset_no }}</td>
                        <td>{{ $log->asset_description }}</td>
                        <td>{{ $log->work_description }}</td>
                        <td>{{ $log->labour }}</td>
                        <td>{{ $log->note->note ?? '' }}</td>
                        <td>{{ Str::ucfirst($log->requisition) }}</td>
                        <td>{{ Str::ucfirst($log->scheduled) }}</td>
                        <td style="text-align: center">{{ $log->progress }}</td>
                        <td style="text-align: center">{{ $log->duration }}</td>
                        <td style="text-align: center">{{ $log->mark_as_complete == 1 ? 'Yes' : 'No' }}</td>
                    </tr>
                    <tr>
                        <td colspan="12">
                            <div
                                style="{{ $log->supervisor_notes == null ? 'min-height: 5px;' : 'overflow-wrap: break-word;' }}">
                                {!! nl2br(e($log->supervisor_notes)) !!}
                            </div>
                        </td>
                    @empty
                @endforelse
            </tbody>
        </table>
    @endif
    <script type="text/php">
    if (isset($pdf)) {
        date_default_timezone_set('Australia/Perth'); // Set timezone

        $font = $fontMetrics->getFont('Helvetica', 'normal');
        $size = 8;
        $color = [0, 0, 0];
        $x = 35;
        $y = $pdf->get_height() - 35;

        // Page number
        $pageText = "Page $PAGE_NUM of $PAGE_COUNT";
        $pdf->text($x, $y, $pageText, $font, $size, $color);

        // Date/time
        $datetime = date('d-m-Y H:i:s A');
        $rightText = "Generated at: $datetime (Australia Perth Time)";
        $textWidth = $fontMetrics->getTextWidth($rightText, $font, $size);
        $pdf->text($pdf->get_width() - $textWidth - 35, $y, $rightText, $font, $size, $color);
    }
</script>

</body>

</html>
