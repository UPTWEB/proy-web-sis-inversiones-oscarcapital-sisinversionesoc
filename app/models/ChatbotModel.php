<?php
require_once '../core/Model.php';

$deepSeekPath = __DIR__ . '/../libreries/DeepSeekService.php';
if (file_exists($deepSeekPath)) {
    require_once $deepSeekPath;
}

class ChatbotModel
{
    private $conn;
    private $patterns;
    private $deepSeekService;
    private $userRole;
    private $userId;
    private $clienteId;
    private $aiEnabled;
    private $contextHistory = [];

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->initializePatterns();
        
        $this->aiEnabled = class_exists('DeepSeekService');
        
        if ($this->aiEnabled) {
            try {
                $this->deepSeekService = new DeepSeekService('sk-838aa95385d543d4831195c744efc5ff');
            } catch (Exception $e) {
                error_log("Error inicializando DeepSeekService: " . $e->getMessage());
                $this->deepSeekService = null;
                $this->aiEnabled = false;
            }
        } else {
            $this->deepSeekService = null;
        }
        
        $this->userRole = $_SESSION['usuario_rol'] ?? 'cliente';
        $this->userId = $_SESSION['usuario_id'] ?? null;
        $this->clienteId = $this->getClienteId();
    }
    
    private function getClienteId()
    {
        if ($this->userRole === 'cliente' && $this->userId) {
            try {
                $sql = "SELECT id FROM cliente WHERE usuario_id = :usuario_id";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([':usuario_id' => $this->userId]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['id'] ?? null;
            } catch (Exception $e) {
                error_log("Error obteniendo cliente ID: " . $e->getMessage());
                return null;
            }
        }
        return null;
    }

    private function initializePatterns()
    {
        // Patrones MUY flexibles y amplios
        $this->patterns = [
            // Consultas de ganancias/reportes financieros
            'ganancias' => [
                'patterns' => [
                    '/(?:ganancia|ganancia|beneficio|utilidad|profit).*(?:mes|mensual|este mes|actual)/i',
                    '/(?:cuanto|cuanta|total).*(?:ganamos|ganado|beneficio|utilidad).*(?:mes|mensual)/i',
                    '/reporte.*(?:ganancia|financiero|mes|mensual)/i',
                    '/(?:dinero|plata|efectivo).*(?:ganado|obtenido).*mes/i'
                ],
                'method' => 'getGananciasDelMes'
            ],
            
            // Clientes - Súper flexible
            'clientes' => [
                'patterns' => [
                    '/(?:cliente|clientes)/i',
                    '/(?:persona|personas).*(?:registrada|registrado|activa)/i',
                    '/(?:cuantos|cuantas|total|cantidad|numero).*(?:usuario|usuarios|gente)/i',
                    '/(?:lista|listar|mostrar|ver).*(?:gente|personas)/i'
                ],
                'method' => 'handleClientesQuery'
            ],
            
            // Inversiones - Muy flexible
            'inversiones' => [
                'patterns' => [
                    '/(?:inversion|inversiones|capital|dinero|plata|fondos)/i',
                    '/(?:deposito|depositos|ahorro|ahorros)/i',
                    '/(?:plazo|plazos|plan|planes)/i',
                    '/(?:activo|activos|activa|activas)/i'
                ],
                'method' => 'handleInversionesQuery'
            ],
            
            // Pagos - Flexible temporal
            'pagos' => [
                'patterns' => [
                    '/(?:pago|pagos|cobro|cobros|cobranza)/i',
                    '/(?:ayer|hoy|mañana|semana|mes|día)/i',
                    '/(?:quien|quién|quienes|cuales|que)/i',
                    '/(?:debe|deben|pendiente|pendientes)/i'
                ],
                'method' => 'handlePagosQuery'
            ],
            
            // Búsquedas generales - Muy amplia
            'busqueda' => [
                'patterns' => [
                    '/(?:buscar|busca|encontrar|busque|ver|mostrar)/i',
                    '/(?:info|información|datos|detalles)/i',
                    '/(?:con|tiene|cuyo|cuya)/i',
                    '/(?:donde|cuando|como|cual)/i'
                ],
                'method' => 'handleBusquedaQuery'
            ],
            
            // Estados y estadísticas
            'estadisticas' => [
                'patterns' => [
                    '/(?:estado|estados|situacion|estadistica|estadísticas)/i',
                    '/(?:resumen|reporte|informe|dashboard)/i',
                    '/(?:general|global|total|completo)/i'
                ],
                'method' => 'handleEstadisticasQuery'
            ]
        ];
    }

    public function processQuery($message)
    {
        try {
            $message = trim($message);
            $originalMessage = $message;
            $messageLower = mb_strtolower($message, 'UTF-8');
            
            // Agregar mensaje al contexto
            $this->addToContext($message);
            
            // PRIORIDAD 1: Usar IA si está disponible (máxima flexibilidad)
            if ($this->aiEnabled && $this->deepSeekService !== null) {
                $aiResponse = $this->processWithAI($originalMessage);
                if ($aiResponse !== null && !$this->isGenericResponse($aiResponse)) {
                    return $aiResponse;
                }
            }
            
            // PRIORIDAD 2: Intentar con patrones flexibles
            $patternResult = $this->tryPatternMatching($messageLower, $originalMessage);
            if ($patternResult !== null) {
                return $patternResult;
            }
            
            // PRIORIDAD 3: Análisis inteligente de palabras clave
            $keywordResult = $this->analyzeKeywords($messageLower, $originalMessage);
            if ($keywordResult !== null) {
                return $keywordResult;
            }
            
            // PRIORIDAD 4: Respuesta por defecto con sugerencias inteligentes
            return $this->getSmartDefaultResponse($originalMessage);
            
        } catch (Exception $e) {
            error_log("Error en processQuery: " . $e->getMessage());
            return "Ocurrió un error al procesar tu consulta. 🔄 Intenta reformular tu pregunta.";
        }
    }
    
    private function addToContext($message)
    {
        $this->contextHistory[] = $message;
        if (count($this->contextHistory) > 5) {
            array_shift($this->contextHistory);
        }
    }
    
    private function isGenericResponse($response)
    {
        $genericPhrases = [
            'no se encontraron resultados',
            'error al procesar',
            'intenta nuevamente',
            'no puedo ayudarte'
        ];
        
        $responseLower = strtolower($response);
        foreach ($genericPhrases as $phrase) {
            if (strpos($responseLower, $phrase) !== false) {
                return true;
            }
        }
        return false;
    }
    
    private function tryPatternMatching($messageLower, $originalMessage)
    {
        foreach ($this->patterns as $category => $config) {
            foreach ($config['patterns'] as $pattern) {
                if (preg_match($pattern, $messageLower, $matches)) {
                    $method = $config['method'];
                    return $this->$method($originalMessage, $matches);
                }
            }
        }
        return null;
    }
    
    private function analyzeKeywords($messageLower, $originalMessage)
    {
        // Análisis de palabras clave más sofisticado
        $keywordCategories = [
            'clientes' => ['cliente', 'clientes', 'persona', 'personas', 'usuario', 'usuarios', 'gente'],
            'inversiones' => ['inversion', 'inversiones', 'dinero', 'plata', 'capital', 'deposito', 'ahorro', 'plan', 'plazo'],
            'pagos' => ['pago', 'pagos', 'cobro', 'cobros', 'debe', 'deben', 'pendiente'],
            'ganancias' => ['ganancia', 'ganancias', 'beneficio', 'utilidad', 'profit', 'rendimiento'],
            'fechas' => ['ayer', 'hoy', 'mañana', 'semana', 'mes', 'año', 'día'],
            'busqueda' => ['buscar', 'encontrar', 'ver', 'mostrar', 'info', 'información', 'datos']
        ];
        
        $scores = [];
        foreach ($keywordCategories as $category => $keywords) {
            $score = 0;
            foreach ($keywords as $keyword) {
                if (strpos($messageLower, $keyword) !== false) {
                    $score += substr_count($messageLower, $keyword);
                }
            }
            if ($score > 0) {
                $scores[$category] = $score;
            }
        }
        
        if (empty($scores)) {
            return null;
        }
        
        arsort($scores);
        $topCategory = array_key_first($scores);
        
        // Llamar al método correspondiente
        switch ($topCategory) {
            case 'clientes':
                return $this->handleClientesQuery($originalMessage);
            case 'inversiones':
                return $this->handleInversionesQuery($originalMessage);
            case 'pagos':
                return $this->handlePagosQuery($originalMessage);
            case 'ganancias':
                return $this->getGananciasDelMes();
            case 'busqueda':
                return $this->handleBusquedaQuery($originalMessage);
            default:
                return null;
        }
    }
    
    // Métodos flexibles para manejar diferentes tipos de consultas
    private function handleClientesQuery($message, $matches = [])
    {
        $messageLower = strtolower($message);
        
        // Detectar tipo de consulta sobre clientes
        if (preg_match('/(?:cuanto|cuanta|total|numero|cantidad)/i', $message)) {
            return $this->getClientesInfo('total');
        } elseif (preg_match('/(?:lista|listar|mostrar|ver|todos)/i', $message)) {
            return $this->getClientesInfo('lista');
        } elseif (preg_match('/(?:nuevo|nuevos|reciente|últimos)/i', $message)) {
            return $this->getClientesInfo('recientes');
        } elseif (preg_match('/(?:dni|documento|cedula|nombre|apellido).*([0-9a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+)/i', $message, $matches)) {
            return $this->buscarCliente(trim($matches[1]));
        } else {
            // Consulta general sobre clientes
            return $this->getClientesInfo();
        }
    }
    
    private function handleInversionesQuery($message, $matches = [])
    {
        $messageLower = strtolower($message);
        
        if (preg_match('/(?:cuanto|cuanta|total|numero|cantidad)/i', $message)) {
            return $this->getInversionesInfo('total');
        } elseif (preg_match('/(?:lista|listar|mostrar|ver|todos)/i', $message)) {
            return $this->getInversionesInfo('lista');
        } elseif (preg_match('/(?:activo|activos|activa|activas)/i', $message)) {
            return $this->getInversionesInfo('activas');
        } elseif (preg_match('/(?:vencido|vencidos|vencida|vencidas)/i', $message)) {
            return $this->getInversionesInfo('vencidas');
        } elseif (preg_match('/(?:monto|dinero|plata|capital).*(?:mayor|menor|superior|inferior).*([0-9]+)/i', $message, $matches)) {
            return $this->getInversionesPorMonto($matches[1], $messageLower);
        } else {
            return $this->getInversionesInfo();
        }
    }
    
    private function handlePagosQuery($message, $matches = [])
    {
        $messageLower = strtolower($message);
        
        if (preg_match('/(?:ayer|día anterior)/i', $message)) {
            return $this->getPagosAyer();
        } elseif (preg_match('/(?:hoy|día de hoy)/i', $message)) {
            return $this->getPagosHoy();
        } elseif (preg_match('/(?:mañana|siguiente día)/i', $message)) {
            return $this->getPagosMañana();
        } elseif (preg_match('/(?:semana|semanal)/i', $message)) {
            return $this->getPagosSemana();
        } elseif (preg_match('/(?:mes|mensual)/i', $message)) {
            return $this->getPagosMes();
        } elseif (preg_match('/(?:pendiente|pendientes|debe|deben)/i', $message)) {
            return $this->getPagosPendientes();
        } else {
            return $this->getPagosRecientes();
        }
    }
    
    private function handleBusquedaQuery($message, $matches = [])
    {
        // Intentar extraer términos de búsqueda
        if (preg_match('/(?:buscar|encontrar|ver).*?(?:cliente|persona).*?([a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s]+)/i', $message, $matches)) {
            return $this->buscarCliente(trim($matches[1]));
        } elseif (preg_match('/(?:dni|documento|cedula)\s*([0-9]+)/i', $message, $matches)) {
            return $this->buscarCliente($matches[1]);
        } elseif (preg_match('/(?:inversion|inversiones).*?([0-9a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+)/i', $message, $matches)) {
            return $this->buscarInversion(trim($matches[1]));
        } else {
            // Búsqueda general
            return $this->busquedaGeneral($message);
        }
    }
    
    private function handleEstadisticasQuery($message, $matches = [])
    {
        return $this->getEstadisticasGenerales();
    }
    
    // Nuevos métodos para mayor flexibilidad
    private function getGananciasDelMes()
    {
        try {
            $sql = "SELECT 
                        SUM(p.monto) as total_cobrado,
                        COUNT(p.id) as total_pagos,
                        COUNT(DISTINCT i.cliente_id) as clientes_activos
                    FROM pago p
                    INNER JOIN inversion i ON p.inversion_id = i.id
                    WHERE EXTRACT(MONTH FROM p.fecha) = EXTRACT(MONTH FROM CURRENT_DATE)
                    AND EXTRACT(YEAR FROM p.fecha) = EXTRACT(YEAR FROM CURRENT_DATE)
                    AND p.estado = true";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['total_cobrado'] > 0) {
                return "💰 **Ganancias del Mes Actual:**\n\n" .
                       "• **Total Cobrado:** " . number_format($result['total_cobrado'], 2) . "\n" .
                       "• **Pagos Recibidos:** {$result['total_pagos']}\n" .
                       "• **Clientes Activos:** {$result['clientes_activos']}\n\n" .
                       "📊 Excelente rendimiento este mes!";
            } else {
                return "📊 **Ganancias del Mes:** Aún no se han registrado pagos este mes.";
            }
        } catch (Exception $e) {
            return "Error al consultar ganancias del mes.";
        }
    }
    
    private function getInversionesPorMonto($monto, $messageLower)
    {
        try {
            $operator = strpos($messageLower, 'mayor') !== false || strpos($messageLower, 'superior') !== false ? '>=' : '<=';
            $operatorText = $operator === '>=' ? 'mayores o iguales' : 'menores o iguales';
            
            $sql = "SELECT 
                        i.id,
                        (c.datos_personales).nombres || ' ' || (c.datos_personales).apellido_paterno as cliente,
                        i.monto,
                        i.moneda,
                        i.plan_inversion,
                        i.estado
                    FROM inversion i
                    INNER JOIN cliente c ON i.cliente_id = c.id
                    WHERE i.monto {$operator} :monto AND i.estado != 'cancelado'
                    ORDER BY i.monto DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':monto' => $monto]);
            $inversiones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($inversiones)) {
                return "No se encontraron inversiones {$operatorText} a {$monto}.";
            }
            
            $response = "💼 **Inversiones {$operatorText} a {$monto}:**\n\n";
            foreach (array_slice($inversiones, 0, 10) as $inv) {
                $plan = $this->formatPlan($inv['plan_inversion']);
                $response .= "• **{$inv['cliente']}**: {$inv['monto']} {$inv['moneda']} - Plan {$plan}\n";
            }
            
            if (count($inversiones) > 10) {
                $response .= "\n*... y " . (count($inversiones) - 10) . " inversiones más*";
            }
            
            return $response;
        } catch (Exception $e) {
            return "Error al consultar inversiones por monto.";
        }
    }
    
    private function getPagosMañana()
    {
        try {
            $sql = "SELECT 
                        (c.datos_personales).nombres || ' ' || (c.datos_personales).apellido_paterno as cliente,
                        i.monto,
                        i.moneda,
                        p.monto as monto_pago,
                        p.numero_pago
                    FROM pago p
                    INNER JOIN inversion i ON p.inversion_id = i.id
                    INNER JOIN cliente c ON i.cliente_id = c.id
                    WHERE DATE(p.fecha) = CURRENT_DATE + INTERVAL '1 day'
                    ORDER BY p.monto DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($pagos)) {
                return "📅 **Pagos de Mañana:** No hay pagos programados para mañana.";
            }
            
            $response = "📅 **Pagos Programados para Mañana:**\n\n";
            $total = 0;
            foreach ($pagos as $pago) {
                $response .= "• **{$pago['cliente']}**: {$pago['monto_pago']} {$pago['moneda']} (Pago #{$pago['numero_pago']})\n";
                $total += $pago['monto_pago'];
            }
            
            $response .= "\n💰 **Total Esperado:** " . number_format($total, 2);
            return $response;
        } catch (Exception $e) {
            return "Error al consultar pagos de mañana.";
        }
    }
    
    private function getPagosSemana()
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_pagos,
                        SUM(p.monto) as total_monto,
                        COUNT(DISTINCT i.cliente_id) as clientes_distintos
                    FROM pago p
                    INNER JOIN inversion i ON p.inversion_id = i.id
                    WHERE p.fecha >= CURRENT_DATE - INTERVAL '7 days'
                    AND p.fecha <= CURRENT_DATE
                    AND p.estado = true";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return "📊 **Pagos de la Semana:**\n\n" .
                   "• **Total de Pagos:** {$result['total_pagos']}\n" .
                   "• **Monto Total:** " . number_format($result['total_monto'], 2) . "\n" .
                   "• **Clientes que Pagaron:** {$result['clientes_distintos']}";
        } catch (Exception $e) {
            return "Error al consultar pagos de la semana.";
        }
    }
    
    private function getPagosPendientes()
    {
        try {
            $sql = "SELECT 
                        (c.datos_personales).nombres || ' ' || (c.datos_personales).apellido_paterno as cliente,
                        i.monto as monto_inversion,
                        i.moneda,
                        p.monto as monto_pendiente,
                        p.fecha,
                        p.numero_pago,
                        CASE 
                            WHEN p.fecha < CURRENT_DATE THEN 'Vencido'
                            WHEN p.fecha = CURRENT_DATE THEN 'Vence Hoy'
                            ELSE 'Próximo'
                        END as estado_pago
                    FROM pago p
                    INNER JOIN inversion i ON p.inversion_id = i.id
                    INNER JOIN cliente c ON i.cliente_id = c.id
                    WHERE p.estado = false
                    ORDER BY p.fecha ASC
                    LIMIT 15";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($pagos)) {
                return "✅ **Pagos Pendientes:** ¡Excelente! No hay pagos pendientes.";
            }
            
            $response = "⏰ **Pagos Pendientes:**\n\n";
            $totalPendiente = 0;
            
            foreach ($pagos as $pago) {
                $icon = $pago['estado_pago'] === 'Vencido' ? '🔴' : 
                       ($pago['estado_pago'] === 'Vence Hoy' ? '🟡' : '🟢');
                
                $response .= "{$icon} **{$pago['cliente']}**: {$pago['monto_pendiente']} {$pago['moneda']} - {$pago['fecha']} ({$pago['estado_pago']})\n";
                $totalPendiente += $pago['monto_pendiente'];
            }
            
            $response .= "\n💰 **Total Pendiente:** " . number_format($totalPendiente, 2);
            return $response;
        } catch (Exception $e) {
            return "Error al consultar pagos pendientes.";
        }
    }
    
    private function buscarInversion($termino)
    {
        try {
            // Buscar por ID de inversión o por cliente
            if (is_numeric($termino)) {
                $sql = "SELECT 
                            i.*,
                            (c.datos_personales).nombres || ' ' || (c.datos_personales).apellido_paterno as cliente
                        FROM inversion i
                        INNER JOIN cliente c ON i.cliente_id = c.id
                        WHERE i.id = :termino";
            } else {
                $sql = "SELECT 
                            i.*,
                            (c.datos_personales).nombres || ' ' || (c.datos_personales).apellido_paterno as cliente
                        FROM inversion i
                        INNER JOIN cliente c ON i.cliente_id = c.id
                        WHERE LOWER((c.datos_personales).nombres || ' ' || (c.datos_personales).apellido_paterno) LIKE LOWER(:termino)";
                $termino = "%{$termino}%";
            }
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':termino' => $termino]);
            $inversiones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($inversiones)) {
                return "❌ No se encontraron inversiones con ese criterio.";
            }
            
            $response = "🔍 **Inversiones Encontradas:**\n\n";
            foreach (array_slice($inversiones, 0, 5) as $inv) {
                $plan = $this->formatPlan($inv['plan_inversion']);
                $response .= "• **{$inv['cliente']}** (ID: {$inv['id']})\n";
                $response .= "  💰 Monto: {$inv['monto']} {$inv['moneda']}\n";
                $response .= "  📅 Plan: {$plan} | Estado: {$inv['estado']}\n\n";
            }
            
            return $response;
        } catch (Exception $e) {
            return "Error al buscar inversiones.";
        }
    }
    
    private function busquedaGeneral($mensaje)
    {
        // Búsqueda inteligente general
        return "🔍 **Búsqueda General**\n\n" .
               "Intenta ser más específico:\n" .
               "• \"Buscar cliente Juan Pérez\"\n" .
               "• \"Cliente con DNI 12345678\"\n" .
               "• \"Inversiones mayores a 10000\"\n" .
               "• \"Pagos pendientes\"\n" .
               "• \"Ganancias del mes\"";
    }
    
    private function getEstadisticasGenerales()
    {
        try {
            $sql = "SELECT 
                        (SELECT COUNT(*) FROM cliente WHERE estado = true) as total_clientes,
                        (SELECT COUNT(*) FROM inversion WHERE estado != 'cancelado') as total_inversiones,
                        (SELECT SUM(monto) FROM inversion WHERE estado = 'activo') as capital_activo,
                        (SELECT COUNT(*) FROM pago WHERE estado = false) as pagos_pendientes,
                        (SELECT SUM(monto) FROM pago WHERE estado = true AND EXTRACT(MONTH FROM fecha) = EXTRACT(MONTH FROM CURRENT_DATE)) as cobrado_mes";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return "📊 **Estadísticas Generales del Sistema:**\n\n" .
                   "👥 **Clientes Activos:** {$stats['total_clientes']}\n" .
                   "💼 **Inversiones Activas:** {$stats['total_inversiones']}\n" .
                   "💰 **Capital en Juego:** " . number_format($stats['capital_activo'], 2) . "\n" .
                   "⏰ **Pagos Pendientes:** {$stats['pagos_pendientes']}\n" .
                   "📈 **Cobrado Este Mes:** " . number_format($stats['cobrado_mes'], 2) . "\n\n" .
                   "🚀 Sistema funcionando correctamente!";
        } catch (Exception $e) {
            return "Error al generar estadísticas generales.";
        }
    }
    
    private function getSmartDefaultResponse($message)
    {
        $aiStatus = $this->aiEnabled ? "🤖 **IA Activada**" : "⚠️ **IA No Disponible**";
        
        // Generar sugerencias basadas en el mensaje
        $suggestions = $this->generateSmartSuggestions($message);
        
        return "{$aiStatus} - Asistente del Sistema de Inversiones\n\n" .
               "No pude entender exactamente tu consulta. Aquí tienes algunas sugerencias:\n\n" .
               $suggestions . "\n\n" .
               "💡 **Tip:** Puedes preguntar en lenguaje natural. Por ejemplo:\n" .
               "• \"¿Cuánto dinero ganamos este mes?\"\n" .
               "• \"Muéstrame los clientes que deben pagar hoy\"\n" .
               "• \"Busca inversiones mayores a 50000\"";
    }
    
    private function generateSmartSuggestions($message)
    {
        $messageLower = strtolower($message);
        $suggestions = [];
        
        // Sugerencias basadas en palabras detectadas
        if (strpos($messageLower, 'cliente') !== false) {
            $suggestions[] = "• \"¿Cuántos clientes tengo?\"";
            $suggestions[] = "• \"Buscar cliente [nombre]\"";
        }
        
        if (strpos($messageLower, 'dinero') !== false || strpos($messageLower, 'plata') !== false) {
            $suggestions[] = "• \"Ganancias del mes\"";
            $suggestions[] = "• \"Total de inversiones activas\"";
        }
        
        if (strpos($messageLower, 'pago') !== false) {
            $suggestions[] = "• \"Pagos pendientes\"";
            $suggestions[] = "• \"Quién paga hoy\"";
        }
        
        if (empty($suggestions)) {
            $suggestions = [
                "• \"Estadísticas generales\"",
                "• \"Clientes activos\"", 
                "• \"Inversiones del mes\""
            ];
        }
        
        return implode("\n", $suggestions);
    }
    
    // Métodos de IA mejorados
    private function processWithAI($message)
    {
        try {
            if (!$this->aiEnabled || $this->deepSeekService === null) {
                return null;
            }
            
            // Agregar contexto de conversación previa
            $contextualMessage = $this->buildContextualMessage($message);
            
            $sqlQuery = $this->deepSeekService->generateSQLQuery(
                $contextualMessage, 
                $this->userRole, 
                $this->clienteId
            );
            
            if ($sqlQuery === 'NO_QUERY') {
                return null;
            }
            
            if ($sqlQuery === 'RESTRICTED_ACTION') {
                return "🚫 **Acción No Permitida**\n\nNo puedo realizar acciones de creación, modificación o eliminación de datos desde el chatbot.\n\n📋 **Para estas acciones, utiliza:**\n• Panel de administración del sistema\n• Sección correspondiente en la gestión\n• Formularios oficiales del sistema\n\n💡 **Puedo ayudarte con:**\n• Consultar información existente\n• Generar reportes\n• Buscar datos específicos\n• Estadísticas del sistema";
            }
            
            if (!$this->isValidSelectQuery($sqlQuery)) {
                return "🚫 **Consulta No Permitida**\n\nSolo puedo realizar consultas de información (SELECT). Para modificar datos, utiliza la gestión del sistema.";
            }
            
            $result = $this->executeAIQuery($sqlQuery);
            
            if (empty($result)) {
                return "📝 No se encontraron resultados para tu consulta. Intenta reformular la pregunta.";
            }
            
            return $this->formatAIResponse($result, $message);
            
        } catch (Exception $e) {
            error_log("Error en processWithAI: " . $e->getMessage());
            return null; // Permitir que continúe con otros métodos
        }
    }
    
    private function buildContextualMessage($message)
    {
        if (empty($this->contextHistory)) {
            return $message;
        }
        
        $context = "Contexto de conversación previa: " . implode(" -> ", array_slice($this->contextHistory, -3));
        return $context . "\n\nConsulta actual: " . $message;
    }
    
    private function isValidSelectQuery($query)
    {
        $query = trim($query);
        
        // Permitir consultas vacías o respuestas especiales
        if (empty($query) || in_array($query, ['NO_QUERY', 'RESTRICTED_ACTION'])) {
            return false;
        }
        
        $queryUpper = strtoupper($query);
        
        // Debe empezar con SELECT (permitir espacios y comentarios)
        if (!preg_match('/^\s*(--.*\n\s*)*SELECT\s+/i', $query)) {
            return false;
        }
        
        // Palabras prohibidas más específicas
        $prohibitedPatterns = [
            '/\b(INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|TRUNCATE|REPLACE)\s+/i',
            '/\b(GRANT|REVOKE|EXEC|EXECUTE|CALL|DO)\s+/i',
            '/\b(MERGE|UPSERT)\s+/i',
            '/;\s*(INSERT|UPDATE|DELETE|DROP|CREATE|ALTER)/i' // Múltiples statements
        ];
        
        foreach ($prohibitedPatterns as $pattern) {
            if (preg_match($pattern, $query)) {
                return false;
            }
        }
        
        return true;
    }
    
    private function executeAIQuery($sqlQuery)
    {
        try {
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error ejecutando consulta AI: " . $e->getMessage());
            error_log("Consulta: " . $sqlQuery);
            throw new Exception("Error al ejecutar la consulta");
        }
    }
    
    private function formatAIResponse($data, $originalMessage)
    {
        if (empty($data)) {
            return "No se encontraron resultados.";
        }
        
        // Si es un solo resultado numérico (como COUNT)
        if (count($data) === 1 && count($data[0]) === 1) {
            $value = array_values($data[0])[0];
            $key = array_keys($data[0])[0];
            
            if (is_numeric($value)) {
                return "📊 **{$value}** {$this->getContextualLabel($key, $originalMessage)}";
            }
        }
        
        $response = "🔍 **Resultados de tu consulta:**\n\n";
        
        // Limitar resultados para evitar respuestas muy largas
        $maxResults = 15;
        $dataToShow = array_slice($data, 0, $maxResults);
        
        foreach ($dataToShow as $index => $row) {
            $response .= "**" . ($index + 1) . ".** ";
            
            $formattedRow = [];
            foreach ($row as $key => $value) {
                if ($value !== null && $value !== '') {
                    $formattedValue = $this->formatValue($value, $key);
                    $formattedRow[] = "**{$this->formatColumnName($key)}:** {$formattedValue}";
                }
            }
            
            $response .= implode(" | ", $formattedRow) . "\n";
        }
        
        if (count($data) > $maxResults) {
            $remaining = count($data) - $maxResults;
            $response .= "\n*... y {$remaining} resultados más*";
        }
        
        return $response;
    }
    
    private function formatValue($value, $key)
    {
        // Formatear valores según el tipo de columna
        if (strpos($key, 'monto') !== false && is_numeric($value)) {
            return number_format($value, 2);
        }
        
        if (strpos($key, 'fecha') !== false) {
            return date('d/m/Y', strtotime($value));
        }
        
        if (strpos($key, 'tasa') !== false && is_numeric($value)) {
            return $value . '%';
        }
        
        return $value;
    }
    
    private function getContextualLabel($key, $message)
    {
        $messageLower = strtolower($message);
        
        if (strpos($messageLower, 'cliente') !== false) {
            return 'clientes encontrados';
        } elseif (strpos($messageLower, 'inversion') !== false) {
            return 'inversiones encontradas';
        } elseif (strpos($messageLower, 'pago') !== false) {
            return 'pagos encontrados';
        } elseif (strpos($messageLower, 'ganancia') !== false) {
            return 'en ganancias';
        }
        
        return 'resultados';
    }
    
    private function formatColumnName($columnName)
    {
        $translations = [
            'nombres' => 'Nombre',
            'apellido_paterno' => 'Apellido Paterno',
            'apellido_materno' => 'Apellido Materno',
            'dni' => 'DNI',
            'monto' => 'Monto',
            'fecha' => 'Fecha',
            'estado' => 'Estado',
            'plan_inversion' => 'Plan',
            'tasa_interes' => 'Tasa',
            'moneda' => 'Moneda',
            'celular1' => 'Teléfono',
            'total' => 'Total',
            'count' => 'Cantidad',
            'cliente' => 'Cliente',
            'numero_pago' => 'Pago #',
            'fecha_inicio' => 'Inicio',
            'fecha_vencimiento' => 'Vencimiento'
        ];
        
        return $translations[$columnName] ?? ucfirst(str_replace('_', ' ', $columnName));
    }

    // Métodos existentes mejorados
    private function getClientesInfo($tipo = null)
    {
        try {
            switch ($tipo) {
                case 'total':
                    $sql = "SELECT COUNT(*) as total FROM cliente WHERE estado = true";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    return "👥 Actualmente tenemos **{$result['total']} clientes** registrados en el sistema.";
                
                case 'recientes':
                    $sql = "SELECT 
                                (datos_personales).nombres || ' ' || (datos_personales).apellido_paterno || ' ' || (datos_personales).apellido_materno as nombre_completo,
                                (datos_personales).dni,
                                DATE(created_at) as fecha_registro
                            FROM cliente 
                            WHERE estado = true 
                            ORDER BY created_at DESC 
                            LIMIT 10";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute();
                    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (empty($clientes)) {
                        return "📋 No hay clientes registrados recientemente.";
                    }
                    
                    $response = "🆕 **Clientes Registrados Recientemente:**\n\n";
                    foreach ($clientes as $cliente) {
                        $fecha = $cliente['fecha_registro'] ? date('d/m/Y', strtotime($cliente['fecha_registro'])) : 'N/A';
                        $response .= "• **{$cliente['nombre_completo']}** (DNI: {$cliente['dni']}) - {$fecha}\n";
                    }
                    return $response;
                
                case 'lista':
                default:
                    $sql = "SELECT 
                                (datos_personales).nombres || ' ' || (datos_personales).apellido_paterno || ' ' || (datos_personales).apellido_materno as nombre_completo,
                                (datos_personales).dni,
                                (datos_personales).celular1,
                                estado_civil
                            FROM cliente 
                            WHERE estado = true 
                            ORDER BY (datos_personales).nombres ASC 
                            LIMIT 10";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute();
                    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (empty($clientes)) {
                        return "📋 No hay clientes registrados en el sistema.";
                    }
                    
                    $response = "👥 **Lista de Clientes:**\n\n";
                    foreach ($clientes as $cliente) {
                        $response .= "• **{$cliente['nombre_completo']}** (DNI: {$cliente['dni']})\n";
                        $response .= "  📞 {$cliente['celular1']} | Estado Civil: {$cliente['estado_civil']}\n\n";
                    }
                    return $response;
            }
        } catch (Exception $e) {
            return "❌ Error al consultar información de clientes.";
        }
    }

    private function getInversionesInfo($tipo = null)
    {
        try {
            switch ($tipo) {
                case 'total':
                    $sql = "SELECT COUNT(*) as total FROM inversion WHERE estado != 'cancelado'";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    return "💼 Hay **{$result['total']} inversiones** activas en el sistema.";
                
                case 'activas':
                    $sql = "SELECT 
                                i.id,
                                (c.datos_personales).nombres || ' ' || (c.datos_personales).apellido_paterno as cliente,
                                i.monto,
                                i.moneda,
                                i.plan_inversion,
                                i.tasa_interes,
                                i.fecha_inicio
                            FROM inversion i
                            INNER JOIN cliente c ON i.cliente_id = c.id
                            WHERE i.estado = 'activo'
                            ORDER BY i.monto DESC 
                            LIMIT 10";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute();
                    $inversiones = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (empty($inversiones)) {
                        return "📊 No hay inversiones activas en el sistema.";
                    }
                    
                    $response = "✅ **Inversiones Activas:**\n\n";
                    $totalCapital = 0;
                    foreach ($inversiones as $inv) {
                        $plan = $this->formatPlan($inv['plan_inversion']);
                        $fecha = date('d/m/Y', strtotime($inv['fecha_inicio']));
                        $response .= "• **{$inv['cliente']}**: {$inv['monto']} {$inv['moneda']} - Plan {$plan} ({$inv['tasa_interes']}%)\n";
                        $response .= "  📅 Inicio: {$fecha}\n\n";
                        $totalCapital += $inv['monto'];
                    }
                    
                    $response .= "💰 **Capital Total Activo:** " . number_format($totalCapital, 2);
                    return $response;
                
                case 'vencidas':
                    $sql = "SELECT 
                                i.id,
                                (c.datos_personales).nombres || ' ' || (c.datos_personales).apellido_paterno as cliente,
                                i.monto,
                                i.moneda,
                                i.fecha_vencimiento
                            FROM inversion i
                            INNER JOIN cliente c ON i.cliente_id = c.id
                            WHERE i.estado = 'vencido'
                            ORDER BY i.fecha_vencimiento DESC 
                            LIMIT 10";
                            
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute();
                    $inversiones = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (empty($inversiones)) {
                        return "✅ No hay inversiones vencidas. ¡Excelente!";
                    }
                    
                    $response = "⏰ **Inversiones Vencidas:**\n\n";
                    foreach ($inversiones as $inv) {
                        $fecha = date('d/m/Y', strtotime($inv['fecha_vencimiento']));
                        $response .= "• **{$inv['cliente']}**: {$inv['monto']} {$inv['moneda']} - Venció: {$fecha}\n";
                    }
                    return $response;
                
                default:
                    return $this->getInversionesInfo('activas');
            }
        } catch (Exception $e) {
            return "❌ Error al consultar información de inversiones.";
        }
    }

    private function buscarCliente($param)
    {
        try {
            if (!$param) {
                return "❓ Por favor especifica el nombre o DNI del cliente que buscas.";
            }
            
            if (is_numeric($param)) {
                $sql = "SELECT 
                            (datos_personales).nombres || ' ' || (datos_personales).apellido_paterno || ' ' || (datos_personales).apellido_materno as nombre_completo,
                            (datos_personales).dni,
                            (datos_personales).celular1,
                            (datos_personales).celular2,
                            estado_civil,
                            (SELECT COUNT(*) FROM inversion WHERE cliente_id = cliente.id AND estado != 'cancelado') as total_inversiones
                        FROM cliente 
                        WHERE (datos_personales).dni = :param AND estado = true";
            } else {
                $sql = "SELECT 
                            (datos_personales).nombres || ' ' || (datos_personales).apellido_paterno || ' ' || (datos_personales).apellido_materno as nombre_completo,
                            (datos_personales).dni,
                            (datos_personales).celular1,
                            (datos_personales).celular2,
                            estado_civil,
                            (SELECT COUNT(*) FROM inversion WHERE cliente_id = cliente.id AND estado != 'cancelado') as total_inversiones
                        FROM cliente 
                        WHERE LOWER((datos_personales).nombres || ' ' || (datos_personales).apellido_paterno || ' ' || (datos_personales).apellido_materno) LIKE LOWER(:param) 
                        AND estado = true";
                $param = "%{$param}%";
            }
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':param' => $param]);
            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($clientes)) {
                return "❌ No se encontró ningún cliente con esos datos.";
            }
            
            if (count($clientes) === 1) {
                $cliente = $clientes[0];
                return "👤 **Cliente Encontrado:**\n\n" .
                       "• **Nombre:** {$cliente['nombre_completo']}\n" .
                       "• **DNI:** {$cliente['dni']}\n" .
                       "• **Teléfono 1:** {$cliente['celular1']}\n" .
                       "• **Teléfono 2:** {$cliente['celular2']}\n" .
                       "• **Estado Civil:** {$cliente['estado_civil']}\n" .
                       "• **Inversiones Activas:** {$cliente['total_inversiones']}";
            } else {
                $response = "👥 **Clientes Encontrados ({count($clientes)}):**\n\n";
                foreach (array_slice($clientes, 0, 10) as $cliente) {
                    $response .= "• **{$cliente['nombre_completo']}** (DNI: {$cliente['dni']})\n";
                    $response .= "  📞 {$cliente['celular1']} | Inversiones: {$cliente['total_inversiones']}\n\n";
                }
                return $response;
            }
                   
        } catch (Exception $e) {
            return "❌ Error al buscar el cliente.";
        }
    }

    private function getPagosAyer()
    {
        try {
            $sql = "SELECT 
                        (c.datos_personales).nombres || ' ' || (c.datos_personales).apellido_paterno as cliente,
                        p.monto,
                        i.moneda,
                        p.numero_pago,
                        p.comprobante
                    FROM pago p
                    INNER JOIN inversion i ON p.inversion_id = i.id
                    INNER JOIN cliente c ON i.cliente_id = c.id
                    WHERE DATE(p.fecha) = CURRENT_DATE - INTERVAL '1 day'
                    AND p.estado = true
                    ORDER BY p.monto DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($pagos)) {
                return "📅 **Pagos de Ayer:** No se registraron pagos ayer.";
            }
            
            $response = "💰 **Pagos Recibidos Ayer:**\n\n";
            $total = 0;
            foreach ($pagos as $pago) {
                $response .= "✅ **{$pago['cliente']}**: {$pago['monto']} {$pago['moneda']} (Pago #{$pago['numero_pago']})\n";
                $total += $pago['monto'];
            }
            
            $response .= "\n💵 **Total Cobrado Ayer:** " . number_format($total, 2);
            return $response;
        } catch (Exception $e) {
            return "❌ Error al consultar pagos de ayer.";
        }
    }

    private function getPagosHoy()
    {
        try {
            $sql = "SELECT 
                        (c.datos_personales).nombres || ' ' || (c.datos_personales).apellido_paterno as cliente,
                        p.monto,
                        i.moneda,
                        p.numero_pago,
                        p.estado as pago_realizado
                    FROM pago p
                    INNER JOIN inversion i ON p.inversion_id = i.id
                    INNER JOIN cliente c ON i.cliente_id = c.id
                    WHERE DATE(p.fecha) = CURRENT_DATE
                    ORDER BY p.estado DESC, p.monto DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($pagos)) {
                return "📅 **Pagos de Hoy:** No hay pagos programados para hoy.";
            }
            
            $response = "📅 **Pagos de Hoy:**\n\n";
            $totalCobrado = 0;
            $totalPendiente = 0;
            
            foreach ($pagos as $pago) {
                $icon = $pago['pago_realizado'] ? '✅' : '⏰';
                $status = $pago['pago_realizado'] ? 'Cobrado' : 'Pendiente';
                
                $response .= "{$icon} **{$pago['cliente']}**: {$pago['monto']} {$pago['moneda']} ({$status})\n";
                
                if ($pago['pago_realizado']) {
                    $totalCobrado += $pago['monto'];
                } else {
                    $totalPendiente += $pago['monto'];
                }
            }
            
            $response .= "\n💰 **Cobrado Hoy:** " . number_format($totalCobrado, 2);
            $response .= "\n⏰ **Pendiente Hoy:** " . number_format($totalPendiente, 2);
            
            return $response;
        } catch (Exception $e) {
            return "❌ Error al consultar pagos de hoy.";
        }
    }
    
    private function getPagosMes()
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_pagos,
                        SUM(CASE WHEN p.estado = true THEN p.monto ELSE 0 END) as total_cobrado,
                        SUM(CASE WHEN p.estado = false THEN p.monto ELSE 0 END) as total_pendiente,
                        COUNT(DISTINCT i.cliente_id) as clientes_distintos
                    FROM pago p
                    INNER JOIN inversion i ON p.inversion_id = i.id
                    WHERE EXTRACT(MONTH FROM p.fecha) = EXTRACT(MONTH FROM CURRENT_DATE)
                    AND EXTRACT(YEAR FROM p.fecha) = EXTRACT(YEAR FROM CURRENT_DATE)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return "📊 **Resumen de Pagos del Mes:**\n\n" .
                   "• **Total de Pagos:** {$result['total_pagos']}\n" .
                   "• **Monto Cobrado:** " . number_format($result['total_cobrado'], 2) . "\n" .
                   "• **Monto Pendiente:** " . number_format($result['total_pendiente'], 2) . "\n" .
                   "• **Clientes Activos:** {$result['clientes_distintos']}\n\n" .
                   "📈 **Eficiencia:** " . round(($result['total_cobrado'] / ($result['total_cobrado'] + $result['total_pendiente'])) * 100, 1) . "% cobrado";
        } catch (Exception $e) {
            return "❌ Error al consultar pagos del mes.";
        }
    }
    
    private function getPagosRecientes()
    {
        try {
            $sql = "SELECT 
                        (c.datos_personales).nombres || ' ' || (c.datos_personales).apellido_paterno as cliente,
                        p.monto,
                        i.moneda,
                        p.fecha,
                        p.numero_pago
                    FROM pago p
                    INNER JOIN inversion i ON p.inversion_id = i.id
                    INNER JOIN cliente c ON i.cliente_id = c.id
                    WHERE p.estado = true
                    ORDER BY p.fecha DESC
                    LIMIT 10";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($pagos)) {
                return "📋 No hay pagos recientes registrados.";
            }
            
            $response = "🕐 **Pagos Recientes:**\n\n";
            foreach ($pagos as $pago) {
                $fecha = date('d/m/Y', strtotime($pago['fecha']));
                $response .= "✅ **{$pago['cliente']}**: {$pago['monto']} {$pago['moneda']} - {$fecha}\n";
            }
            
            return $response;
        } catch (Exception $e) {
            return "❌ Error al consultar pagos recientes.";
        }
    }

    private function formatPlan($plan)
    {
        switch ((int)$plan) {
            case 1: return "Mensual";
            case 3: return "Trimestral";
            case 6: return "Semestral";
            case 12: return "Anual";
            default: return "{$plan} meses";
        }
    }
}
?>