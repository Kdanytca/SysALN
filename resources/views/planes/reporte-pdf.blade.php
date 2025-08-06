<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte del Plan Estratégico</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 16px;
            /* letra más grande */
            color: #000;
            margin: 20px;
        }

        h1,
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .tabla {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .tabla th,
        .tabla td {
            border: 1px solid #444;
            padding: 8px 12px;
            text-align: left;
        }

        .tabla th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .indicador {
            font-weight: bold;
            text-transform: uppercase;
        }

        .verde {
            color: green;
        }

        .amarillo {
            color: orange;
        }

        .rojo {
            color: red;
        }
    </style>
</head>

<body>
    <h1>Reporte del Plan Estratégico</h1>

    <table class="tabla">
        <thead>
            <tr>
                <th colspan="2">Información General del Plan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Nombre del Plan</td>
                <td>{{ $plan->nombre_plan_estrategico ?? 'No asignado' }}</td>
            </tr>
            <tr>
                <td>Responsable del Plan</td>
                <td>{{ optional($plan->responsable)->nombre_usuario ?? 'No asignado' }}</td>
            </tr>
            <tr>
                <td>Departamento</td>
                <td>{{ $plan->departamento->departamento ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Institución</td>
                <td>{{ $plan->departamento->institucion->nombre_institucion ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Encargado Institución</td>
                <td>{{ optional($plan->departamento->institucion->encargadoInstitucion)->nombre_usuario ?? 'No asignado' }}
                </td>
            </tr>
            <tr>
                <td>Avance Global</td>
                <td>{{ $porcentajeTotal }}%</td>
            </tr>
        </tbody>
    </table>

    @foreach ($plan->metas as $meta)
        <table class="tabla">
            <thead>
                <tr>
                    <th colspan="2">Meta: {{ $meta->nombre_meta }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Responsable de Meta</td>
                    <td>{{ optional($meta->responsable)->nombre_usuario ?? 'No asignado' }}</td>
                </tr>
                <tr>
                    <td>Comentario</td>
                    <td>{{ $meta->comentario ?? 'No hay' }}</td>
                </tr>
            </tbody>
        </table>

        @if ($meta->actividades->count())
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Actividad</th>
                        <th>Responsable</th>
                        <th>Seguimientos (total / finalizados)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($meta->actividades as $act)
                        <tr>
                            <td>{{ $act->nombre_actividad }}</td>
                            <td>{{ optional($act->usuario)->nombre_usuario ?? 'No asignado' }}</td>
                            <td>{{ $act->seguimientos->count() }} /
                                {{ $act->seguimientos->where('estado', 'finalizado')->count() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p><strong>No hay actividades registradas para esta meta.</strong></p>
        @endif
    @endforeach

    <table class="tabla">
        <thead>
            <tr>
                <th>Meta</th>
                <th>Descripción</th>
                <th>Avance (%)</th>
                <th>Comentario</th>
                <th>Indicador</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $fila)
                <tr>
                    <td>{{ $fila['meta'] }}</td>
                    <td>{{ $fila['descripcion'] }}</td>
                    <td>{{ $fila['porcentaje'] }}%</td>
                    <td>{{ $fila['comentario'] ?: 'Ninguno' }}</td>
                    <td class="indicador {{ $fila['indicador'] }}">{{ strtoupper($fila['indicador']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
