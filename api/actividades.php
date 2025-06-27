<?php
header('Content-Type: application/json');
// Simular autenticación por token
$headers = getallheaders();
$token = $headers['Authorization'] ?? '';
if ($token !== 'Bearer TOKEN_DEMO_123') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Token inválido.']);
    exit;
}
// Simular actividades asignadas
$actividades = [
    [
        'id' => 1,
        'nombre' => 'Taller de Inclusión Social',
        'fecha_inicio' => '2024-07-01',
        'fecha_fin' => '2024-07-10',
        'lugar' => 'Centro Comunitario',
    ],
    [
        'id' => 2,
        'nombre' => 'Capacitación en Carpintería',
        'fecha_inicio' => '2024-07-15',
        'fecha_fin' => '2024-07-20',
        'lugar' => 'Escuela Técnica',
    ]
];
echo json_encode(['success' => true, 'actividades' => $actividades]);
