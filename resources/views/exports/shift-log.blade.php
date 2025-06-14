<!DOCTYPE html>
<html>

<head>
    <title>Supervisors Shift Log - {{ $date }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 9px;
            /* further reduced */
            margin: 8px;
            /* tighter margins */
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
            /* smaller logos */
        }

        .title-text {
            font-size: 11px;
            /* reduced */
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
            /* very tight */
            text-align: left;
            vertical-align: top;
            font-size: 8px;
            /* very compact */
            line-height: 1.1;
        }

        table.data-table th {
            background-color: #eee;
        }

        .supervisor-notes {
            min-height: 30px;
            /* less height */
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
                <th style="width: 100px;">Labour </th>
                <th>Notes</th>
                <th>Req</th>
                <th style="text-align: center">Complete (%)</th>
                {{-- <th>Priority</th> --}}
                {{-- <th>Department</th> --}}
                <th>Duration</th>
                <th>Completed</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $index => $log)
                @php
                    $background = match ($log->shift_name) {
                        'night' => 'background-color: #939393a8;',
                        default => '',
                    };
                    if ($log->mark_as_complete == 1) {
                        $background = 'background-color: #ffef3bc2;';
                    }

                @endphp

                <tr style="{{ $background }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ Str::ucfirst($log->shift_name) }}</td>
                    <td>{{ $log->wo_number }}</td>
                    <td>{{ $log->asset_no }}</td>
                    <td>{{ $log->asset_description }}</td>
                    <td>{{ $log->work_description }}</td>
                    <td>{{ $log->labour }}</td>
                    <td>{{ $log->note->note ?? '' }}</td>
                    <td>{{ Str::ucfirst($log->requisition) }}</td>
                    <td style="text-align: center">{{ $log->progress }}</td>
                    {{-- <td>{{ $log->priority }}</td> --}}
                    {{-- <td style="text-align: center">{{ $log->department }}</td> --}}
                    <td style="text-align: center">{{ $log->duration }}</td>
                    <td style="text-align: center">{{ $log->mark_as_complete == 1 ? 'Yes' : 'No' }}</td>
                </tr>
                <tr>
                    <td colspan="12">
                        <div style="min-height: 40px; overflow-wrap: break-word;">
                            {{ $log->supervisor_notes }}
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
