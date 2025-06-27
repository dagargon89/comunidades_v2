<?php
require_once 'includes/config.php';
if (!isAuthenticated()) redirect('/auth/login.php');

try {
    $pdo = getDBConnection();
    
    // Obtener actividades con información completa
    $sql = "SELECT 
                a.id,
                a.nombre,
                a.descripcion,
                a.fecha_inicio,
                a.fecha_fin,
                a.estatus,
                a.lugar,
                a.meta,
                a.observaciones,
                a.created_at,
                a.updated_at,
                p.nombre as producto_nombre,
                c.nombre as componente_nombre,
                e.nombre as eje_nombre,
                ta.nombre as tipo_actividad,
                tp.nombre as tipo_poblacion,
                ea.nombre as estado_actividad,
                pol.nombre as poligono_nombre,
                u.nombre as responsable_nombre,
                u.apellido_paterno as responsable_apellido,
                org.nombre as organizacion_nombre
            FROM actividades a
            LEFT JOIN productos p ON a.producto_id = p.id
            LEFT JOIN componentes c ON p.componente_id = c.id
            LEFT JOIN ejes e ON c.eje_id = e.id
            LEFT JOIN tipos_actividad ta ON a.tipo_actividad_id = ta.id
            LEFT JOIN tipos_poblacion tp ON a.tipo_poblacion_id = tp.id
            LEFT JOIN estados_actividad ea ON a.estado_actividad_id = ea.id
            LEFT JOIN poligonos pol ON a.poligono_id = pol.id
            LEFT JOIN usuarios u ON a.responsable_id = u.id
            LEFT JOIN organizaciones org ON u.organizacion_id = org.id
            ORDER BY a.fecha_inicio ASC, e.nombre, c.nombre, p.nombre";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $actividades = $stmt->fetchAll();
    
    // Obtener estadísticas
    $sql_stats = "SELECT 
                    COUNT(*) as total_actividades,
                    COUNT(CASE WHEN estatus = 'Completada' THEN 1 END) as completadas,
                    COUNT(CASE WHEN estatus = 'En Progreso' THEN 1 END) as en_progreso,
                    COUNT(CASE WHEN estatus = 'Programada' THEN 1 END) as programadas,
                    COUNT(CASE WHEN estatus = 'Cancelada' THEN 1 END) as canceladas
                  FROM actividades";
    $stmt_stats = $pdo->prepare($sql_stats);
    $stmt_stats->execute();
    $stats = $stmt_stats->fetch();
    
    $user = getCurrentUser();
    
} catch (PDOException $e) {
    setFlashMessage('error', 'Error al cargar las actividades.');
    redirect('/index.php');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagrama de Gantt - Comunidades</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- DHTMLX Gantt -->
    <link rel="stylesheet" href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css">
    <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>

    <!-- Configuración de Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        xanthous: { DEFAULT: '#FFBA49' },
                        seagreen: { DEFAULT: '#20A39E' },
                        bittersweet: { DEFAULT: '#EF5B5B' },
                        darkpurple: { DEFAULT: '#23001E' },
                        cadet: { DEFAULT: '#A4A9AD' },
                        primary: { DEFAULT: '#23001E' },
                        secondary: { DEFAULT: '#20A39E' },
                        accent: { DEFAULT: '#FFBA49' },
                        error: { DEFAULT: '#EF5B5B' },
                        base: { DEFAULT: '#A4A9AD' }
                    }
                }
            }
        }
    </script>

    <style>
        /* Estilos personalizados para el Gantt */
        .gantt_container {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .gantt_task_line {
            border-radius: 3px;
        }
        
        .gantt_task_line.gantt_project {
            background-color: #23001E;
            border-color: #23001E;
        }
        
        .gantt_task_line.gantt_task {
            background-color: #20A39E;
            border-color: #20A39E;
        }
        
        .gantt_task_line.gantt_milestone {
            background-color: #FFBA49;
            border-color: #FFBA49;
        }
        
        .gantt_task_line.gantt_completed {
            background-color: #28a745;
            border-color: #28a745;
        }
        
        .gantt_task_line.gantt_delayed {
            background-color: #EF5B5B;
            border-color: #EF5B5B;
        }
        
        .gantt_task_line.gantt_overdue {
            background-color: #dc3545;
            border-color: #dc3545;
            opacity: 0.8;
        }
        
        .gantt_task_line.gantt_overdue::after {
            content: "⚠";
            position: absolute;
            right: -5px;
            top: -5px;
            background: #ffc107;
            color: #000;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
        
        .gantt_grid_head_cell {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            font-weight: 600;
            color: #23001E;
        }
        
        .gantt_grid_data {
            border-color: #dee2e6;
        }
        
        .gantt_row {
            border-color: #dee2e6;
        }
        
        .gantt_cell {
            border-color: #dee2e6;
        }
        
        .gantt_task_progress {
            background-color: #FFBA49;
        }
        
        .gantt_task_progress_wrapper {
            background-color: rgba(255, 186, 73, 0.3);
        }
        
        .gantt_scale_cell {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            font-weight: 600;
            color: #23001E;
        }
        
        .gantt_scale_line {
            border-color: #dee2e6;
        }
        
        /* Estilos para edición inline */
        .gantt_cell_editor {
            border: 2px solid #20A39E;
            border-radius: 4px;
            padding: 2px 4px;
            background: white;
        }
        
        .gantt_cell_editor:focus {
            outline: none;
            border-color: #23001E;
            box-shadow: 0 0 0 2px rgba(35, 0, 30, 0.2);
        }
        
        /* Estilos para tooltips mejorados */
        .gantt_tooltip {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Estilos para filtros */
        .filter-badge {
            transition: all 0.3s ease;
        }
        
        .filter-badge:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        /* Estilos para controles de zoom */
        .zoom-controls button {
            transition: all 0.2s ease;
        }
        
        .zoom-controls button:hover {
            transform: scale(1.05);
        }
        
        /* Estilos para notificaciones */
        .notification {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        /* Estilos para el modal mejorado */
        .modal-content {
            animation: modalSlideIn 0.3s ease-out;
        }
        
        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        /* Estilos para estadísticas con hover */
        .stats-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        /* Estilos para botones de acción */
        .action-button {
            transition: all 0.2s ease;
        }
        
        .action-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        /* Estilos para el grid del Gantt */
        .gantt_grid_scale .gantt_grid_head_cell,
        .gantt_task .gantt_task_scale .gantt_scale_cell {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        /* Estilos para las filas alternadas */
        .gantt_row:nth-child(even) {
            background-color: rgba(248, 249, 250, 0.5);
        }
        
        .gantt_row:hover {
            background-color: rgba(32, 163, 158, 0.1);
        }
        
        /* Estilos para el scrollbar personalizado */
        .gantt_container ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        .gantt_container ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .gantt_container ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        .gantt_container ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Estilos para el estado de carga */
        .gantt_loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        /* Estilos para el indicador de progreso */
        .progress-indicator {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #20A39E 0%, #FFBA49 100%);
            border-radius: 3px;
            transition: width 0.3s ease;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .gantt_container {
                font-size: 12px;
            }
            
            .gantt_grid_head_cell,
            .gantt_scale_cell {
                font-size: 11px;
            }
            
            .gantt_config_columns {
                min-width: 80px;
            }
            
            .zoom-controls {
                flex-direction: column;
                gap: 4px;
            }
            
            .zoom-controls button {
                padding: 6px 8px;
                font-size: 12px;
            }
        }
        
        @media (max-width: 480px) {
            .gantt_container {
                font-size: 10px;
            }
            
            .gantt_grid_head_cell,
            .gantt_scale_cell {
                font-size: 10px;
                padding: 4px 2px;
            }
            
            .gantt_config_columns {
                min-width: 60px;
            }
        }
    </style>
</head>

<body class="bg-gray-50">
    <?php require_once 'includes/header.php'; ?>

    <div class="container mx-auto px-4 py-6">
        <!-- Header del Gantt -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-primary mb-2">
                        <i class="fas fa-chart-gantt text-accent mr-2"></i>
                        Diagrama de Gantt
                    </h1>
                    <p class="text-cadet">Visualización temporal de actividades y proyectos</p>
                </div>
                
                <div class="flex flex-wrap gap-2">
                    <button onclick="exportarPDF()" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors action-button">
                        <i class="fas fa-file-pdf mr-2"></i>Exportar PDF
                    </button>
                    <button onclick="exportarExcel()" class="bg-secondary text-white px-4 py-2 rounded-lg hover:bg-secondary/90 transition-colors action-button">
                        <i class="fas fa-file-excel mr-2"></i>Exportar Excel
                    </button>
                    <button onclick="exportarDatosFiltrados()" class="bg-accent text-darkpurple px-4 py-2 rounded-lg hover:bg-accent/90 transition-colors action-button">
                        <i class="fas fa-file-csv mr-2"></i>Exportar CSV
                    </button>
                    <button onclick="imprimirGantt()" class="bg-cadet text-white px-4 py-2 rounded-lg hover:bg-cadet/90 transition-colors action-button">
                        <i class="fas fa-print mr-2"></i>Imprimir
                    </button>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-blue-500 stats-card" onclick="filtrarPorEstado('')">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-cadet">Total Actividades</p>
                        <p class="text-2xl font-bold text-primary"><?php echo $stats['total_actividades']; ?></p>
                    </div>
                    <i class="fas fa-tasks text-blue-500 text-2xl"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-green-500 stats-card" onclick="filtrarPorEstado('Completada')">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-cadet">Completadas</p>
                        <p class="text-2xl font-bold text-green-600"><?php echo $stats['completadas']; ?></p>
                    </div>
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-yellow-500 stats-card" onclick="filtrarPorEstado('En Progreso')">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-cadet">En Progreso</p>
                        <p class="text-2xl font-bold text-yellow-600"><?php echo $stats['en_progreso']; ?></p>
                    </div>
                    <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-blue-400 stats-card" onclick="filtrarPorEstado('Programada')">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-cadet">Programadas</p>
                        <p class="text-2xl font-bold text-blue-600"><?php echo $stats['programadas']; ?></p>
                    </div>
                    <i class="fas fa-calendar text-blue-400 text-2xl"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-red-500 stats-card" onclick="filtrarPorEstado('Cancelada')">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-cadet">Canceladas</p>
                        <p class="text-2xl font-bold text-red-600"><?php echo $stats['canceladas']; ?></p>
                    </div>
                    <i class="fas fa-times-circle text-red-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-primary mb-4">
                <i class="fas fa-filter text-accent mr-2"></i>Filtros
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-darkpurple mb-2">Eje Estratégico</label>
                    <select id="filtro-eje" class="w-full px-3 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Todos los ejes</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-darkpurple mb-2">Estado</label>
                    <select id="filtro-estado" class="w-full px-3 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Todos los estados</option>
                        <option value="Programada">Programada</option>
                        <option value="En Progreso">En Progreso</option>
                        <option value="Completada">Completada</option>
                        <option value="Cancelada">Cancelada</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-darkpurple mb-2">Responsable</label>
                    <select id="filtro-responsable" class="w-full px-3 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Todos los responsables</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-darkpurple mb-2">Rango de Fechas</label>
                    <div class="flex gap-2">
                        <input type="date" id="fecha-inicio" class="flex-1 px-3 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        <input type="date" id="fecha-fin" class="flex-1 px-3 py-2 border border-cadet/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                </div>
            </div>
            
            <div class="flex gap-2 mt-4">
                <button onclick="aplicarFiltros()" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                    <i class="fas fa-search mr-2"></i>Aplicar Filtros
                </button>
                <button onclick="limpiarFiltros()" class="bg-cadet text-white px-4 py-2 rounded-lg hover:bg-cadet/90 transition-colors">
                    <i class="fas fa-times mr-2"></i>Limpiar
                </button>
            </div>
        </div>

        <!-- Contenedor del Gantt -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div id="gantt_container" style="width: 100%; height: 600px;"></div>
        </div>
    </div>

    <!-- Modal para detalles de actividad -->
    <div id="modal-actividad" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-primary">Detalles de la Actividad</h3>
                        <button onclick="cerrarModal()" class="text-cadet hover:text-primary">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div id="modal-content"></div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once 'includes/footer.php'; ?>

    <script>
        // Datos de las actividades desde PHP
        const actividadesData = <?php echo json_encode($actividades); ?>;
        
        // Configuración del Gantt
        gantt.config.date_format = "%Y-%m-%d %H:%i";
        gantt.config.scale_unit = "month";
        gantt.config.date_scale = "%F, %Y";
        gantt.config.subscales = [
            {unit: "week", step: 1, date: "%j"}
        ];
        
        // Configuración avanzada
        gantt.config.drag_resize = true;
        gantt.config.drag_move = true;
        gantt.config.drag_progress = true;
        gantt.config.drag_links = true;
        gantt.config.drag_plan = true;
        
        // Configuración de edición
        gantt.config.inline_editing = true;
        gantt.config.auto_scheduling = false;
        gantt.config.auto_scheduling_strict = false;
        gantt.config.work_time = true;
        gantt.config.correct_work_time = true;
        
        // Configuración de columnas
        gantt.config.columns = [
            {name: "text", label: "Actividad", width: 200, tree: true, editor: {type: "text", map_to: "text"}},
            {name: "responsable", label: "Responsable", width: 120, editor: {type: "select", map_to: "responsable", options: []}},
            {name: "estado", label: "Estado", width: 100, template: function(obj) {
                const estados = {
                    'Programada': '<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Programada</span>',
                    'En Progreso': '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">En Progreso</span>',
                    'Completada': '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Completada</span>',
                    'Cancelada': '<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Cancelada</span>'
                };
                return estados[obj.estado] || obj.estado;
            }, editor: {type: "select", map_to: "estado", options: [
                {key: "Programada", label: "Programada"},
                {key: "En Progreso", label: "En Progreso"},
                {key: "Completada", label: "Completada"},
                {key: "Cancelada", label: "Cancelada"}
            ]}},
            {name: "lugar", label: "Lugar", width: 150, editor: {type: "text", map_to: "lugar"}},
            {name: "tipo", label: "Tipo", width: 100, editor: {type: "text", map_to: "tipo"}},
            {name: "progress", label: "Progreso", width: 80, template: function(obj) {
                return Math.round(obj.progress || 0) + "%";
            }, editor: {type: "number", map_to: "progress", min: 0, max: 100}}
        ];
        
        // Configuración de enlaces
        gantt.config.links = {
            finish_to_start: "0",
            start_to_start: "1",
            finish_to_finish: "2",
            start_to_finish: "3"
        };
        
        // Configuración de tooltips
        gantt.templates.tooltip_text = function(start, end, task) {
            const duracion = gantt.calculateDuration(start, end);
            return `<div class="p-3">
                <h4 class="font-bold text-lg mb-2">${task.text}</h4>
                <p class="text-sm mb-1"><strong>Inicio:</strong> ${gantt.templates.tooltip_date_format(start)}</p>
                <p class="text-sm mb-1"><strong>Fin:</strong> ${gantt.templates.tooltip_date_format(end)}</p>
                <p class="text-sm mb-1"><strong>Duración:</strong> ${duracion} días</p>
                <p class="text-sm mb-1"><strong>Estado:</strong> ${task.estado}</p>
                <p class="text-sm mb-1"><strong>Responsable:</strong> ${task.responsable}</p>
                <p class="text-sm mb-1"><strong>Lugar:</strong> ${task.lugar || 'No especificado'}</p>
                <p class="text-sm mb-1"><strong>Progreso:</strong> ${Math.round(task.progress || 0)}%</p>
                ${task.descripcion ? `<p class="text-sm mt-2"><strong>Descripción:</strong> ${task.descripcion}</p>` : ''}
            </div>`;
        };
        
        // Configuración de fechas
        gantt.templates.tooltip_date_format = function(date) {
            return gantt.date.date_to_str("%d/%m/%Y %H:%i")(date);
        };
        
        // Configuración de colores por estado
        gantt.templates.task_class = function(start, end, task) {
            let classes = [];
            
            if (task.estado === 'Completada') {
                classes.push('gantt_completed');
            } else if (task.estado === 'Cancelada') {
                classes.push('gantt_delayed');
            } else if (task.estado === 'En Progreso') {
                classes.push('gantt_task');
            } else {
                classes.push('gantt_project');
            }
            
            // Agregar clase para tareas vencidas
            if (end < new Date() && task.estado !== 'Completada') {
                classes.push('gantt_overdue');
            }
            
            return classes.join(' ');
        };
        
        // Configuración de barras de progreso
        gantt.templates.progress_text = function(start, end, task) {
            return Math.round(task.progress || 0) + "%";
        };
        
        // Eventos del Gantt
        gantt.attachEvent("onTaskClick", function(id, e) {
            if (e.target.classList.contains('gantt_cell')) {
                return true; // Permitir edición inline
            }
            mostrarDetallesActividad(id);
            return false;
        });
        
        gantt.attachEvent("onTaskDblClick", function(id, e) {
            mostrarDetallesActividad(id);
            return false;
        });
        
        // Evento de cambio de tarea
        gantt.attachEvent("onAfterTaskUpdate", function(id, task) {
            actualizarActividadEnBD(id, task);
        });
        
        // Evento de creación de tarea
        gantt.attachEvent("onAfterTaskAdd", function(id, task) {
            console.log("Nueva tarea creada:", task);
        });
        
        // Evento de eliminación de tarea
        gantt.attachEvent("onAfterTaskDelete", function(id) {
            console.log("Tarea eliminada:", id);
        });
        
        // Evento de cambio de fecha
        gantt.attachEvent("onAfterTaskDrag", function(id, mode, e) {
            const task = gantt.getTask(id);
            actualizarActividadEnBD(id, task);
        });
        
        // Función para actualizar actividad en BD
        function actualizarActividadEnBD(id, task) {
            // Aquí se implementaría la llamada AJAX para actualizar la BD
            console.log("Actualizando actividad:", id, task);
            
            // Ejemplo de implementación AJAX:
            /*
            fetch('/api/actividades/actualizar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: id,
                    nombre: task.text,
                    fecha_inicio: task.start_date,
                    fecha_fin: task.end_date,
                    estatus: task.estado,
                    lugar: task.lugar,
                    progreso: task.progress
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion('Actividad actualizada correctamente', 'success');
                } else {
                    mostrarNotificacion('Error al actualizar la actividad', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarNotificacion('Error de conexión', 'error');
            });
            */
        }
        
        // Función para mostrar notificación
        function mostrarNotificacion(mensaje, tipo) {
            const notificacion = document.createElement('div');
            notificacion.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
                tipo === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            notificacion.textContent = mensaje;
            document.body.appendChild(notificacion);
            
            setTimeout(() => {
                notificacion.remove();
            }, 3000);
        }
        
        // Función para mostrar detalles de actividad
        function mostrarDetallesActividad(id) {
            const task = gantt.getTask(id);
            const actividad = actividadesData.find(a => a.id == id);
            
            if (!actividad) return;
            
            const modalContent = `
                <div class="space-y-4">
                    <div>
                        <h4 class="font-semibold text-darkpurple">${actividad.nombre}</h4>
                        <p class="text-sm text-cadet">${actividad.producto_nombre}</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm font-medium text-cadet">Eje:</span>
                            <div>${actividad.eje_nombre}</div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-cadet">Componente:</span>
                            <div>${actividad.componente_nombre}</div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-cadet">Tipo de Actividad:</span>
                            <div>${actividad.tipo_actividad || 'No especificado'}</div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-cadet">Tipo de Población:</span>
                            <div>${actividad.tipo_poblacion || 'No especificado'}</div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-cadet">Estado:</span>
                            <div>${actividad.estado_actividad || 'No especificado'}</div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-cadet">Responsable:</span>
                            <div>${actividad.responsable_nombre ? actividad.responsable_nombre + ' ' + actividad.responsable_apellido : 'No asignado'}</div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-cadet">Organización:</span>
                            <div>${actividad.organizacion_nombre || 'No especificada'}</div>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-cadet">Lugar:</span>
                            <div>${actividad.lugar || 'No especificado'}</div>
                        </div>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-cadet">Fechas:</span>
                        <div class="mt-1">
                            <div><strong>Inicio:</strong> ${actividad.fecha_inicio ? new Date(actividad.fecha_inicio).toLocaleString('es-ES') : 'No especificada'}</div>
                            <div><strong>Fin:</strong> ${actividad.fecha_fin ? new Date(actividad.fecha_fin).toLocaleString('es-ES') : 'No especificada'}</div>
                        </div>
                    </div>
                    
                    ${actividad.descripcion ? `
                        <div>
                            <span class="text-sm font-medium text-cadet">Descripción:</span>
                            <div class="mt-1 p-3 bg-gray-50 rounded-lg">${actividad.descripcion}</div>
                        </div>
                    ` : ''}
                    
                    ${actividad.meta ? `
                        <div>
                            <span class="text-sm font-medium text-cadet">Meta:</span>
                            <div class="mt-1 p-3 bg-blue-50 rounded-lg">${actividad.meta}</div>
                        </div>
                    ` : ''}
                    
                    ${actividad.observaciones ? `
                        <div>
                            <span class="text-sm font-medium text-cadet">Observaciones:</span>
                            <div class="mt-1 p-3 bg-yellow-50 rounded-lg">${actividad.observaciones}</div>
                        </div>
                    ` : ''}
                    
                    <div class="flex gap-2 pt-4">
                        <button onclick="editarActividad(${actividad.id})" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                            <i class="fas fa-edit mr-2"></i>Editar
                        </button>
                        <button onclick="verBeneficiarios(${actividad.id})" class="bg-secondary text-white px-4 py-2 rounded-lg hover:bg-secondary/90 transition-colors">
                            <i class="fas fa-users mr-2"></i>Ver Beneficiarios
                        </button>
                    </div>
                </div>
            `;
            
            document.getElementById('modal-content').innerHTML = modalContent;
            document.getElementById('modal-actividad').classList.remove('hidden');
        }
        
        // Función para editar actividad
        function editarActividad(id) {
            window.location.href = `/actividades/edit.php?id=${id}`;
        }
        
        // Función para ver beneficiarios
        function verBeneficiarios(id) {
            window.location.href = `/captura_especial/ver_beneficiarios.php?id=${id}`;
        }
        
        // Función para cerrar modal
        function cerrarModal() {
            document.getElementById('modal-actividad').classList.add('hidden');
        }
        
        // Función para aplicar filtros
        function aplicarFiltros() {
            const eje = document.getElementById('filtro-eje').value;
            const estado = document.getElementById('filtro-estado').value;
            const responsable = document.getElementById('filtro-responsable').value;
            const fechaInicio = document.getElementById('fecha-inicio').value;
            const fechaFin = document.getElementById('fecha-fin').value;
            
            gantt.filter({
                eje: eje,
                estado: estado,
                responsable: responsable,
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin
            });
        }
        
        // Función para limpiar filtros
        function limpiarFiltros() {
            document.getElementById('filtro-eje').value = '';
            document.getElementById('filtro-estado').value = '';
            document.getElementById('filtro-responsable').value = '';
            document.getElementById('fecha-inicio').value = '';
            document.getElementById('fecha-fin').value = '';
            
            gantt.clearFilter();
        }
        
        // Función para filtrar por estado desde las tarjetas de estadísticas
        function filtrarPorEstado(estado) {
            document.getElementById('filtro-estado').value = estado;
            aplicarFiltros();
            
            // Mostrar notificación
            const mensaje = estado ? `Filtrando por estado: ${estado}` : 'Mostrando todas las actividades';
            mostrarNotificacion(mensaje, 'success');
        }
        
        // Función para filtrar por eje desde las tarjetas de estadísticas
        function filtrarPorEje(eje) {
            document.getElementById('filtro-eje').value = eje;
            aplicarFiltros();
            
            const mensaje = eje ? `Filtrando por eje: ${eje}` : 'Mostrando todos los ejes';
            mostrarNotificacion(mensaje, 'success');
        }
        
        // Función para filtrar por responsable
        function filtrarPorResponsable(responsable) {
            document.getElementById('filtro-responsable').value = responsable;
            aplicarFiltros();
            
            const mensaje = responsable ? `Filtrando por responsable: ${responsable}` : 'Mostrando todos los responsables';
            mostrarNotificacion(mensaje, 'success');
        }
        
        // Función para mostrar actividades vencidas
        function mostrarActividadesVencidas() {
            const hoy = new Date();
            const tareasVencidas = gantt.getTaskByTime().filter(task => {
                return task.end_date < hoy && task.estado !== 'Completada';
            });
            
            if (tareasVencidas.length > 0) {
                gantt.showTask(tareasVencidas[0].id);
                mostrarNotificacion(`Se encontraron ${tareasVencidas.length} actividades vencidas`, 'warning');
            } else {
                mostrarNotificacion('No hay actividades vencidas', 'success');
            }
        }
        
        // Función para mostrar actividades de hoy
        function mostrarActividadesHoy() {
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);
            const manana = new Date(hoy);
            manana.setDate(manana.getDate() + 1);
            
            const tareasHoy = gantt.getTaskByTime(hoy, manana);
            
            if (tareasHoy.length > 0) {
                gantt.showTask(tareasHoy[0].id);
                mostrarNotificacion(`Se encontraron ${tareasHoy.length} actividades para hoy`, 'success');
            } else {
                mostrarNotificacion('No hay actividades programadas para hoy', 'info');
            }
        }
        
        // Función para mostrar actividades de esta semana
        function mostrarActividadesSemana() {
            const hoy = new Date();
            const inicioSemana = new Date(hoy);
            inicioSemana.setDate(hoy.getDate() - hoy.getDay());
            inicioSemana.setHours(0, 0, 0, 0);
            
            const finSemana = new Date(inicioSemana);
            finSemana.setDate(inicioSemana.getDate() + 7);
            
            const tareasSemana = gantt.getTaskByTime(inicioSemana, finSemana);
            
            if (tareasSemana.length > 0) {
                gantt.showTask(tareasSemana[0].id);
                mostrarNotificacion(`Se encontraron ${tareasSemana.length} actividades esta semana`, 'success');
            } else {
                mostrarNotificacion('No hay actividades programadas esta semana', 'info');
            }
        }
        
        // Función para mostrar actividades de este mes
        function mostrarActividadesMes() {
            const hoy = new Date();
            const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
            const finMes = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
            
            const tareasMes = gantt.getTaskByTime(inicioMes, finMes);
            
            if (tareasMes.length > 0) {
                gantt.showTask(tareasMes[0].id);
                mostrarNotificacion(`Se encontraron ${tareasMes.length} actividades este mes`, 'success');
            } else {
                mostrarNotificacion('No hay actividades programadas este mes', 'info');
            }
        }
        
        // Función para calcular estadísticas en tiempo real
        function actualizarEstadisticas() {
            const todasLasTareas = gantt.getTaskByTime();
            const total = todasLasTareas.length;
            const completadas = todasLasTareas.filter(t => t.estado === 'Completada').length;
            const enProgreso = todasLasTareas.filter(t => t.estado === 'En Progreso').length;
            const programadas = todasLasTareas.filter(t => t.estado === 'Programada').length;
            const canceladas = todasLasTareas.filter(t => t.estado === 'Cancelada').length;
            
            // Actualizar contadores en las tarjetas
            document.querySelectorAll('.stats-card').forEach((card, index) => {
                const contador = card.querySelector('.text-2xl');
                if (contador) {
                    switch(index) {
                        case 0: contador.textContent = total; break;
                        case 1: contador.textContent = completadas; break;
                        case 2: contador.textContent = enProgreso; break;
                        case 3: contador.textContent = programadas; break;
                        case 4: contador.textContent = canceladas; break;
                    }
                }
            });
        }
        
        // Función para exportar datos filtrados
        function exportarDatosFiltrados() {
            const tareasVisibles = gantt.getTaskByTime();
            const datos = tareasVisibles.map(tarea => ({
                Actividad: tarea.text,
                Responsable: tarea.responsable,
                Estado: tarea.estado,
                Lugar: tarea.lugar,
                Fecha_Inicio: gantt.templates.tooltip_date_format(tarea.start_date),
                Fecha_Fin: gantt.templates.tooltip_date_format(tarea.end_date),
                Progreso: Math.round(tarea.progress || 0) + '%',
                Eje: tarea.eje,
                Componente: tarea.componente,
                Producto: tarea.producto
            }));
            
            // Crear CSV
            const headers = Object.keys(datos[0]);
            const csvContent = [
                headers.join(','),
                ...datos.map(row => headers.map(header => `"${row[header]}"`).join(','))
            ].join('\n');
            
            // Descargar archivo
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `actividades_filtradas_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        
        // Funciones de zoom
        function zoomIn() {
            gantt.ext.zoom.zoomIn();
        }
        
        function zoomOut() {
            gantt.ext.zoom.zoomOut();
        }
        
        function zoomToFit() {
            gantt.ext.zoom.setLevel("month");
        }
        
        // Inicialización del Gantt
        gantt.init("gantt_container");
        
        // Cargar extensión de zoom
        gantt.ext.zoom.init({
            levels: [
                {
                    name: "day",
                    scale_height: 60,
                    min_column_width: 70,
                    scales: [
                        {unit: "day", step: 1, format: "%d %M"}
                    ]
                },
                {
                    name: "week",
                    scale_height: 60,
                    min_column_width: 70,
                    scales: [
                        {unit: "week", step: 1, format: "Semana #%W"}
                    ]
                },
                {
                    name: "month",
                    scale_height: 60,
                    min_column_width: 120,
                    scales: [
                        {unit: "month", step: 1, format: "%F, %Y"}
                    ]
                },
                {
                    name: "quarter",
                    scale_height: 60,
                    min_column_width: 90,
                    scales: [
                        {unit: "quarter", step: 1, format: "%Q %Y"}
                    ]
                },
                {
                    name: "year",
                    scale_height: 60,
                    min_column_width: 120,
                    scales: [
                        {unit: "year", step: 1, format: "%Y"}
                    ]
                }
            ]
        });
        
        // Preparar datos para el Gantt
        const tasks = {
            data: actividadesData.map(actividad => ({
                id: actividad.id,
                text: actividad.nombre,
                start_date: actividad.fecha_inicio ? new Date(actividad.fecha_inicio) : new Date(),
                end_date: actividad.fecha_fin ? new Date(actividad.fecha_fin) : new Date(),
                responsable: actividad.responsable_nombre ? actividad.responsable_nombre + ' ' + actividad.responsable_apellido : 'No asignado',
                estado: actividad.estatus,
                lugar: actividad.lugar || 'No especificado',
                tipo: actividad.tipo_actividad || 'No especificado',
                progress: 0, // Se puede calcular basado en beneficiarios registrados
                descripcion: actividad.descripcion,
                eje: actividad.eje_nombre,
                componente: actividad.componente_nombre,
                producto: actividad.producto_nombre,
                meta: actividad.meta,
                observaciones: actividad.observaciones,
                organizacion: actividad.organizacion_nombre,
                estado_actividad: actividad.estado_actividad,
                tipo_poblacion: actividad.tipo_poblacion,
                poligono: actividad.poligono_nombre
            }))
        };
        
        // Cargar datos
        gantt.parse(tasks);
        
        // Llenar filtros
        const ejes = [...new Set(actividadesData.map(a => a.eje_nombre).filter(Boolean))];
        const responsables = [...new Set(actividadesData.map(a => a.responsable_nombre ? a.responsable_nombre + ' ' + a.responsable_apellido : null).filter(Boolean))];
        
        const filtroEje = document.getElementById('filtro-eje');
        ejes.forEach(eje => {
            const option = document.createElement('option');
            option.value = eje;
            option.textContent = eje;
            filtroEje.appendChild(option);
        });
        
        const filtroResponsable = document.getElementById('filtro-responsable');
        responsables.forEach(responsable => {
            const option = document.createElement('option');
            option.value = responsable;
            option.textContent = responsable;
            filtroResponsable.appendChild(option);
        });
        
        // Configurar filtros personalizados
        gantt.filter = function(tasks, filters) {
            return tasks.filter(function(task) {
                if (filters.eje && task.eje !== filters.eje) return false;
                if (filters.estado && task.estado !== filters.estado) return false;
                if (filters.responsable && task.responsable !== filters.responsable) return false;
                if (filters.fecha_inicio && task.start_date < new Date(filters.fecha_inicio)) return false;
                if (filters.fecha_fin && task.end_date > new Date(filters.fecha_fin)) return false;
                return true;
            });
        };
        
        // Cerrar modal al hacer clic fuera
        document.getElementById('modal-actividad').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModal();
            }
        });
        
        // Configurar controles de zoom
        document.addEventListener('DOMContentLoaded', function() {
            // Agregar controles de zoom al header
            const headerControls = document.querySelector('.flex.flex-wrap.gap-2');
            if (headerControls) {
                const zoomControls = document.createElement('div');
                zoomControls.className = 'flex gap-2 ml-4';
                zoomControls.innerHTML = `
                    <button onclick="zoomOut()" class="bg-cadet text-white px-3 py-2 rounded-lg hover:bg-cadet/90 transition-colors" title="Alejar">
                        <i class="fas fa-search-minus"></i>
                    </button>
                    <button onclick="zoomToFit()" class="bg-cadet text-white px-3 py-2 rounded-lg hover:bg-cadet/90 transition-colors" title="Ajustar">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </button>
                    <button onclick="zoomIn()" class="bg-cadet text-white px-3 py-2 rounded-lg hover:bg-cadet/90 transition-colors" title="Acercar">
                        <i class="fas fa-search-plus"></i>
                    </button>
                `;
                headerControls.appendChild(zoomControls);
            }
        });
    </script>
</body>
</html>
