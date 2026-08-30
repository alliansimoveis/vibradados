<?php
/**
 * Candidatura — Closer de Vendas (PJ)
 * Salva a candidatura em CSV (fora do public_html) e notifica selecao@vibradados.com.br.
 */

declare(strict_types=1);

$LOG_FILE = '/home/vibradadoscombr/candidaturas-closer-vendas.csv';
$NOTIFY_TO = 'selecao@vibradados.com.br';

function wantsJson(): bool {
  return isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
}

function respond(bool $ok, string $error = ''): void {
  if (wantsJson()) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['ok' => $ok, 'error' => $error], JSON_UNESCAPED_UNICODE);
    exit;
  }
  if ($ok) {
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">'
       . '<title>Candidatura recebida | Vibra Marketing</title>'
       . '<link rel="stylesheet" href="/styles.css?v=12"><link rel="stylesheet" href="vaga.css?v=1"></head>'
       . '<body class="js"><div class="vg-shell"><div class="vg-card"><div class="vg-success" style="display:block">'
       . '<h2>Recebemos sua candidatura!</h2>'
       . '<p>Se seu perfil for selecionado, entraremos em contato pelo e-mail ou telefone informado.</p>'
       . '</div></div></div></body></html>';
    exit;
  }
  http_response_code(400);
  echo 'Não foi possível enviar sua candidatura: ' . htmlspecialchars($error);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  respond(false, 'Método inválido.');
}

// honeypot — bots that fill hidden fields get a fake success, silently dropped
if (!empty($_POST['website'])) {
  respond(true);
}

function clean(string $v): string {
  return trim(preg_replace('/[\r\n\t]+/', ' ', strip_tags($v)));
}

$nome       = clean($_POST['nome'] ?? '');
$email      = trim($_POST['email'] ?? '');
$telefone   = clean($_POST['telefone'] ?? '');
$cidade     = clean($_POST['cidade'] ?? '');
$ocupacao   = clean($_POST['ocupacao'] ?? '');
$experiencia = clean($_POST['experiencia'] ?? '');
$closing    = clean($_POST['closing'] ?? '');
$crm        = clean($_POST['crm'] ?? '');
$ticket     = clean($_POST['ticket'] ?? '');
$situacional = trim(strip_tags($_POST['situacional'] ?? ''));
$curriculo  = clean($_POST['curriculo'] ?? '');
$ciente     = isset($_POST['ciente']) && $_POST['ciente'] !== '';

$turnosArr = $_POST['turno'] ?? [];
if (!is_array($turnosArr)) { $turnosArr = []; }
$turnosArr = array_map('clean', $turnosArr);
$turnos = implode('; ', array_filter($turnosArr));

$expOpcoes = ['Menos de 1 ano', '1 a 2 anos', '3 a 5 anos', 'Mais de 5 anos'];

$erros = [];
if ($nome === '' || mb_strlen($nome) < 3) { $erros[] = 'nome'; }
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $erros[] = 'email'; }
if ($telefone === '') { $erros[] = 'telefone'; }
if ($cidade === '') { $erros[] = 'cidade'; }
if ($ocupacao === '') { $erros[] = 'ocupação'; }
if (!in_array($experiencia, $expOpcoes, true)) { $erros[] = 'experiência'; }
if (!in_array($closing, ['Sim', 'Não'], true)) { $erros[] = 'closing'; }
if ($ticket === '') { $erros[] = 'ticket médio'; }
if (mb_strlen($situacional) < 60) { $erros[] = 'resposta situacional'; }
if ($turnos === '') { $erros[] = 'turno'; }
if (!$ciente) { $erros[] = 'confirmação PJ/presencial'; }

if (!empty($erros)) {
  respond(false, 'Campos inválidos ou incompletos: ' . implode(', ', $erros) . '.');
}

$dataHora = date('d/m/Y H:i:s');

// ---- salva no CSV (fora do public_html) ----
$isNew = !file_exists($LOG_FILE);
$fh = @fopen($LOG_FILE, 'a');
if ($fh) {
  if (flock($fh, LOCK_EX)) {
    if ($isNew) {
      fputcsv($fh, [
        'Nome', 'E-mail', 'Telefone', 'Cidade', 'Emprego atual', 'Experiência em vendas',
        'Já fez closing', 'CRM usado', 'Ticket médio', 'Resposta situacional',
        'Turnos disponíveis', 'Data da candidatura', 'Link do currículo',
      ]);
    }
    fputcsv($fh, [
      $nome, $email, $telefone, $cidade, $ocupacao, $experiencia,
      $closing, $crm, $ticket, $situacional,
      $turnos, $dataHora, $curriculo,
    ]);
    flock($fh, LOCK_UN);
  }
  fclose($fh);
}

// ---- notifica por e-mail ----
$assunto = '=?UTF-8?B?' . base64_encode('Nova candidatura - Closer de Vendas (PJ): ' . $nome) . '?=';

$corpo = "Nova candidatura recebida para Closer de Vendas (PJ) — Itajaí/SC\n";
$corpo .= "Data: {$dataHora}\n\n";
$corpo .= "Nome completo: {$nome}\n";
$corpo .= "E-mail: {$email}\n";
$corpo .= "Telefone/WhatsApp: {$telefone}\n";
$corpo .= "Cidade: {$cidade}\n\n";
$corpo .= "Ocupação atual/recente: {$ocupacao}\n";
$corpo .= "Experiência em vendas: {$experiencia}\n";
$corpo .= "Já fez closing: {$closing}\n";
$corpo .= "CRM usado: " . ($crm !== '' ? $crm : '(não informado)') . "\n";
$corpo .= "Maior ticket médio: {$ticket}\n\n";
$corpo .= "Resposta situacional:\n{$situacional}\n\n";
$corpo .= "Turnos disponíveis: {$turnos}\n";
$corpo .= "Currículo/LinkedIn: " . ($curriculo !== '' ? $curriculo : '(não informado)') . "\n";
$corpo .= "Ciente do regime PJ/presencial: Sim\n";

$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "From: Vagas Vibra Marketing <no-reply@vibradados.com.br>\r\n";
$headers .= "Reply-To: {$nome} <{$email}>\r\n";

@mail($NOTIFY_TO, $assunto, $corpo, $headers);

respond(true);
