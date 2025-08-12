<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Supervisors Completion Compliance Report</title>
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
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
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
            padding: 4px;
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
            <td style="width: 16%; border: none; text-align: left;">
                <img src="{{ $logo1 }}" alt="Logo" style="width: 100px;">
            </td>
            <td style="width: 68%; border: none; text-align: center;">
                <h1 style="font-size: 18px; font-weight: bold; margin-bottom: 6px;">
                    All Supervisors Completion Compliance Report
                </h1>
                <h2 style="font-size: 14px; font-weight: normal;">
                    <strong>Date Range:</strong> {{ $start_date }} to {{ $end_date }}
                </h2>
            </td>
            <td style="width: 16%; border: none; text-align: right;">
                <img src="{{ $logo2 }}" alt="Logo" style="width: 100px;">
            </td>
        </tr>
    </table>
</div>
<!-- Show overall average completion percentage -->
<h2 style="font-size: 12px; font-weight: bold;">1. Overall Average Supervisors Completion Percentage:</h2>
<table style="margin-bottom: 30px;">
    <thead>
    <th>Supervisor Name</th>
    <th>Average Completion %</th>
    </thead>
    <tbody>
    @forelse($averageProgressData as $supervisor => $averageProgress)
        <tr>
            <td>{{ $supervisor }}</td>
            <td>{{ $averageProgress !== null ? $averageProgress . '%' : '-' }}</td>
        </tr>
    @empty
    @endforelse
    </tbody>
</table>

<h2 style="font-size: 12px; font-weight: bold;">2. Supervisors Completion Compliance Report:</h2>
<table>
    <thead>
    <tr>
        <th>Supervisor Name</th>
        <th>Date</th>
        <th>Completion %</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($reportData as $row)
        <tr>
            <td>{{ $row['supervisor'] }}</td>
            <td>{{ \Carbon\Carbon::parse($row['date'])->format('d-m-Y') }}</td>
            <td>{{ $row['percentage'] !== null ? $row['percentage'] . '%' : '-' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="3">No supervisors found for the given date range.</td>
        </tr>
    @endforelse
    </tbody>
</table>


<div class="footer-note">
    Generated on {{ \Carbon\Carbon::now('Australia/Perth')->format('d-m-Y h:i a') }} (Australia/Perth Timezone)
</div>

</body>
</html>
