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

<table class="logo-title-table">
    <tr>
        <td style="width: 25%;">
            <img src="{{ public_path('assets/logos/koormal-logo.png') }}" alt="Koormal Logo">
        </td>
        <td style="width: 50%;">
            <div class="title-text">
                SUPERVISORS SHIFT LOG – {{ \Carbon\Carbon::parse($date)->format('d-m-y') }}
            </div>

            <div class="labour-box"><strong>Labour for Dayshift:</strong><br> {{ implode(', ', $dayLabour) }}</div>
            <div class="labour-box"><strong>Labour for Nightshift:</strong> <br> {{ implode(', ', $nightLabour) }}
            </div>
        </td>
        <td style="width: 25%;">
            <img src="{{ public_path('assets/logos/4emus-logo.png') }}" alt="4EMUS Logo">
        </td>
    </tr>
</table>
<table class="data-table">
    <thead>
    <tr>
        <th>#</th>
        <th>Shift</th>
        <th>WO Number</th>
        <th>Asset No</th>
        <th>Asset Description</th>
        <th>Work Description</th>
        <th style="width: 100px;">Labour</th>
        <th>Notes</th>
        <th>Req</th>
        <th style="text-align: center">Complete (%)</th>
        <th>Duration</th>
        <th>Completed</th>
    </tr>
    </thead>
    <tbody>
    @if($shift == 'both')
        {{--  Day Shift Logs  --}}
        @forelse($dayLogs as $log)
            @php
                $background = $log->mark_as_complete == 1 ? 'background-color: #ffef3bc2;' : '';
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
                <td style="text-align: center">{{ $log->progress }}</td>
                <td style="text-align: center">{{ $log->duration }}</td>
                <td style="text-align: center">{{ $log->mark_as_complete == 1 ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <td colspan="12">
                    <div style="{{$log->supervisor_notes == null ? 'min-height: 5px;' : 'overflow-wrap: break-word;'}}">
                        {{ $log->supervisor_notes }}
                    </div>
                </td>
            </tr>
        @empty
        @endforelse
        {{--  Night Shift Logs  --}}
        @forelse($nightLogs as $log)
            @php
                $background = $log->mark_as_complete == 1 ? 'background-color: #ffef3bc2;' : 'background-color: #939393a8;';
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
                <td style="text-align: center">{{ $log->progress }}</td>
                <td style="text-align: center">{{ $log->duration }}</td>
                <td style="text-align: center">{{ $log->mark_as_complete == 1 ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <td colspan="12">
                    <div style="{{$log->supervisor_notes == null ? 'min-height: 5px;' : 'overflow-wrap: break-word;'}}">
                        {{ $log->supervisor_notes }}
                    </div>
                </td>
            </tr>
        @empty
        @endforelse
    @else
        {{--  Dynamic Shift Logs  --}}
        @forelse($logs as $index => $log)
            @php
                $background = '';

                if ($log->mark_as_complete == 1) {
                    $background = 'background-color: #ffef3bc2;';
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
                <td style="text-align: center">{{ $log->progress }}</td>
                <td style="text-align: center">{{ $log->duration }}</td>
                <td style="text-align: center">{{ $log->mark_as_complete == 1 ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <td colspan="12">
                    <div style="{{$log->supervisor_notes == null ? 'min-height: 5px;' : 'overflow-wrap: break-word;'}}">
                        {{ $log->supervisor_notes }}
                    </div>
                </td>
        @empty
        @endforelse
    @endif
    </tbody>
</table>
<script type="text/php">
    if (isset($pdf)) {
        $pdf->page_script('
            $text = __("Page :pageNum/:pageCount", ["pageNum" => $PAGE_NUM, "pageCount" => $PAGE_COUNT]);
            $font = null;
            $size = 9;
            $color = array(0,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default

            // Compute text width to center correctly
            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);

            $x = ($pdf->get_width() - $textWidth) - 38;
            $y = $pdf->get_height() - 35;

            $pdf->text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        ');
    }
</script>
</body>
</html>
