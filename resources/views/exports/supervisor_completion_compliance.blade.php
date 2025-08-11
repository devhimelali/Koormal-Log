<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supervisor Completion Compliance Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h1, h2 {
            margin: 0 0 10px 0;
            font-weight: 700;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        thead th {
            background: #f0f0f0;
            border: 1px solid #ddd;
            padding: 8px;
            font-weight: 600;
            text-align: center;
        }

        tbody td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background: #fafafa;
        }

        tfoot td {
            font-weight: 700;
            background: #f7f7f7;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="header">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 16%; border: none;">
                <img src="{{ $logo1 }}" alt="Logo" style="width: 100px;">
            </td>
            <td style="width: 68%; border: none;">
                <h1 style="font-size: 16px; font-weight: 700; color: #222; margin-bottom: 4px;">
                    Supervisor Completion Compliance Report
                </h1>
                <h2 style="font-size: 13px; font-weight: normal; color: #222; margin-bottom: 1px;">
                    <span style="font-weight: bold;">Shift:</span> {{ $shift ? ucfirst($shift->value) : 'N/A' }}
                </h2>
                <h2 style="font-size: 13px; font-weight: normal; color: #222; margin-bottom: 1px;">
                    <span style="font-weight: bold;">Supervisor:</span> {{ $supervisor ?? 'N/A' }}
                </h2>
                <h2 style="font-size: 13px; font-weight: normal; color: #222;">
                    <span style="font-weight: bold;">Date Range:</span> {{ $start_date }} to {{ $end_date }}
                </h2>
            </td>
            <td style="width: 16%; border: none;">
                <img src="{{ $logo2 }}" alt="Logo" style="width: 100px;">
            </td>
        </tr>
    </table>
</div>

<table>
    <thead>
    <tr>
        <th>Date</th>
        <th>Completion %</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($reportData as $date => $progress)
        <tr>
            <td>{{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</td>
            <td>{{ $progress !== null ? $progress . '%' : '-' }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td>Average</td>
        <td>{{ $averageProgress !== null ? $averageProgress . '%' : '-' }}</td>
    </tr>
    </tfoot>
</table>

<div style="font-size: 10px; color: #888; position: fixed; bottom: 10px; left: 20px;">
    Generated on {{ \Carbon\Carbon::now('Australia/Perth')->format('d-m-Y h:i a') }} Australia/Perth Timezone.
</div>

</body>
</html>
