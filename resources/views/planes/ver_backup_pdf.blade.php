<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Respaldo - {{ $backup->nombre_plan_estrategico }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }

        h2,
        h3 {
            color: #1e3a8a;
            margin-bottom: 8px;
        }

        h2 {
            font-size: 20px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 4px;
        }

        h3 {
            font-size: 16px;
            margin-top: 15px;
        }

        .section {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 15px;
            background: #f9fafb;
        }

        .subsection {
            border: 1px solid #e0e7ff;
            border-radius: 6px;
            background: #eef2ff;
            padding: 10px;
            margin-top: 8px;
        }

        p,
        li {
            line-height: 1.5;
        }

        .meta-title {
            background-color: #e0f2fe;
            padding: 8px;
            border-radius: 6px;
            font-weight: bold;
            color: #0369a1;
            margin-bottom: 6px;
        }

        ul {
            padding-left: 20px;
            margin-top: 5px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 5px 8px;
            text-align: left;
        }

        th {
            background-color: #e8eaf6;
        }

        hr {
            border: none;
            border-top: 1px solid #ccc;
            margin: 10px 0;
        }

        .small {
            font-size: 11px;
            color: #555;
        }

        .evidencia {
            margin-top: 6px;
            margin-left: 10px;
        }

        .evidencia img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 5px;
            border: 1px solid #ccc;
        }

        .evidencia a {
            display: block;
            color: #1d4ed8;
            text-decoration: underline;
            font-size: 12px;
        }
    </style>
</head>

<body>

    <h2>📑 Respaldo del Plan: {{ $backup->nombre_plan_estrategico }}</h2>

    <!-- Información General -->
    <div class="section">
        <h3>📋 Información General</h3>
        <table>
            <tr>
                <th>📂 Departamento</th>
                <td>{{ $backup->nombre_departamento }}</td>
            </tr>
            <tr>
                <th>👤 Responsable</th>
                <td>{{ $backup->nombre_responsable }}</td>
            </tr>
            <tr>
                <th>📅 Fecha Inicio</th>
                <td>{{ $backup->fecha_inicio }}</td>
            </tr>
            <tr>
                <th>📅 Fecha Fin</th>
                <td>{{ $backup->fecha_fin }}</td>
            </tr>
        </table>
    </div>

    <!-- Ejes Estratégicos y Objetivos -->
    <div class="section">
        <h3>🎯 Ejes Estratégicos del Plan</h3>
        <ul>
            @foreach (explode(',', $backup->ejes_estrategicos) as $eje)
                <li>{{ trim($eje) }}</li>
            @endforeach
        </ul>

        <h3>📌 Objetivos</h3>
        @if ($backup->objetivos)
            <ul>
                @foreach (json_decode($backup->objetivos) as $objetivo)
                    <li>{{ $objetivo }}</li>
                @endforeach
            </ul>
        @else
            <p class="small"><em>Sin objetivos</em></p>
        @endif
    </div>

    <!-- Metas -->
    @if ($backup->metas)
        <div class="section">
            <h3>🎯 Metas / Objetivos Estratégicos</h3>

            @foreach (json_decode($backup->metas) as $index => $meta)
                <div class="subsection">
                    <div class="meta-title">Meta/Objetivo Estratégico {{ $index + 1 }}: {{ $meta->nombre }}</div>

                    <p><strong>Encargado:</strong> {{ $meta->responsable ?? 'N/A' }}</p>
                    <p><strong>Ejes Estratégicos:</strong> {{ $meta->ejes_estrategicos }}</p>
                    <p><strong>Resultados Esperados:</strong> {{ $meta->resultados_esperados ?? 'N/A' }}</p>
                    <p><strong>Indicador:</strong> {{ $meta->indicador_resultados ?? 'N/A' }}</p>
                    <p><strong>Comentario:</strong> {{ $meta->comentario ?? 'N/A' }}</p>
                    <p><strong>Fecha Inicio:</strong> {{ $meta->fecha_inicio }} |
                        <strong>Fecha Fin:</strong> {{ $meta->fecha_fin }}
                    </p>

                    <!-- Actividades -->
                    @if (!empty($meta->actividades))
                        <h4>📋 Actividades / Líneas de acción</h4>
                        @foreach ($meta->actividades as $actIndex => $actividad)
                            <div
                                style="margin-left: 10px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 8px; margin-top: 6px;">
                                <p><strong>Actividad {{ $actIndex + 1 }}:</strong> {{ $actividad->nombre_actividad }}
                                </p>
                                <p><strong>Encargado:</strong> {{ $actividad->encargado ?? 'N/A' }}</p>
                                <p><strong>Objetivos:</strong> {{ $actividad->objetivos ?? 'N/A' }}</p>
                                <p><strong>Comentario:</strong> {{ $actividad->comentario ?? 'N/A' }}</p>
                                <p><strong>Unidad Encargada:</strong> {{ $actividad->unidad_encargada ?? 'N/A' }}</p>
                                <p><strong>Periodo:</strong> {{ $actividad->fecha_inicio }} -
                                    {{ $actividad->fecha_fin }}</p>

                                <!-- Evidencias -->
                                @php
                                    $evidencias = is_array($actividad->evidencia)
                                        ? $actividad->evidencia
                                        : json_decode($actividad->evidencia, true) ?? [];
                                @endphp

                                @if (!empty($evidencias))
                                    <div class="evidencia">
                                        <strong>📎 Evidencias:</strong><br>
                                        @foreach ($evidencias as $archivo)
                                            @php
                                                $extension = pathinfo($archivo, PATHINFO_EXTENSION);
                                            @endphp

                                            @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                                <img src="{{ public_path($archivo) }}" alt="Evidencia">
                                            @else
                                                <a href="{{ public_path($archivo) }}">{{ basename($archivo) }}</a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Seguimientos -->
                                @if (!empty($actividad->seguimientos))
                                    <h5 style="margin-top: 8px;">📊 Seguimientos</h5>
                                    @foreach ($actividad->seguimientos as $segIndex => $seg)
                                        <div
                                            style="margin-left: 10px; background: #fef9c3; border-left: 3px solid #facc15; padding: 5px; border-radius: 4px; margin-bottom: 4px;">
                                            <p><strong>Seguimiento {{ $segIndex + 1 }}:</strong></p>
                                            <p>Periodo: {{ $seg->periodo_consultar }}</p>
                                            <p>Observaciones: {{ $seg->observaciones }}</p>
                                            <p>Estado: {{ $seg->estado }}</p>
                                            <p>Documento: {{ $seg->documento ?? 'N/A' }}</p>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            @endforeach
        </div>
    @endif

</body>

</html>
