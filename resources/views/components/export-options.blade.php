@php
    if (isset($preview_only) === false) {
        $preview_only = false;
    }

    $param = [
        'report_model' => $report_model,
    ];

@endphp
<div class="flex">
    <a class="btn btn-primary w-1/2 sm:w-auto mr-1" target="_blank_{{ $report_model }}"
        href="{{ route('ds.report.preview.pdf', $param) }}">Export PDF<i data-feather="printer" class="w-4 h-4"></i></a>

</div>
