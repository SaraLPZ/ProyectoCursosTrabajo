
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Alumnos</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
        }

        .sub {
            text-align: center;
            margin-bottom: 20px;
            font-size: 12px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f3f4f6;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }
    </style>
</head>

<body>

<div class="header">
    <h1>Listado de Alumnos</h1>
</div>

<div class="sub">
    Generado el {{ date('d/m/Y H:i') }}
</div>

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Apellidos</th>
            <th>DNI</th>
            <th>Email</th>
            <th>Teléfono</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($alumnos as $alumno)
            <tr>
                <td>{{ $alumno->nombre }}</td>
                <td>{{ $alumno->apellidos }}</td>
                <td>{{ $alumno->dni }}</td>
                <td>{{ $alumno->email }}</td>
                <td>{{ $alumno->telefono }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>