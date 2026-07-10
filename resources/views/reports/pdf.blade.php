@extends('layouts.app')

@section('content')

<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">Dashboard & Reporting</h1>
    <p class="text-muted mb-0">
        Export laporan operasional dan analitik sistem.
    </p>
</div>

<div class="row g-4">

    @foreach([
        'travel' => 'Travel',
        'booking' => 'Booking',
        'atk' => 'ATK',
        'po' => 'Purchase Order',
        'csat' => 'CSAT'
    ] as $type => $label)

    <div class="col-lg-4 col-md-6">

        <div class="report-card">

            <div class="report-title">
                {{ $label }}
            </div>

            <div class="report-description">
                Download laporan dalam format PDF atau CSV.
            </div>

            <div class="d-flex gap-2 mt-4">

                <a class="btn btn-outline-danger flex-fill"
                   href="{{ route('reports.export',[$type,'format'=>'pdf']) }}">
                    PDF
                </a>

                <a class="btn btn-outline-success flex-fill"
                   href="{{ route('reports.export',[$type,'format'=>'csv']) }}">
                    CSV
                </a>

            </div>

        </div>

    </div>

    @endforeach

</div>

@endsection
