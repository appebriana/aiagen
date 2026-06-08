<table border="1">
    <thead>
        <tr>
            <th style="background-color: #4f46e5; color: #ffffff; font-weight: bold;">Tanggal</th>
            <th style="background-color: #4f46e5; color: #ffffff; font-weight: bold;">Departemen</th>
            <th style="background-color: #4f46e5; color: #ffffff; font-weight: bold;">Sesi ID</th>
            <th style="background-color: #4f46e5; color: #ffffff; font-weight: bold;">Pertanyaan</th>
            <th style="background-color: #4f46e5; color: #ffffff; font-weight: bold;">Jawaban</th>
            <th style="background-color: #4f46e5; color: #ffffff; font-weight: bold;">Rating</th>
        </tr>
    </thead>
    <tbody>
        @foreach($logs as $log)
        <tr>
            <td style="vertical-align: top;">{{ $log->created_at }}</td>
            <td style="vertical-align: top;">{{ \Illuminate\Support\Facades\DB::table('departments')->where('id', $log->department_id)->value('name') ?? '-' }}</td>
            <td style="vertical-align: top;">{{ $log->customer_phone }}</td>
            <td style="vertical-align: top;">{{ $log->question }}</td>
            <td style="vertical-align: top; white-space: pre-wrap;">{{ $log->answer }}</td>
            <td style="vertical-align: top; text-align: center;">{{ $log->rating ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
