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
            margin: 0 0 8px 0;
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
            margin-bottom: 20px;
        }

        thead th {
            background: #efefef;
            border: 1px solid #ccc;
            padding: 8px;
            font-weight: 600;
            text-align: center;
        }

        tbody td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        tfoot td {
            font-weight: bold;
            background: #f4f4f4;
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        .footer-note {
            font-size: 10px;
            color: #888;
            text-align: left;
            position: fixed;
            bottom: 10px;
            left: 20px;
        }
    </style>
</head>
<body>

<div class="header">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 10%; border: none; text-align: left;">
                <img src="{{ $logo1 }}" alt="Logo" style="width: 80px;">
            </td>
            <td style="width: 80%; border: none; text-align: center;">
                <h1 style="font-size: 16px; font-weight: bold; margin-bottom: 4px;">
                    Supervisor Completion Compliance Report
                </h1>
                <h2 style="font-size: 14px; font-weight: normal; margin-bottom: 2px;">
                    <strong>Supervisor:</strong> {{ $supervisor ?? 'N/A' }}
                </h2>
                <h2 style="font-size: 14px; font-weight: normal;">
                    <strong>Date Range:</strong> {{ $start_date }} to {{ $end_date }}
                </h2>
            </td>
            <td style="width: 10%; border: none; text-align: right;">
                <img src="{{ $logo2 }}" alt="Logo" style="width: 80px;">
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
    @forelse ($reportData as $data)
        <tr>
            <td>{{ \Carbon\Carbon::parse($data['date'])->format('d-m-Y') }}</td>
            <td>{{ $data['percentage'] !== null ? $data['percentage'] . '%' : '-' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="2">No data available for this supervisor in the given date range.</td>
        </tr>
    @endforelse
    </tbody>
    <tfoot>
    <tr>
        <td>Average</td>
        <td>{{ $averageProgress !== null ? $averageProgress . '%' : '-' }}</td>
    </tr>
    </tfoot>
</table>

<div class="footer-note">
    Generated on {{ \Carbon\Carbon::now('Australia/Perth')->format('d-m-Y h:i a') }} (Australia/Perth Timezone)
</div>

</body>
</html>
