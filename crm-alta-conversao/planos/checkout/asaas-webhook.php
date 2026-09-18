<?php
/* ============================================================
   Webhook do Asaas — recebe eventos de cobrança/assinatura.
   Valida o token (header asaas-access-token) contra o config
   acima do public_html, registra em log e notifica por e-mail
   quando um pagamento é confirmado/recebido.
   ============================================================ */
date_default_timezone_set('America/Sao_Paulo');

$cfgPath = dirname($_SERVER['DOCUMENT_ROOT']) . '/asaas-config.php';
$cfg = is_file($cfgPath) ? require $cfgPath : [];

/* Validação do token do webhook */
$sent = $_SERVER['HTTP_ASAAS_ACCESS_TOKEN'] ?? '';
$expected = $cfg['webhookToken'] ?? '';
if (empty($expected) || !hash_equals($expected, $sent)) {
  http_response_code(401);
  echo 'unauthorized';
  exit;
}

$raw = file_get_contents('php://input');
$evt = json_decode($raw, true);
$event = $evt['event'] ?? 'UNKNOWN';

/* Log (acima do public_html) */
$logFile = dirname($_SERVER['DOCUMENT_ROOT']) . '/asaas-events.log';
$pay = $evt['payment'] ?? [];
$line = sprintf("[%s] %s | id=%s | status=%s | value=%s | sub=%s | ext=%s\n",
  date('Y-m-d H:i:s'), $event,
  $pay['id'] ?? '-', $pay['status'] ?? '-', $pay['value'] ?? '-',
  $pay['subscription'] ?? '-', $pay['externalReference'] ?? '-'
);
@file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);

/* Notifica a equipe: pagamentos e eventos que exigem atenção */
$paidEvents  = ['PAYMENT_CONFIRMED', 'PAYMENT_RECEIVED'];
$alertEvents = ['PAYMENT_OVERDUE', 'PAYMENT_REFUNDED', 'PAYMENT_CHARGEBACK_REQUESTED', 'PAYMENT_CREDIT_CARD_CAPTURE_REFUSED'];
if (!empty($cfg['notifyEmail']) && (in_array($event, $paidEvents, true) || in_array($event, $alertEvents, true))) {
  $valor = isset($pay['value']) ? number_format($pay['value'], 2, ',', '.') : '?';
  $tipo  = in_array($event, $paidEvents, true) ? 'Novo pagamento' : 'ATENCAO';
  $assunto = '[' . $tipo . '] CRM — R$ ' . $valor . ' · ' . $event . ' (' . ($pay['externalReference'] ?? '') . ')';
  $corpo = "Evento: $event\n"
         . 'Cobrança: ' . ($pay['id'] ?? '-') . "\n"
         . 'Assinatura: ' . ($pay['subscription'] ?? '-') . "\n"
         . 'Cliente: ' . ($pay['customer'] ?? '-') . "\n"
         . 'Valor: R$ ' . $valor . "\n"
         . 'Plano: ' . ($pay['externalReference'] ?? '-') . "\n"
         . 'Fatura: ' . ($pay['invoiceUrl'] ?? '-') . "\n";
  @mail($cfg['notifyEmail'], $assunto, $corpo, 'From: no-reply@vibradados.com.br');
}

/* Implementação (pagamento único): quando a 1ª mensalidade é confirmada, gera a cobrança avulsa da
   implementação no mesmo cliente. O valor vem do externalReference da assinatura ("crm-<plano>-i<valor>").
   Idempotente por assinatura (impl-cobradas.log). Se falhar, avisa a equipe para cobrar manualmente. */
if (in_array($event, $paidEvents, true) && !empty($pay['subscription']) && !empty($pay['customer']) && !empty($cfg['apiKey'])) {
  $apiBase = $cfg['apiBase'] ?? 'https://api.asaas.com/v3';
  $call = function ($method, $path, $body) use ($apiBase, $cfg) {
    $ch = curl_init($apiBase . $path);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true, CURLOPT_CUSTOMREQUEST => $method, CURLOPT_TIMEOUT => 30,
      CURLOPT_USERAGENT => 'VibraCRM/1.0 (vibradados.com.br)',
      CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'User-Agent: VibraCRM/1.0', 'access_token: ' . $cfg['apiKey']],
    ]);
    if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    $resp = curl_exec($ch); $http = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    return ['http' => $http, 'data' => json_decode($resp, true)];
  };
  $ext = $pay['externalReference'] ?? '';
  if (!preg_match('/^crm-[a-z]+-i(\d+)$/', $ext)) {
    $sub = $call('GET', '/subscriptions/' . rawurlencode($pay['subscription']), null);
    $ext = $sub['data']['externalReference'] ?? '';
  }
  if (preg_match('/^crm-[a-z]+-i(\d+)$/', $ext, $m) && in_array((int)$m[1], [600, 1200], true)) {
    $flag = dirname($_SERVER['DOCUMENT_ROOT']) . '/impl-cobradas.log';
    $ja = is_file($flag) && strpos(file_get_contents($flag), '|' . $pay['subscription'] . '|') !== false;
    if (!$ja) {
      @file_put_contents($flag, '|' . $pay['subscription'] . '|' . date('c') . "\n", FILE_APPEND | LOCK_EX);
      $r = $call('POST', '/payments', [
        'customer' => $pay['customer'], 'billingType' => 'UNDEFINED', 'value' => (int)$m[1],
        'dueDate' => date('Y-m-d', strtotime('+3 days')),
        'description' => 'Implementação do CRM de Alta Conversão (pagamento único)',
        'externalReference' => 'impl-' . $pay['subscription'],
      ]);
      $ok = $r['http'] >= 200 && $r['http'] < 300;
      @file_put_contents($logFile, sprintf("[%s] IMPL | sub=%s | value=%s | http=%s | %s\n", date('Y-m-d H:i:s'), $pay['subscription'], $m[1], $r['http'], $ok ? ($r['data']['id'] ?? 'ok') : json_encode($r['data'])), FILE_APPEND | LOCK_EX);
      if (!empty($cfg['notifyEmail'])) {
        @mail($cfg['notifyEmail'], ($ok ? '[Implementação cobrada]' : '[ATENCAO] Falha ao cobrar implementação') . ' R$ ' . $m[1] . ' · ' . $pay['customer'],
          "Assinatura: {$pay['subscription']}\nCliente: {$pay['customer']}\nValor: R$ {$m[1]}\n" . ($ok ? 'Link: ' . ($r['data']['invoiceUrl'] ?? '-') : 'Erro: ' . json_encode($r['data']) . "\nCriar a cobrança manualmente no Asaas."),
          'From: no-reply@vibradados.com.br');
      }
    }
  }
}

http_response_code(200);
echo 'ok';
