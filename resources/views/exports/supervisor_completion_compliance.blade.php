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

        .meta {
            margin-bottom: 20px;
            font-size: 13px;
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
                <h1 style="margin-bottom: 2px; padding: 0; line-height: 1; font-size: 16px; font-weight: 700; color: #222;">
                    Supervisor Completion Compliance Report
                </h1>
                <h2 style="margin-bottom: 2px; padding: 0; line-height: 1; font-size: 13px; font-weight: normal; color: #222;">Supervisor: {{ $supervisor ?? 'N/A' }}</h2>
                <h2 style="margin-bottom: 2px; padding: 0; line-height: 1; font-size: 13px; font-weight: normal; color: #222;">Date Range: {{ $start_date }} to {{ $end_date }}</h2>
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
        <th>Day Shift %</th>
        <th>Night Shift %</th>
    </tr>
    </thead>
    <tbody>
    <tbody>
    @foreach ($reportData as $date => $shifts)
        <tr>
            <td>{{ \Carbon\Carbon::createFromFormat('d-M-Y', $date)->format('d-m-Y') }}</td>
            <td>{{ isset($shifts['day']) ? $shifts['day'] . '%' : '-' }}</td>
            <td>{{ isset($shifts['night']) ? $shifts['night'] . '%' : '-' }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td>Average</td>
        <td>{{ isset($averageProgress['day']) ? $averageProgress['day'] . '%' : '-' }}</td>
        <td>{{ isset($averageProgress['night']) ? $averageProgress['night'] . '%' : '-' }}</td>
    </tr>
    </tfoot>
</table>

<div style="font-size: 10px; color: #888; position: fixed; bottom: 10px; left: 20px;">
    Generated on {{ \Carbon\Carbon::now('Australia/Perth')->format('d-m-Y h:i a') }} Australia/Perth Timezone.
</div>
</body>
</html>
