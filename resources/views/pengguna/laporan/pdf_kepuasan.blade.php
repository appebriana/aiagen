<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kepuasan Pelanggan (CSAT)</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin: 0; color: #b45309; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 8px; color: #777; }
        .bg-gray { background-color: #f9f9f9; }
        .rating { font-size: 14px; font-weight: bold; color: #b45309; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN KEPUASAN PELANGGAN (CSAT)</h2>
        <p>Dicetak oleh: {{ $user->name }} | Tanggal: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">Waktu & Tanggal</th>
                <th width="15%">Departemen</th>
                <th width="15%">Nomor</th>
                <th width="8%">Rating</th>
                <th width="23%">Pesan Terakhir</th>
                <th width="24%">Jawaban</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $index => $log)
            <tr class="{{ $index % 2 == 0 ? '' : 'bg-gray' }}">
                <td>{{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d/m/y H:i') }}</td>
                <td>{{ \Illuminate\Support\Facades\DB::table('departments')->where('id', $log->department_id)->value('name') ?? '-' }}</td>
                <td>{{ $log->customer_phone }}</td>
                <td class="rating">{{ $log->rating }}</td>
                <td>{{ $log->question }}</td>
                <td>{{ $log->answer }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Halaman ini dicetak otomatis oleh sistem AIAGEN
    </div>
</body>
</html>
