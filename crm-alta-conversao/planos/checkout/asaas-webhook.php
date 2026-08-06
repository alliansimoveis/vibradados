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

http_response_code(200);
echo 'ok';
