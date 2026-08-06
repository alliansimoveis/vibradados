<?php
/* ============================================================
   CRM de Alta Conversão — Checkout (Asaas, assinatura cartão)
   Coleta os dados do cliente, cria Cliente + Assinatura via API
   e redireciona para o invoiceUrl (página segura do Asaas) onde
   o cartão é informado e tokenizado para a recorrência mensal.
   A API key NUNCA fica aqui — vem do config acima do public_html.
   ============================================================ */
date_default_timezone_set('America/Sao_Paulo');

/* Dados dos planos (público — não é segredo) */
$PLANOS = [
  'avancado' => ['nome'=>'Avançado','valor'=>150.00,'usuarios'=>'Até 3 usuários',
                 'desc'=>'CRM de Alta Conversão — Plano Avançado (até 3 usuários)'],
  'pro'      => ['nome'=>'Pro','valor'=>250.00,'usuarios'=>'Usuários ilimitados',
                 'desc'=>'CRM de Alta Conversão — Plano Pro (usuários ilimitados)'],
];

/* Config com segredos (apiKey, webhookToken) — acima do public_html */
$cfgPath = dirname($_SERVER['DOCUMENT_ROOT']) . '/asaas-config.php';
$cfg = is_file($cfgPath) ? require $cfgPath : [];
$apiBase = $cfg['apiBase'] ?? 'https://api.asaas.com/v3';

/* Qual plano */
$key = preg_replace('/[^a-z]/', '', $_GET['plano'] ?? $_POST['plano'] ?? '');
$plano = $PLANOS[$key] ?? null;

/* Sem plano válido → volta pra página de planos */
if (!$plano) { header('Location: /crm-alta-conversao/planos/'); exit; }

