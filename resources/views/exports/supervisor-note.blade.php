@php
    function getBase64Image($path) {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
@endphp

        <!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supervisor Notes PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 20px;
            font-size: 14px;
        }

        .logo-title-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .logo-title-table td {
            vertical-align: middle;
        }

        .title-text {
            font-size: 22px;
            font-weight: bold;
            color: #1e2d58;
        }

        .header-details p {
            margin: 2px 0;
            font-size: 15px;
        }

        hr {
            border: 0;
            border-top: 2px solid #d1d9f3;
            margin: 10px 0 20px;
        }

        .note-section p {
            line-height: 1.6;
        }

        .note-section strong {
            font-size: 16px;
        }

        .attached-images {
            margin-top: 35px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .attached-images img {
            max-width: 200px;
            max-height: 130px;
            border: 1px solid #aaa;
            padding: 3px;
            background-color: #f9f9f9;
            margin: 3px;
        }
    </style>
</head>
<body>

<table class="logo-title-table">
    <tr>
        <td style="width: 15%;">
            <img src="{{ public_path('assets/logos/koormal-logo.png') }}" alt="Koormal Logo" style="max-width: 100px;">
        </td>
        <td style="width: 70%; text-align: center;">
            <div class="title-text">
                SUPERVISORS NOTE - {{ $log_date }}
            </div>
            <div class="header-details">
                <p><strong>Shift:</strong> {{ $note_type }}</p>
                <p><strong>Supervisor Name:</strong> {{ $supervisor->name ?? 'N/A' }}</p>
            </div>
        </td>
        <td style="width: 15%; text-align: right;">
            <img src="{{ public_path('assets/logos/4emus-logo.png') }}" alt="4EMUS Logo" style="max-width: 100px;">
        </td>
    </tr>
</table>

<hr>

<div class="note-section">
    <p style="margin-bottom: 0; margin-top: -10px;"><strong>Note:</strong></p>
    <p style="margin-top: 0; line-height: 1.3">{!! nl2br(e($supervisor_notes->note)) !!}</p>

    @if($supervisor_notes->media->isNotEmpty())
        <div style="clear: both;"></div>
        <p style="margin-top: 10px;"><strong>Attached Images:</strong></p>
        <div class="attached-images">
            @foreach($supervisor_notes->media as $media)
                @php
                    $path = public_path($media->url);
                    $base64 = file_exists($path) ? getBase64Image($path) : '';
                @endphp
                @if($base64)
                    <div style="display: inline-block; margin: 3px;">
                        <img src="{{ $base64 }}" style="max-width: 200px; max-height: 130px; border: 1px solid #ccc;">
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>

<script type="text/php">
    if (isset($pdf)) {
        $pdf->page_script('
            $text = __("Page :pageNum/:pageCount", ["pageNum" => $PAGE_NUM, "pageCount" => $PAGE_COUNT]);
            $font = null;
            $size = 9;
            $color = [0, 0, 0];
            $x = $pdf->get_width() - 70;
            $y = $pdf->get_height() - 35;
            $pdf->text($x, $y, $text, $font, $size, $color);
        ');
    }
</script>

</body>
</html>
