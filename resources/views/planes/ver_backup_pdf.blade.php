<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Respaldo - {{ $backup->nombre_plan_estrategico }}</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url("{{ public_path('fonts/DejaVuSans.ttf') }}") format('truetype');
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            color: #1f2937;
            margin: 25px;
            line-height: 1.4;
        }

        h2 {
            font-size: 22px;
            color: #1e3a8a;
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        h3 {
            font-size: 17px;
            color: #1e40af;
            margin-bottom: 6px;
        }

        h4 {
            font-size: 15px;
            color: #854d0e;
            margin: 8px 0 5px;
        }

        .section {
            background: #f9fafb;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 16px;
            page-break-inside: avoid;
        }

        .subsection {
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            border-radius: 6px;
            padding: 8px 10px;
            margin-top: 8px;
            page-break-inside: avoid;
        }

        .meta-title {
            background: #e0f2fe;
            padding: 6px 8px;
            border-radius: 5px;
            font-weight: bold;
            color: #0369a1;
            margin-bottom: 6px;
            page-break-after: avoid;
        }

        .seguimiento {
            background: #fef9c3;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 6px 8px;
            margin-top: 5px;
            page-break-inside: avoid;
        }

        .small {
            font-size: 11px;
            color: #6b7280;
        }

        .italic {
            font-style: italic;
        }

        * {
            orphans: 3;
            widows: 3;
        }

        div,
        table,
        tr,
        td,
        th {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

    <h2>📑 Respaldo del Plan: {{ $backup->nombre_plan_estrategico }}</h2>

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

    @php
        function safe_decode($data)
        {
            if (is_string($data)) {
                $decoded = json_decode($data, true);
                return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
            }
            return is_array($data) ? $data : [];
        }

        function listaCompactaPDF($valor)
        {
            $items = safe_decode($valor);
            if (empty($items)) {
                return '<p class="italic small">Sin información disponible</p>';
            }
            return '<div class="compact-list"><p>• ' .
                implode('</p><p>• ', array_map(fn($i) => e($i), $items)) .
                '</p></div>';
        }

        function evidenciasPDF($evidencias)
        {
            if (empty($evidencias)) {
                return '';
            }
            $links = '';
            foreach ($evidencias as $archivo) {
                $links .= '<p>📄 <a href="' . asset($archivo) . '" target="_blank">' . basename($archivo) . '</a></p>';
            }
            return $links;
        }
    @endphp

    <div class="section">
        <h3>🎯 Ejes Estratégicos</h3>
        {!! listaCompactaPDF($backup->ejes_estrategicos) !!}
    </div>

    <div class="section">
        <h3>📌 Objetivos</h3>
        {!! listaCompactaPDF($backup->objetivos) !!}
    </div>

    @if (!empty($backup->metas))
        <div class="section">
            <h3>🎯 Metas / Objetivos Estratégicos</h3>

            @foreach (safe_decode($backup->metas) as $meta)
                @php
                    $meta = safe_decode($meta);
                    $meta['ejes_estrategicos'] = safe_decode($meta['ejes_estrategicos']);
                    $meta['evidencias'] = safe_decode($meta['evidencias']);
                    $actividades = safe_decode($meta['actividades']);
                @endphp

                <div class="subsection">
                    <div class="meta-title">
                        Meta: {{ $meta['nombre'] ?? '' }}
                        @if (!empty($meta['responsable']))
                            (Encargado: {{ $meta['responsable'] }})
                        @endif
                    </div>

                    <p><strong>Ejes Estratégicos:</strong></p>
                    {!! listaCompactaPDF($meta['ejes_estrategicos']) !!}

                    <p><strong>Resultados Esperados:</strong> {{ $meta['resultados_esperados'] ?? 'N/A' }}</p>
                    <p><strong>Indicador:</strong> {{ $meta['indicador_resultados'] ?? 'N/A' }}</p>
                    <p><strong>Comentario:</strong> {{ $meta['comentario'] ?? 'N/A' }}</p>
                    <p><strong>Periodo:</strong> {{ $meta['fecha_inicio'] ?? '' }} - {{ $meta['fecha_fin'] ?? '' }}
                    </p>

                    @if (!empty($meta['evidencias']))
                        <div>
                            <strong>📎 Evidencias:</strong>
                            {!! evidenciasPDF($meta['evidencias']) !!}
                        </div>
                    @endif

                    {{-- Actividades --}}
                    @if (!empty($actividades))
                        <h4>📋 Actividades</h4>
                        @foreach ($actividades as $act)
                            @php
                                $act = safe_decode($act);

                                // NUEVO: obtener tipo de campo (objetivos o indicadores)
                                $tipoCampo = $act['tipo_campo'] ?? 'objetivos';
                                $lista = $act['contenido_campo'] ?? [];

                                if (is_string($lista)) {
                                    $lista = json_decode($lista, true) ?? [];
                                }

                                // Evidencias
                                $act['evidencias'] = safe_decode($act['evidencias']);
                                $seguimientos = safe_decode($act['seguimientos']);
                            @endphp

                            <div class="subsection">
                                <div class="meta-title">
                                    Actividad: {{ $act['nombre_actividad'] ?? '' }}
                                    @if (!empty($act['encargado']))
                                        (Encargado: {{ $act['encargado'] }})
                                    @endif
                                </div>

                                <p><strong>{{ ucfirst($tipoCampo) }}:</strong></p>
                                {!! listaCompactaPDF($lista) !!}

                                <p><strong>Comentario:</strong> {{ $act['comentario'] ?? 'N/A' }}</p>
                                <p><strong>Unidad Encargada:</strong> {{ $act['unidad_encargada'] ?? 'N/A' }}</p>
                                <p><strong>Periodo:</strong> {{ $act['fecha_inicio'] ?? '' }} - {{ $act['fecha_fin'] ?? '' }}</p>

                                @if (!empty($act['evidencias']))
                                    <div>
                                        <strong>📎 Evidencias:</strong>
                                        {!! evidenciasPDF($act['evidencias']) !!}
                                    </div>
                                @endif

                                {{-- Seguimientos --}}
                                @if (!empty($seguimientos))
                                    <h4>📅 Seguimientos</h4>
                                    @foreach ($seguimientos as $seg)
                                        @php $seg = safe_decode($seg); @endphp

                                        <div class="seguimiento">
                                            <p><strong>Periodo:</strong> {{ $seg['periodo_consultar'] ?? '' }}</p>
                                            <p><strong>Observaciones:</strong> {{ $seg['observaciones'] ?? '' }}</p>
                                            <p><strong>Estado:</strong> {{ $seg['estado'] ?? '' }}</p>
                                            <p><strong>Documento:</strong> {{ $seg['documento'] ?? 'N/A' }}</p>

                                            @if (!empty($seg['evidencias']))
                                                <div>
                                                    <strong>📎 Evidencias:</strong>
                                                    {!! evidenciasPDF($seg['evidencias']) !!}
                                                </div>
                                            @endif
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
