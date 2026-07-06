<div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead><tr><th>Konten</th><th>Status</th><th>Pengusul</th></tr></thead>
        <tbody>
            @forelse($contents as $content)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $content['title'] }}</div>
                        <div class="small text-muted">{{ $content['type'] }} - {{ optional($content['updated_at'])->translatedFormat('d M Y H:i') }}</div>
                    </td>
                    <td><span class="badge text-bg-warning">{{ $content['status'] }}</span></td>
                    <td>{{ $content['creator'] }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center text-muted py-4">Tidak ada konten pada kategori ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
