<?php

class DeepSeekService
{
    private $apiKey;
    private $apiUrl = 'https://api.deepseek.com/v1/chat/completions';
    private $contextHistory = [];
    private $queryPatterns = [];
    
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->initializeQueryPatterns();
    }
    
    private function initializeQueryPatterns()
    {
        $this->queryPatterns = [
            'reportes_financieros' => [
                'keywords' => ['reporte', 'informe', 'ganancia', 'beneficio', 'utilidad', 'rendimiento', 'financiero'],
                'context' => 'Consulta sobre reportes financieros y ganancias'
            ],
            'clientes_info' => [
                'keywords' => ['cliente', 'clientes', 'persona', 'personas', 'usuario', 'usuarios'],
                'context' => 'Información sobre clientes del sistema'
            ],
            'inversiones_estado' => [
                'keywords' => ['inversion', 'inversiones', 'capital', 'dinero', 'activo', 'vencido'],
                'context' => 'Estado y detalles de inversiones'
            ],
            'pagos_programados' => [
                'keywords' => ['pago', 'pagos', 'cobro', 'pendiente', 'vence', 'fecha'],
                'context' => 'Pagos programados y pendientes'
            ],
            'estadisticas_generales' => [
                'keywords' => ['estadistica', 'resumen', 'total', 'cantidad', 'cuanto', 'cuanta'],
                'context' => 'Estadísticas generales del sistema'
            ]
        ];
    }
    
    public function generateSQLQuery($userMessage, $userRole, $userId = null)
    {
        // Agregar contexto del mensaje
        $this->addToContext($userMessage);
        
        // Detectar el tipo de consulta
        $queryType = $this->detectQueryType($userMessage);
        
        $systemPrompt = $this->getEnhancedSystemPrompt($userRole, $userId, $queryType);
        
        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt
            ],
            [
                'role' => 'user',
                'content' => $this->enhanceUserMessage($userMessage, $queryType)
            ]
        ];
        
        // Agregar contexto histórico si existe
        if (!empty($this->contextHistory)) {
            $contextMessage = "Contexto de conversación anterior: " . implode('. ', array_slice($this->contextHistory, -3));
            array_splice($messages, 1, 0, [[
                'role' => 'assistant',
                'content' => $contextMessage
            ]]);
        }
        
        $data = [
            'model' => 'deepseek-chat',
            'messages' => $messages,
            'temperature' => 0.1,
            'max_tokens' => 2000,
            'top_p' => 0.95
        ];
        
        $response = $this->makeApiCall($data);
        return $this->parseResponse($response);
    }
    
    private function addToContext($message)
    {
        $this->contextHistory[] = $message;
        if (count($this->contextHistory) > 5) {
            array_shift($this->contextHistory);
        }
    }
    
    private function detectQueryType($message)
    {
        $messageLower = mb_strtolower($message, 'UTF-8');
        $scores = [];
        
        foreach ($this->queryPatterns as $type => $pattern) {
            $score = 0;
            foreach ($pattern['keywords'] as $keyword) {
                if (strpos($messageLower, $keyword) !== false) {
                    $score += substr_count($messageLower, $keyword);
                }
            }
            if ($score > 0) {
                $scores[$type] = $score;
            }
        }
        
        if (empty($scores)) {
            return 'general';
        }
        
        arsort($scores);
        return array_key_first($scores);
    }
    
    private function enhanceUserMessage($message, $queryType)
    {
        $enhancements = [
            'reportes_financieros' => 'Necesito generar un reporte financiero. ',
            'clientes_info' => 'Consulta sobre información de clientes. ',
            'inversiones_estado' => 'Consulta sobre el estado de inversiones. ',
            'pagos_programados' => 'Consulta sobre pagos y fechas. ',
            'estadisticas_generales' => 'Necesito estadísticas del sistema. '
        ];
        
        $enhancement = $enhancements[$queryType] ?? '';
        return $enhancement . $message;
    }
    
    private function getEnhancedSystemPrompt($userRole, $userId, $queryType)
    {
        $baseSchema = $this->getDatabaseSchema();
        $examples = $this->getQueryExamples($queryType);
        $specialInstructions = $this->getSpecialInstructions($queryType);

        $restrictionMessage = "

⚠️ RESTRICCIONES CRÍTICAS:
- SOLO genera consultas SELECT (lectura de datos) - NUNCA INSERT, UPDATE, DELETE, CREATE, DROP, ALTER, etc.
- Si detectas intención de modificar datos, responde exactamente: 'RESTRICTED_ACTION'
- Si no puedes generar una consulta válida, responde exactamente: 'NO_QUERY'
- SIEMPRE incluye LIMIT para evitar consultas masivas (máximo 50 registros)
- Usa COALESCE o NULLIF cuando sea necesario para evitar errores
- Para fechas usa CURRENT_DATE, CURRENT_TIMESTAMP según corresponda
- Para JSON usa la sintaxis (campo_json).subcampo
";
        
        if ($userRole === 'admin') {
            return "Eres un experto en SQL y PostgreSQL especializado en sistemas de inversiones financieras.

TIPO DE CONSULTA DETECTADA: {$queryType}

ESQUEMA DE BASE DE DATOS:
{$baseSchema}

EJEMPLOS ESPECÍFICOS PARA ESTE TIPO DE CONSULTA:
{$examples}

INSTRUCCIONES ESPECIALES:
{$specialInstructions}

{$restrictionMessage}
            
Genera SOLO consultas SELECT válidas en PostgreSQL basadas en las preguntas del usuario.
Puedes acceder a todas las tablas del sistema para consultas de lectura.
Respuesta debe ser SOLO la consulta SQL, sin explicaciones adicionales.
Si no puedes generar una consulta válida, responde: 'NO_QUERY'";
        } else {
            return "Eres un asistente de base de datos para un sistema de inversiones.
            
TIPO DE CONSULTA: {$queryType}

Esquema de la base de datos:
{$baseSchema}

Ejemplos relevantes:
{$examples}

{$restrictionMessage}
            
Genera SOLO consultas SELECT válidas en PostgreSQL basadas en las preguntas del usuario.
Este usuario es un CLIENTE (ID: {$userId}), solo puede ver:
- Sus propios datos de cliente
- Sus propias inversiones
- Sus propios pagos
- Estadísticas generales del sistema

Siempre incluye WHERE cliente.id = {$userId} o WHERE i.cliente_id = {$userId} según corresponda.
Respuesta debe ser SOLO la consulta SQL, sin explicaciones adicionales.
Si no puedes generar una consulta válida, responde: 'NO_QUERY'";
        }
    }
    
    private function getDatabaseSchema()
    {
        return "
TABLAS PRINCIPALES:

1. cliente:
   - id (PK)
   - datos_personales (JSON: nombres, apellido_paterno, apellido_materno, dni, direccion, celular1, celular2)
   - estado_civil
   - estado (boolean)
   - usuario_id (FK)
   - created_at, updated_at

2. inversion:
   - id (PK)
   - cliente_id (FK)
   - monto (decimal)
   - moneda (varchar)
   - tasa_interes (decimal)
   - plan_inversion (integer: 1=mensual, 6=semestral, 12=anual)
   - fecha_inicio (date)
   - fecha_vencimiento (date)
   - fecha_calculada (timestamp)
   - estado (varchar: 'activo', 'vencido', 'cancelado')
   - meses (integer)
   - created_at, updated_at

3. pago:
   - id (PK)
   - inversion_id (FK)
   - monto (decimal)
   - fecha (date)
   - numero_pago (integer)
   - estado (boolean: true=pagado, false=pendiente)
   - comprobante (varchar)
   - created_at, updated_at

4. usuario:
   - id (PK)
   - username (varchar)
   - email (varchar)
   - rol (varchar: 'admin', 'cliente')
   - estado (boolean)
   - created_at, updated_at

RELACIONES:
- cliente.usuario_id → usuario.id
- inversion.cliente_id → cliente.id
- pago.inversion_id → inversion.id

EJEMPLOS DE SINTAXIS:
- Para JSON: SELECT (datos_personales).nombres, (datos_personales).apellido_paterno FROM cliente
- Para fechas: WHERE fecha >= CURRENT_DATE - INTERVAL '30 days'
- Para uniones: INNER JOIN cliente c ON i.cliente_id = c.id
";
    }
    
    private function getQueryExamples($queryType = 'general')
    {
        $examples = [
            'reportes_financieros' => "
EJEMPLOS DE REPORTES FINANCIEROS:

1. Ganancias del mes actual:
SELECT 
    SUM(p.monto) as total_cobrado,
    COUNT(p.id) as total_pagos,
    COUNT(DISTINCT i.cliente_id) as clientes_activos
FROM pago p
INNER JOIN inversion i ON p.inversion_id = i.id
WHERE EXTRACT(MONTH FROM p.fecha) = EXTRACT(MONTH FROM CURRENT_DATE)
AND EXTRACT(YEAR FROM p.fecha) = EXTRACT(YEAR FROM CURRENT_DATE)
AND p.estado = true;

2. Reporte de inversiones por estado:
SELECT 
    i.estado,
    COUNT(*) as cantidad,
    SUM(i.monto) as monto_total
FROM inversion i
GROUP BY i.estado
ORDER BY monto_total DESC;

3. Top clientes por inversión:
SELECT 
    (c.datos_personales).nombres || ' ' || (c.datos_personales).apellido_paterno as cliente,
    SUM(i.monto) as total_invertido,
    COUNT(i.id) as num_inversiones
FROM cliente c
INNER JOIN inversion i ON c.id = i.cliente_id
GROUP BY c.id, (c.datos_personales).nombres, (c.datos_personales).apellido_paterno
ORDER BY total_invertido DESC
LIMIT 10;",
            
            'clientes_info' => "
EJEMPLOS DE CONSULTAS DE CLIENTES:

1. Lista de clientes activos:
SELECT 
    (datos_personales).nombres,
    (datos_personales).apellido_paterno,
    (datos_personales).dni,
    (datos_personales).celular1
FROM cliente
WHERE estado = true
ORDER BY (datos_personales).apellido_paterno
LIMIT 20;

2. Buscar cliente por DNI:
SELECT 
    (datos_personales).nombres || ' ' || (datos_personales).apellido_paterno as nombre_completo,
    (datos_personales).dni,
    estado_civil,
    estado
FROM cliente
WHERE (datos_personales).dni = '12345678';

3. Clientes con más inversiones:
SELECT 
    (c.datos_personales).nombres || ' ' || (c.datos_personales).apellido_paterno as cliente,
    COUNT(i.id) as num_inversiones,
    SUM(i.monto) as total_invertido
FROM cliente c
LEFT JOIN inversion i ON c.id = i.cliente_id
GROUP BY c.id, (c.datos_personales).nombres, (c.datos_personales).apellido_paterno
HAVING COUNT(i.id) > 0
ORDER BY num_inversiones DESC
LIMIT 15;",
            
            'inversiones_estado' => "
EJEMPLOS DE CONSULTAS DE INVERSIONES:

1. Inversiones activas:
SELECT 
    i.id,
    (c.datos_personales).nombres || ' ' || (c.datos_personales).apellido_paterno as cliente,
    i.monto,
    i.moneda,
    i.plan_inversion,
    i.fecha_vencimiento
FROM inversion i
INNER JOIN cliente c ON i.cliente_id = c.id
WHERE i.estado = 'activo'
ORDER BY i.fecha_vencimiento ASC
LIMIT 25;

2. Inversiones que vencen pronto:
SELECT 
    i.id,
    (c.datos_personales).nombres || ' ' || (c.datos_personales).apellido_paterno as cliente,
    i.monto,
    i.fecha_vencimiento,
    (i.fecha_vencimiento - CURRENT_DATE) as dias_restantes
FROM inversion i
INNER JOIN cliente c ON i.cliente_id = c.id
WHERE i.estado = 'activo'
AND i.fecha_vencimiento <= CURRENT_DATE + INTERVAL '30 days'
ORDER BY i.fecha_vencimiento ASC;

3. Inversiones por rango de monto:
SELECT 
    i.id,
    (c.datos_personales).nombres || ' ' || (c.datos_personales).apellido_paterno as cliente,
    i.monto,
    i.moneda,
    i.estado
FROM inversion i
INNER JOIN cliente c ON i.cliente_id = c.id
WHERE i.monto BETWEEN 1000 AND 10000
ORDER BY i.monto DESC
LIMIT 20;",
            
            'pagos_programados' => "
EJEMPLOS DE CONSULTAS DE PAGOS:

1. Pagos pendientes:
SELECT 
    (c.datos_personales).nombres || ' ' || (c.datos_personales).apellido_paterno as cliente,
    i.monto as monto_inversion,
    p.monto as monto_pago,
    p.fecha,
    p.numero_pago
FROM pago p
INNER JOIN inversion i ON p.inversion_id = i.id
INNER JOIN cliente c ON i.cliente_id = c.id
WHERE p.estado = false
ORDER BY p.fecha ASC
LIMIT 30;

2. Pagos de hoy:
SELECT 
    (c.datos_personales).nombres || ' ' || (c.datos_personales).apellido_paterno as cliente,
    p.monto,
    p.numero_pago,
    i.moneda
FROM pago p
INNER JOIN inversion i ON p.inversion_id = i.id
INNER JOIN cliente c ON i.cliente_id = c.id
WHERE DATE(p.fecha) = CURRENT_DATE
ORDER BY p.monto DESC;

3. Pagos de la semana:
SELECT 
    DATE(p.fecha) as fecha_pago,
    COUNT(*) as cantidad_pagos,
    SUM(p.monto) as total_dia
FROM pago p
WHERE p.fecha >= CURRENT_DATE - INTERVAL '7 days'
AND p.fecha <= CURRENT_DATE
AND p.estado = true
GROUP BY DATE(p.fecha)
ORDER BY fecha_pago DESC;",
            
            'estadisticas_generales' => "
EJEMPLOS DE ESTADÍSTICAS GENERALES:

1. Resumen general del sistema:
SELECT 
    (SELECT COUNT(*) FROM cliente WHERE estado = true) as total_clientes,
    (SELECT COUNT(*) FROM inversion WHERE estado = 'activo') as inversiones_activas,
    (SELECT SUM(monto) FROM inversion WHERE estado = 'activo') as capital_activo,
    (SELECT COUNT(*) FROM pago WHERE estado = false) as pagos_pendientes;

2. Estadísticas por moneda:
SELECT 
    i.moneda,
    COUNT(*) as num_inversiones,
    SUM(i.monto) as total_invertido,
    AVG(i.monto) as promedio_inversion
FROM inversion i
WHERE i.estado = 'activo'
GROUP BY i.moneda
ORDER BY total_invertido DESC;

3. Rendimiento por plan de inversión:
SELECT 
    i.plan_inversion,
    COUNT(*) as cantidad,
    SUM(i.monto) as total_capital,
    AVG(i.tasa_interes) as tasa_promedio
FROM inversion i
WHERE i.estado = 'activo'
GROUP BY i.plan_inversion
ORDER BY i.plan_inversion;",
            
            'general' => "
EJEMPLOS GENERALES:

1. Información básica de cliente:
SELECT (datos_personales).nombres, (datos_personales).apellido_paterno FROM cliente WHERE id = 1;

2. Inversiones de un cliente:
SELECT * FROM inversion WHERE cliente_id = 1 ORDER BY fecha_inicio DESC LIMIT 10;

3. Pagos recientes:
SELECT * FROM pago WHERE fecha >= CURRENT_DATE - INTERVAL '7 days' ORDER BY fecha DESC LIMIT 15;"
        ];
        
        return $examples[$queryType] ?? $examples['general'];
    }
    
    private function getSpecialInstructions($queryType)
    {
        $instructions = [
            'reportes_financieros' => "- Incluye siempre SUM, COUNT, AVG cuando sea relevante\n- Usa GROUP BY para agrupar datos\n- Incluye fechas en los filtros\n- Calcula totales y promedios",
            'clientes_info' => "- Usa la sintaxis (datos_personales).campo para JSON\n- Incluye nombres completos concatenados\n- Filtra por estado activo cuando sea relevante",
            'inversiones_estado' => "- Filtra por estado de inversión\n- Incluye fechas de vencimiento\n- Calcula días restantes cuando sea útil",
            'pagos_programados' => "- Filtra por estado de pago (true/false)\n- Usa DATE() para comparaciones de fechas\n- Incluye información del cliente e inversión",
            'estadisticas_generales' => "- Usa subconsultas para estadísticas complejas\n- Incluye COUNT, SUM, AVG\n- Agrupa por categorías relevantes",
            'general' => "- Mantén las consultas simples y claras\n- Incluye LIMIT apropiado\n- Usa JOINs cuando necesites datos relacionados"
        ];
        
        return $instructions[$queryType] ?? $instructions['general'];
    }
    
    private function makeApiCall($data)
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception('Error de cURL: ' . $error);
        }
        
        if ($httpCode !== 200) {
            throw new Exception('Error en la API de DeepSeek: ' . $httpCode . ' - ' . $response);
        }
        
        return json_decode($response, true);
    }
    
    private function parseResponse($response)
    {
        if (!isset($response['choices'][0]['message']['content'])) {
            throw new Exception('Respuesta inválida de la API');
        }
        
        $content = trim($response['choices'][0]['message']['content']);
        
        // Usar el nuevo método de validación
        return $this->validateAndCleanQuery($content);
    }
    
    private function validateAndCleanQuery($content)
    {
        // Limpiar la respuesta
        $content = preg_replace('/```sql\s*/', '', $content);
        $content = preg_replace('/```\s*$/', '', $content);
        $content = preg_replace('/^```/', '', $content);
        $content = trim($content);
        
        // Validar respuestas especiales
        if (in_array($content, ['RESTRICTED_ACTION', 'NO_QUERY'])) {
            return $content;
        }
        
        // Validar que sea una consulta SELECT válida
        if (!preg_match('/^\s*SELECT\s+/i', $content)) {
            error_log("DeepSeek generó consulta inválida: " . $content);
            return 'NO_QUERY';
        }
        
        // Verificar que no tenga comandos prohibidos
        $prohibitedWords = ['INSERT', 'UPDATE', 'DELETE', 'DROP', 'CREATE', 'ALTER', 'TRUNCATE'];
        $contentUpper = strtoupper($content);
        
        foreach ($prohibitedWords as $word) {
            if (strpos($contentUpper, $word) !== false) {
                error_log("DeepSeek generó consulta con comando prohibido: " . $word);
                return 'RESTRICTED_ACTION';
            }
        }
        
        return $content;
    }
    
    // Método adicional para procesar respuestas de consultas
    public function processQueryResult($sqlQuery, $userMessage)
    {
        if (in_array($sqlQuery, ['RESTRICTED_ACTION', 'NO_QUERY'])) {
            return null;
        }
        
        // Aquí podrías agregar lógica adicional para procesar el resultado
        // Por ejemplo, logging, validaciones adicionales, etc.
        
        return $sqlQuery;
    }
    
    public function getSuggestions($userRole)
    {
        $suggestions = [
            'admin' => [
                'Mostrar ganancias del mes actual',
                'Listar clientes con más inversiones',
                'Pagos pendientes de esta semana',
                'Inversiones que vencen pronto',
                'Estadísticas generales del sistema'
            ],
            'cliente' => [
                'Ver mis inversiones activas',
                'Mostrar mis próximos pagos',
                'Estado de mis inversiones',
                'Historial de mis pagos'
            ]
        ];
        
        return $suggestions[$userRole] ?? $suggestions['cliente'];
    }
}
?>