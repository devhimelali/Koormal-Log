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
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Supervisor Notes PDF</title>
</head>
<body>
<table class="logo-title-table" style="width: 100%; border-collapse: collapse;">
    <tr style="margin-bottom: 8px;">
        <td style="width: 15%;">
            <img src="{{ public_path('assets/logos/koormal-logo.png') }}" alt="Koormal Logo" style="max-width: 100px;">
        </td>
        <td style="width: 70%; text-align: center;">
            <div class="title-text" style="font-size: 18px; font-weight: bold;">
                SUPERVISORS SHIFT LOG - {{$log_date}}
            </div>
            <p style="font-size: 16px; margin-top: 5px; margin-bottom: 0"><strong>Shift:</strong> {{ $note_type }}</p>
            <p style="font-size: 16px; margin-top: 2px; margin-bottom: 0">
                <strong>Supervisor:</strong> {{ $supervisor->name ?? 'N/A' }}</p>
        </td>
        <td style="width: 15%; text-align: right;">
            <img src="{{ public_path('assets/logos/4emus-logo.png') }}" alt="4EMUS Logo" style="max-width: 100px;">
        </td>
    </tr>
</table>
<hr style="color: #d1d9f3; margin-bottom: 0;">
<div style="margin-top: -5px">
    <p style="font-size: 18px; margin-bottom: 10px;">
        <strong>Note:</strong>
    </p>
    <p style="font-size: 14px; margin-top: 5px; margin-bottom: 10px">
        {!! nl2br(e($supervisor_notes->note)) !!}
    </p>
    <p style="font-size: 17px; margin-bottom: 35px;">
        <strong>
            Attached Images
        </strong>
    </p>
    @foreach($supervisor_notes->media as $media)
        @php
            $path = public_path($media->url);
            $base64 = file_exists($path) ? getBase64Image($path) : '';
        @endphp
        @if($base64)
            <img src="{{ $base64 }}"
                 style="max-width: 200px; max-height: 130px; margin: 5px; border: 1px solid #ccc;">
        @endif
    @endforeach
</div>
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