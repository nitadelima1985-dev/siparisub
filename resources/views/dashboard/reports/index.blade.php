@extends('layouts.dashboard', ['title' => 'Statistik & Laporan'])

@section('content')
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">Statistik & Laporan</h1>
            <p class="text-muted mb-0">Pantau ringkasan data, workflow, dan aktivitas aktor SIPARISUB.</p>
        </div>
        <span class="badge text-bg-success rounded-pill px-3 py-2">
            <i class="fa-solid fa-chart-pie me-1"></i>Dashboard Analitik
        </span>
    </div>

    <div class="row g-3 mb-4">
        @foreach($summaryCards as $card)
            <div class="col-md-6 col-xl-3">
                <div class="border rounded-3 p-3 h-100 bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">{{ $card['label'] }}</div>
                            <div class="fs-3 fw-bold">{{ number_format($card['value'], 0, ',', '.') }}</div>
                        </div>
                        <div class="rounded-circle text-bg-{{ $card['color'] }} d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fa-solid {{ $card['icon'] }}"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="border rounded-3 p-4 h-100">
                <h2 class="h6 fw-bold mb-3">Destinasi per Kategori</h2>
                <canvas id="destinationCategoryChart" height="180"></canvas>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="border rounded-3 p-4 h-100">
                <h2 class="h6 fw-bold mb-3">Destinasi per Kecamatan</h2>
                <canvas id="destinationDistrictChart" height="180"></canvas>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="border rounded-3 p-4 h-100">
                <h2 class="h6 fw-bold mb-3">Event per Bulan</h2>
                <canvas id="eventMonthChart" height="180"></canvas>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="border rounded-3 p-4 h-100">
                <h2 class="h6 fw-bold mb-3">Artikel per Kategori</h2>
                <canvas id="articleCategoryChart" height="180"></canvas>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="border rounded-3 p-4 h-100">
                <h2 class="h6 fw-bold mb-3">Konten berdasarkan Workflow</h2>
                <canvas id="workflowChart" height="180"></canvas>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="border rounded-3 p-4 h-100">
                <h2 class="h6 fw-bold mb-3">User per Role dan Organisasi per Tipe</h2>
                <div class="row g-3">
                    <div class="col-md-6"><canvas id="userRoleChart" height="180"></canvas></div>
                    <div class="col-md-6"><canvas id="organizationTypeChart" height="180"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-6">
            <div class="border rounded-3 p-4 h-100">
                <h2 class="h6 fw-bold mb-3">Destinasi Belum Diperbarui &gt; 90 Hari</h2>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Destinasi</th><th>Kategori</th><th>Update Terakhir</th></tr></thead>
                        <tbody>
                            @forelse($staleDestinations as $destination)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $destination->name }}</div>
                                        <div class="small text-muted">{{ $destination->district?->name ?: '-' }}</div>
                                    </td>
                                    <td>{{ $destination->category?->name ?: '-' }}</td>
                                    <td>{{ optional($destination->last_content_update_at ?? $destination->updated_at)->translatedFormat('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">Tidak ada destinasi yang melewati batas 90 hari.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="border rounded-3 p-4 h-100">
                <h2 class="h6 fw-bold mb-3">Konten Pending Review</h2>
                @include('dashboard.reports.partials.content-table', ['contents' => $pendingContents])
            </div>
        </div>

        <div class="col-xl-6">
            <div class="border rounded-3 p-4 h-100">
                <h2 class="h6 fw-bold mb-3">Konten Revision Needed</h2>
                @include('dashboard.reports.partials.content-table', ['contents' => $revisionNeededContents])
            </div>
        </div>

        <div class="col-xl-6">
            <div class="border rounded-3 p-4 h-100">
                <h2 class="h6 fw-bold mb-3">Aktor/User Paling Aktif</h2>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>User</th><th>Role</th><th class="text-end">Konten</th></tr></thead>
                        <tbody>
                            @forelse($activeUsers as $user)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                        <div class="small text-muted">{{ $user->email }}</div>
                                    </td>
                                    <td>{{ $user->role_name ?: '-' }}</td>
                                    <td class="text-end fw-bold">{{ number_format($user->total_content, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">Belum ada aktivitas pembuatan konten.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const chartData = @json($chartData);
        const palette = ['#0f766e', '#2563eb', '#f59e0b', '#dc2626', '#7c3aed', '#0891b2', '#65a30d', '#be123c'];

        createBarChart('destinationCategoryChart', chartData.destinationByCategory);
        createBarChart('destinationDistrictChart', chartData.destinationByDistrict);
        createLineChart('eventMonthChart', chartData.eventByMonth);
        createBarChart('articleCategoryChart', chartData.articleByCategory);
        createBarChart('workflowChart', chartData.workflowCounts);
        createDoughnutChart('userRoleChart', chartData.userByRole);
        createDoughnutChart('organizationTypeChart', chartData.organizationByType);

        function createBarChart(id, data) {
            new Chart(document.getElementById(id), {
                type: 'bar',
                data: chartPayload(data),
                options: baseOptions(),
            });
        }

        function createLineChart(id, data) {
            new Chart(document.getElementById(id), {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Total',
                        data: data.values,
                        borderColor: '#0f766e',
                        backgroundColor: 'rgba(15, 118, 110, .12)',
                        fill: true,
                        tension: .35,
                    }],
                },
                options: baseOptions(),
            });
        }

        function createDoughnutChart(id, data) {
            new Chart(document.getElementById(id), {
                type: 'doughnut',
                data: chartPayload(data),
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } },
                },
            });
        }

        function chartPayload(data) {
            return {
                labels: data.labels,
                datasets: [{
                    label: 'Total',
                    data: data.values,
                    backgroundColor: data.values.map((_, index) => palette[index % palette.length]),
                }],
            };
        }

        function baseOptions() {
            return {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            };
        }
    </script>
@endsection
