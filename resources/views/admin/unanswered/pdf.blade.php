<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pertanyaan Tidak Terjawab</title>
    <style>
        @page { size: landscape; margin: 1cm; }
        body { font-family: sans-serif; font-size: 10px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; table-layout: fixed; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; word-wrap: break-word; vertical-align: top; }
        th { background-color: #f8fafc; font-weight: bold; color: #475569; }
        .header { text-align: center; margin-bottom: 20px; }
        .status { font-weight: bold; font-size: 9px; text-transform: uppercase; }
        .pending { color: #d97706; }
        .answered { color: #059669; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin-bottom: 5px; color: #1e293b;">LAPORAN PERTANYAAN & JAWABAN AIAGEN</h2>
        <p style="margin-top: 0; color: #64748b;">Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 18%;">Pengirim</th>
                <th style="width: 29%;">Pertanyaan</th>
                <th style="width: 29%;">Jawaban Manual</th>
                <th style="width: 8%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($questions as $index => $q)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $q->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    @if($q->customer)
                        <strong>{{ $q->customer->nickname ?: $q->customer->name }}</strong><br>
                        <small style="color: #64748b;">{{ $q->sender }}</small>
                    @else
                        <strong>{{ $q->sender ?? 'Anonim' }}</strong>
                    @endif
                    <br>
                    <small style="color: #94a3b8;">{{ $q->department->name ?? '-' }}</small>
                </td>
                <td>{{ $q->question }}</td>
                <td>{{ $q->answer ?? '-' }}</td>
                <td class="status {{ $q->is_answered ? 'answered' : 'pending' }}">
                    {{ $q->is_answered ? 'Terjawab' : 'Pending' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