function asaas($method, $path, $body, $apiBase, $apiKey) {
  $ch = curl_init($apiBase . $path);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST  => $method,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'access_token: ' . $apiKey],
  ]);
  if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
  $resp = curl_exec($ch);
  $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  return ['http' => $http, 'data' => json_decode($resp, true)];
}
function asaasErr($r) {
  return $r['data']['errors'][0]['description'] ?? 'Não foi possível processar agora. Tente novamente em instantes.';
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome  = trim($_POST['nome'] ?? '');
  $cpf   = preg_replace('/\D/', '', $_POST['cpfCnpj'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $fone  = preg_replace('/\D/', '', $_POST['celular'] ?? '');

  if (mb_strlen($nome) < 3 || (strlen($cpf) !== 11 && strlen($cpf) !== 14)
      || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($fone) < 10) {
    $erro = 'Confira os dados: nome completo, CPF ou CNPJ válido, e-mail e celular com DDD.';
  } elseif (empty($cfg['apiKey']) || strpos($cfg['apiKey'], 'COLOQUE') !== false) {
    $erro = 'O pagamento ainda está sendo configurado. Fale com a Vibra pelo WhatsApp.';
  } else {
    $apiKey = $cfg['apiKey'];
    $cli = asaas('POST', '/customers', [
      'name' => $nome, 'cpfCnpj' => $cpf, 'email' => $email, 'mobilePhone' => $fone,
      'externalReference' => 'crm-' . $key,
    ], $apiBase, $apiKey);

    if ($cli['http'] >= 200 && $cli['http'] < 300 && !empty($cli['data']['id'])) {
      $base = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . '://' . $_SERVER['HTTP_HOST'];
      $sub = asaas('POST', '/subscriptions', [
        'customer'     => $cli['data']['id'],
        'billingType'  => 'CREDIT_CARD',
        'value'        => $plano['valor'],
        'nextDueDate'  => date('Y-m-d'),
        'cycle'        => 'MONTHLY',
        'description'  => $plano['desc'],
        'externalReference' => 'crm-' . $key,
        'callback'     => ['successUrl' => $base . '/crm-alta-conversao/planos/checkout/obrigado.php', 'autoRedirect' => true],
      ], $apiBase, $apiKey);

      if ($sub['http'] >= 200 && $sub['http'] < 300 && !empty($sub['data']['id'])) {
        $pay = asaas('GET', '/subscriptions/' . $sub['data']['id'] . '/payments', null, $apiBase, $apiKey);
        $invoice = $pay['data']['data'][0]['invoiceUrl'] ?? '';
        if ($invoice) { header('Location: ' . $invoice); exit; }
        $erro = 'Assinatura criada, mas não recebemos o link de pagamento. Fale com a Vibra.';
      } else {
        $erro = asaasErr($sub);
      }
    } else {
      $erro = asaasErr($cli);
    }
  }
}

$brl = function ($v) { return number_format($v, 0, ',', '.'); };
$e   = function ($s) { return htmlspecialchars($s !== null ? $s : '', ENT_QUOTES, 'UTF-8'); };
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#050505" />
  <meta name="robots" content="noindex, nofollow" />
  <title>Checkout · Plano <?= $e($plano['nome']) ?> — CRM de Alta Conversão</title>
  <link rel="icon" href="/assets/favicon.svg" type="image/svg+xml" />
  <link rel="preconnect" href="https://api.fontshare.com" crossorigin />
  <link rel="preconnect" href="https://cdn.fontshare.com" crossorigin />
  <link href="https://api.fontshare.com/v2/css?f[]=clash-display@600,700,500,400&f[]=general-sans@400,500,600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/styles.css?v=8" />
  <link rel="stylesheet" href="/crm-alta-conversao/planos/checkout/checkout.css?v=1" />
</head>
<body>
  <div class="bg-grain" aria-hidden="true"></div>
  <div class="bg-aura" aria-hidden="true"><div class="aura aura--1"></div><div class="aura aura--2"></div></div>
  <div class="bg-grid" aria-hidden="true"></div>

  <header class="nav" style="position:relative;padding-top:22px;padding-bottom:0">
    <a class="nav__brand" href="/crm-alta-conversao/planos/" aria-label="Vibra Marketing">
      <img class="nav__logo" src="/assets/vibra-logo-white.png" alt="Vibra Marketing" style="height:34px" />
    </a>
  </header>

  <main class="ck-wrap">
    <div class="ck-eyebrow"><span class="dot"></span> Checkout · CRM de Alta Conversão</div>
    <h1 class="ck-title">Você está a um passo de ativar o <span class="grad">Plano <?= $e($plano['nome']) ?>.</span></h1>

    <div class="ck-grid">
      <!-- FORM -->
      <div class="ck-form">
        <h2>Seus dados</h2>
        <p class="sub">Preencha para criarmos sua assinatura. No próximo passo você informa o cartão na página segura do Asaas.</p>

        <?php if ($erro): ?><div class="ck-error"><b>Ops.</b> <?= $e($erro) ?></div><?php endif; ?>

        <form method="post" autocomplete="on">
          <input type="hidden" name="plano" value="<?= $e($key) ?>" />
          <div class="ck-field">
            <label for="nome">Nome completo</label>
            <input id="nome" name="nome" type="text" required placeholder="Como no documento" value="<?= $e($_POST['nome'] ?? '') ?>" />
          </div>
          <div class="ck-field">
            <label for="cpfCnpj">CPF ou CNPJ</label>
            <input id="cpfCnpj" name="cpfCnpj" type="text" inputmode="numeric" required placeholder="Somente números" value="<?= $e($_POST['cpfCnpj'] ?? '') ?>" />
          </div>
          <div class="ck-row">
            <div class="ck-field">
              <label for="email">E-mail</label>
              <input id="email" name="email" type="email" required placeholder="voce@empresa.com" value="<?= $e($_POST['email'] ?? '') ?>" />
            </div>
            <div class="ck-field">
              <label for="celular">Celular (com DDD)</label>
              <input id="celular" name="celular" type="tel" inputmode="numeric" required placeholder="(47) 99999-9999" value="<?= $e($_POST['celular'] ?? '') ?>" />
            </div>
          </div>
          <button type="submit" class="btn btn--primary ck-submit" data-magnetic>
            <span>Ir para o pagamento</span>
            <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
          <div class="ck-secure">
            <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <span>Pagamento processado pelo Asaas. Os dados do cartão são digitados na página segura do Asaas — não passam pelo nosso servidor.</span>
          </div>
        </form>
      </div>

      <!-- RESUMO -->
      <aside class="ck-summary">
        <h2>Plano <?= $e($plano['nome']) ?></h2>
        <span class="plan-tag"><?= $e($plano['usuarios']) ?></span>
        <div class="ck-price"><span class="cur">R$</span><span class="val"><?= $brl($plano['valor']) ?></span><span class="per">/mês</span></div>
        <p class="ck-cycle">Cobrança mensal no cartão · <b>contrato de 12 meses</b></p>
        <ul>
          <li><svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>CRM completo: funil, WhatsApp e automações</li>
          <li><svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>Meta CAPI + Google Ads via API + GA4 (server-side)</li>
          <li><svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>Conversão por etapa do funil enviada a Meta e Google</li>
          <li><svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>Configuração e operação completas pela Vibra</li>
        </ul>
        <p class="switch">Plano errado? <a href="/crm-alta-conversao/planos/">Ver os dois planos</a></p>
      </aside>
    </div>
  </main>

  <footer class="ck-footer">
    <img src="/assets/vibra-logo-white.png" alt="Vibra Marketing" />
    <span>© 2026 Vibra Marketing · CRM de Alta Conversão</span>
  </footer>
</body>
</html>
