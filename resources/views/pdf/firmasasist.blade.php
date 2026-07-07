<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Hoja de Firmas – {{ $curso->nombre_curso }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            padding: 28px 32px;
        }

        /* ── CABECERA ── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #1a7a4a;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .header-left h1 {
            font-size: 17px;
            font-weight: bold;
            color: #1a7a4a;
            margin-bottom: 2px;
        }

        .header-left p {
            font-size: 11px;
            color: #555;
        }

        .header-right {
            text-align: right;
            font-size: 10px;
            color: #666;
        }

        /* ── META DEL CURSO ── */
        .meta {
            display: flex;
            gap: 24px;
            background: #f3faf6;
            border: 1px solid #c6e8d4;
            border-radius: 5px;
            padding: 8px 14px;
            margin-bottom: 18px;
            font-size: 10.5px;
        }

        .meta-item strong {
            display: block;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #1a7a4a;
            margin-bottom: 2px;
        }

        /* ── TABLA ── */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background-color: #1a7a4a;
            color: #fff;
        }

        thead th {
            padding: 7px 6px;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.04em;
            border: 1px solid #158040;
        }

        thead th.col-alumno {
            text-align: left;
            padding-left: 10px;
            width: 28%;
        }

        tbody tr:nth-child(even) {
            background-color: #f7fdf9;
        }

        tbody tr:hover {
            background-color: #edf7f2;
        }

        tbody td {
            border: 1px solid #d1d5db;
            padding: 0;
            height: 36px;
            vertical-align: middle;
        }

        tbody td.col-alumno {
            padding: 6px 10px;
            font-size: 11px;
            font-weight: 500;
        }

        tbody td.col-firma {
            /* celda vacía para firma */
            min-width: 80px;
        }

        /* ── FILA PROFESOR ── */
        .profesor-row td {
            background-color: #e8f5ee !important;
            font-weight: bold;
            font-size: 11px;
        }

        /* ── PIE ── */
        .footer {
            margin-top: 22px;
            font-size: 9.5px;
            color: #888;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    {{-- CABECERA --}}
    <div class="header">
        <div class="header-left">
            <h1>Hoja de asistencia semanal</h1>
            <p>Control de firmas · {{ $curso->nombre_curso }}</p>
        </div>
        <div class="header-right">
            Semana: _____ / _____ / _____&nbsp;&nbsp;al&nbsp;&nbsp;_____ / _____ / _____<br><br>
            Generado el {{ date('d/m/Y H:i') }}
        </div>
    </div>

    {{-- META CURSO --}}
    <div class="meta">
        <div class="meta-item">
            <strong>Curso</strong>
            {{ $curso->nombre_curso }}
        </div>
        <div class="meta-item">
            <strong>Total alumnos</strong>
            {{ $alumnos->count() }}
        </div>
        <div class="meta-item">
            <strong>Semana</strong>
            Lunes – Viernes
        </div>
    </div>

    {{-- TABLA DE FIRMAS --}}
    <table>
        <thead>
            <tr>
                <th class="col-alumno">Alumno / Docente</th>
                <th>Lunes</th>
                <th>Martes</th>
                <th>Miércoles</th>
                <th>Jueves</th>
                <th>Viernes</th>
            </tr>
        </thead>

        <tbody>
            {{-- Filas de alumnos --}}
            @foreach ($alumnos as $i => $alumno)
                <tr>
                    <td class="col-alumno">
                        {{ $i + 1 }}. {{ $alumno->nombre }} {{ $alumno->apellidos }}
                    </td>
                    <td class="col-firma"></td>
                    <td class="col-firma"></td>
                    <td class="col-firma"></td>
                    <td class="col-firma"></td>
                    <td class="col-firma"></td>
                </tr>
            @endforeach

            {{-- Fila del profesor --}}
            <tr class="profesor-row">
                <td class="col-alumno">Profesor/a</td>
                <td class="col-firma"></td>
                <td class="col-firma"></td>
                <td class="col-firma"></td>
                <td class="col-firma"></td>
                <td class="col-firma"></td>
            </tr>
        </tbody>
    </table>

    {{-- PIE --}}
    <div class="footer">
        Documento generado automáticamente · Uso interno
    </div>

</body>
</html>