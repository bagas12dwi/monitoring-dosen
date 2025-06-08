<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        ul li {
            font-size: 10pt;
            margin-top: 10pt;
        }

        p {
            font-size: 10pt;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead {
            background-color: #f2f2f2;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px 10px;
            text-align: left;
        }

        th {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h2>{{ $title }}</h2>
    <p>Semester : {{ $semester->tahun_ajaran }} {{ $semester->semester }}</p>
    <p>Jumlah Responden : {{ $jumlah }}</p>
    <p style="margin-top: 10pt">List Matakuliah : </p>
    <ul>
        @foreach ($data as $item)
            <li>{{ $item['dosen'] }} | Skor Penilaian : <span style="font-weight: bold">{{ $item['score'] }}</span></li>
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Kriteria</th>
                        <th>Sangat Setuju</th>
                        <th>Setuju</th>
                        <th>Netral</th>
                        <th>Tidak Setuju</th>
                        <th>Sangat Tidak Setuju</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item['detail'] as $detail)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $detail['kriteria'] }}</td>
                            <td>
                                <nobr>{{ $detail['sangat_setuju'] }}</nobr>
                            </td>
                            <td>
                                <nobr>{{ $detail['setuju'] }}</nobr>
                            </td>
                            <td>
                                <nobr>{{ $detail['netral'] }}</nobr>
                            </td>
                            <td>
                                <nobr>{{ $detail['tidak_setuju'] }}</nobr>
                            </td>
                            <td>
                                <nobr>{{ $detail['sangat_tidak_setuju'] }}</nobr>
                            </td>
                            <td>
                                <nobr>{{ $detail['total'] }}</nobr>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach

    </ul>
    <li>Catatan : </li>
    @foreach ($catatan as $item)
        <p>{{ $item->komentar }}</p>
    @endforeach
</body>

</html>
