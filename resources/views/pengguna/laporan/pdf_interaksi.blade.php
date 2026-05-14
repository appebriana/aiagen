<!DOCTYPE html>
<html>
<head>
    <title>Laporan Interaksi AI</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin: 0; color: #1e40af; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 8px; color: #777; }
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 8px; }
        .bg-gray { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN INTERAKSI AI</h2>
        <p>Dicetak oleh: {{ $user->name }} | Tanggal: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="12%">Tanggal</th>
                <th width="12%">Departemen</th>
                <th width="12%">Nomor WA</th>
                <th width="30%">Pertanyaan</th>
                <th width="30%">Jawaban</th>
                <th width="6%">Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $index => $log)
            <tr class="{{ $index % 2 == 0 ? '' : 'bg-gray' }}">
                <td>{{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d/m/y H:i') }}</td>
                <td>{{ \Illuminate\Support\Facades\DB::table('departments')->where('id', $log->department_id)->value('name') ?? '-' }}</td>
                <td>{{ $log->customer_phone }}</td>
                <td>{{ $log->question }}</td>
                <td>{{ $log->answer }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $log->rating ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Halaman ini dicetak otomatis oleh sistem AIAGEN
    </div>
</body>
</html>
