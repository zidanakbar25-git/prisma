<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        Kalender Kegiatan Humas
    </title>

    <style>

        @page {
            margin: 30px 35px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #24332A;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .subtitle {
            font-size: 11px;
            color: #6B7280;
        }

        .period {
            margin-top: 8px;
            font-size: 11px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #234936;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 9px;
        }

        td {
            border: 1px solid #D9E0DA;
            padding: 8px;
            vertical-align: top;
            font-size: 9px;
        }

        tr:nth-child(even) td {
            background-color: #F7F8F6;
        }

        .date {
            width: 12%;
        }

        .time {
            width: 14%;
        }

        .activity {
            width: 25%;
        }

        .location {
            width: 20%;
        }

        .pic {
            width: 29%;
        }

        .empty {
            text-align: center;
            padding: 25px;
            color: #6B7280;
        }

        .footer {
            margin-top: 25px;
            text-align: right;
            font-size: 8px;
            color: #8A938D;
        }

    </style>

</head>


<body>


    <div class="header">

        <div class="title">
            KALENDER KEGIATAN BIRO HUMAS
        </div>

        <div class="subtitle">
            Sistem Informasi Manajemen Humas
        </div>

        <div class="period">
            Periode: {{ $periodLabel }}
        </div>

    </div>


    @if($activities->count() > 0)

        <table>

            <thead>

                <tr>

                    <th class="date">
                        Tanggal
                    </th>

                    <th class="time">
                        Waktu
                    </th>

                    <th class="activity">
                        Kegiatan
                    </th>

                    <th class="location">
                        Lokasi
                    </th>

                    <th class="pic">
                        PIC
                    </th>

                </tr>

            </thead>


            <tbody>

                @foreach($activities as $activity)

                    <tr>

                        <td class="date">

                            {{ $activity->activity_date->format('d/m/Y') }}

                        </td>


                        <td class="time">

                            {{ \Carbon\Carbon::parse(
                                $activity->start_time
                            )->format('H:i') }}

                            -

                            @if($activity->end_time)

                                {{ \Carbon\Carbon::parse(
                                    $activity->end_time
                                )->format('H:i') }}

                            @else

                                Selesai

                            @endif

                        </td>


                        <td class="activity">

                            {{ $activity->title }}

                        </td>


                        <td class="location">

                            {{ $activity->location ?? '-' }}

                        </td>


                        <td class="pic">

                            @forelse($activity->pics as $pic)

                                {{ $pic->name }}{{ !$loop->last ? ', ' : '' }}

                            @empty

                                -

                            @endforelse

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    @else

        <div class="empty">

            Tidak ada kegiatan pada periode yang dipilih.

        </div>

    @endif


    <div class="footer">

        Dicetak pada
        {{ now()->format('d/m/Y H:i') }}
        WIB

    </div>


</body>

</html>