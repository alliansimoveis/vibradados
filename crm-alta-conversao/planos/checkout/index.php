<?php
/* ============================================================
   CRM de Alta Conversão — Checkout (Asaas, assinatura cartão)
   + Contrato de assinatura com aceite eletrônico.
   Cria Cliente + Assinatura via API e redireciona ao invoiceUrl
   (cartão informado na página segura do Asaas). API key vem do
   config acima do public_html. Cartão nunca trafega no servidor.
   ============================================================ */
date_default_timezone_set('America/Sao_Paulo');

/* Planos (público) */
$PLANOS = [
  'avancado' => ['nome'=>'Avançado','valor'=>150.00,'usuarios'=>'Até 3 usuários',
                 'desc'=>'CRM de Alta Conversão — Plano Avançado (até 3 usuários)'],
  'pro'      => ['nome'=>'Pro','valor'=>250.00,'usuarios'=>'Usuários ilimitados',
                 'desc'=>'CRM de Alta Conversão — Plano Pro (usuários ilimitados)'],
];
$MULTA_PCT = 50; // % da multa rescisória sobre as mensalidades vincendas (proporcional aos meses restantes)

/* Config com segredos — acima do public_html */
$cfgPath = dirname($_SERVER['DOCUMENT_ROOT']) . '/asaas-config.php';
$cfg = is_file($cfgPath) ? require $cfgPath : [];
$apiBase = $cfg['apiBase'] ?? 'https://api.asaas.com/v3';

$key = preg_replace('/[^a-z]/', '', $_GET['plano'] ?? $_POST['plano'] ?? '');
$plano = $PLANOS[$key] ?? null;
if (!$plano) { header('Location: /crm-alta-conversao/planos/'); exit; }
$anual = $plano['valor'] * 12;

/* ---------- validadores CPF/CNPJ ---------- */
function validaCPF($cpf){
  $cpf = preg_replace('/\D/','',$cpf);
  if (strlen($cpf) != 11 || preg_match('/^(\d)\1{10}$/',$cpf)) return false;
  for ($t=9;$t<11;$t++){ for($d=0,$c=0;$c<$t;$c++)$d+=$cpf[$c]*(($t+1)-$c); $d=((10*$d)%11)%10; if($cpf[$c]!=$d) return false; }
  return true;
}
function validaCNPJ($cnpj){
  $cnpj = preg_replace('/\D/','',$cnpj);
  if (strlen($cnpj) != 14 || preg_match('/^(\d)\1{13}$/',$cnpj)) return false;
  $b=[5,4,3,2,9,8,7,6,5,4,3,2];
  for($i=0,$n=0;$i<12;$i++)$n+=$cnpj[$i]*$b[$i]; $r=$n%11; if($cnpj[12]!=(($r<2)?0:11-$r)) return false;
  array_unshift($b,6);
  for($i=0,$n=0;$i<13;$i++)$n+=$cnpj[$i]*$b[$i]; $r=$n%11; return $cnpj[13]==(($r<2)?0:11-$r);
}
function docValido($v){ $v=preg_replace('/\D/','',$v); return strlen($v)==11 ? validaCPF($v) : (strlen($v)==14 ? validaCNPJ($v) : false); }

function asaas($method, $path, $body, $apiBase, $apiKey) {
  $ch = curl_init($apiBase . $path);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST  => $method,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_USERAGENT      => 'VibraCRM/1.0 (vibradados.com.br)',
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'User-Agent: VibraCRM/1.0', 'access_token: ' . $apiKey],
  ]);
  if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
  $resp = curl_exec($ch); $http = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
  return ['http' => $http, 'data' => json_decode($resp, true)];
}
function asaasErr($r){ return $r['data']['errors'][0]['description'] ?? 'Não foi possível processar agora. Tente novamente em instantes.'; }

$erro = '';
$in = function($k){ return trim($_POST[$k] ?? ''); };

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome  = $in('nome');
  $doc   = preg_replace('/\D/','',$in('cpfCnpj'));
  $email = $in('email');
  $fone  = preg_replace('/\D/','',$in('celular'));
  $cep   = preg_replace('/\D/','',$in('cep'));
  $logr  = $in('logradouro'); $num = $in('numero'); $compl = $in('complemento');
  $bairro= $in('bairro'); $cidade = $in('cidade'); $uf = strtoupper($in('uf'));
  $aceite= isset($_POST['aceite']);

  if (mb_strlen($nome) < 3)                     $erro = 'Informe o nome completo ou a razão social.';
  elseif (!docValido($doc))                     $erro = 'CPF ou CNPJ inválido. Confira os números.';
  elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erro = 'E-mail inválido.';
  elseif (strlen($fone) < 10)                   $erro = 'Informe um celular válido com DDD.';
  elseif (strlen($cep) != 8 || $logr==='' || $num==='' || $bairro==='' || $cidade==='' || strlen($uf)!=2)
                                                $erro = 'Preencha o endereço completo (CEP, logradouro, número, bairro, cidade e UF).';
  elseif (!$aceite)                             $erro = 'É necessário ler e aceitar o contrato para continuar.';
  elseif (empty($cfg['apiKey']) || strpos($cfg['apiKey'],'COLOQUE')!==false)
                                                $erro = 'O pagamento ainda está sendo configurado. Fale com a Vibra pelo WhatsApp.';
  else {
    $apiKey = $cfg['apiKey'];
    // registro de aceite do contrato (acima do public_html)
    @file_put_contents(dirname($_SERVER['DOCUMENT_ROOT']).'/contratos-aceites.log',
      sprintf("[%s] %s | doc=%s | %s | %s | %s/%s | IP=%s | plano=%s | mensal=%.2f | anual=%.2f | multa=%d%% | aceite=SIM\n",
        date('Y-m-d H:i:s'),$nome,$doc,$email,$fone,$cidade,$uf,($_SERVER['REMOTE_ADDR']??'?'),$plano['nome'],$plano['valor'],$anual,$MULTA_PCT),
      FILE_APPEND|LOCK_EX);

    // Asaas Checkout — SOMENTE cartao de credito (sem debito) + assinatura recorrente mensal.
    $host = (empty($_SERVER['HTTPS'])?'http':'https').'://'.$_SERVER['HTTP_HOST'];
    $base = $host.'/crm-alta-conversao/planos/checkout';
    $chk = asaas('POST','/checkouts',[
      'billingTypes'    => ['CREDIT_CARD'],
      'chargeTypes'     => ['RECURRENT'],
      'minutesToExpire' => 60,
      'externalReference' => 'crm-'.$key,
      'callback' => [
        'successUrl' => $base.'/obrigado.php',
        'cancelUrl'  => $base.'/?plano='.$key,
        'expiredUrl' => $host.'/crm-alta-conversao/planos/',
      ],
      'items' => [[
        'name'        => 'Plano '.$plano['nome'].' - CRM Vibra',
        'description' => $plano['desc'].' (assinatura mensal)',
        'quantity'    => 1,
        'value'       => $plano['valor'],
      ]],
      'customerData' => [
        'name'=>$nome,'cpfCnpj'=>$doc,'email'=>$email,'phone'=>$fone,
        'postalCode'=>$cep,'address'=>$logr,'addressNumber'=>$num,'complement'=>$compl,'province'=>$bairro,
      ],
      'subscription' => ['cycle'=>'MONTHLY','nextDueDate'=>date('Y-m-d')],
    ],$apiBase,$apiKey);

    if ($chk['http']>=200 && $chk['http']<300 && !empty($chk['data']['link'])) {
      header('Location: '.$chk['data']['link']); exit;
    } else $erro = asaasErr($chk);
  }
}

$brl2 = function($v){ return number_format($v,2,',','.'); };
$brl0 = function($v){ return number_format($v,0,',','.'); };
$e    = function($s){ return htmlspecialchars($s!==null?$s:'', ENT_QUOTES, 'UTF-8'); };
$V = function($k){ return htmlspecialchars($_POST[$k] ?? '', ENT_QUOTES, 'UTF-8'); };
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
  <link rel="stylesheet" href="/crm-alta-conversao/planos/checkout/checkout.css?v=4" />
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
        <p class="sub">Preencha para gerarmos seu contrato e sua assinatura. No próximo passo você informa o cartão na página segura do Asaas.</p>

        <?php if ($erro): ?><div class="ck-error"><b>Ops.</b> <?= $e($erro) ?></div><?php endif; ?>

        <form method="post" autocomplete="on" id="ckForm" novalidate>
          <input type="hidden" name="plano" value="<?= $e($key) ?>" />

          <div class="ck-field">
            <label for="nome">Nome completo / Razão social</label>
            <input id="nome" name="nome" type="text" required placeholder="Como no documento" value="<?= $V('nome') ?>" />
          </div>
          <div class="ck-field">
            <label for="cpfCnpj">CPF ou CNPJ</label>
            <input id="cpfCnpj" name="cpfCnpj" type="text" inputmode="numeric" required placeholder="Somente números" value="<?= $V('cpfCnpj') ?>" />
            <small class="ck-hint" id="docHint"></small>
          </div>
          <div class="ck-row">
            <div class="ck-field">
              <label for="email">E-mail</label>
              <input id="email" name="email" type="email" required placeholder="voce@empresa.com" value="<?= $V('email') ?>" />
            </div>
            <div class="ck-field">
              <label for="celular">Celular (com DDD)</label>
              <input id="celular" name="celular" type="tel" inputmode="numeric" required placeholder="(47) 99999-9999" value="<?= $V('celular') ?>" />
            </div>
          </div>

          <p class="ck-sec">Endereço</p>
          <div class="ck-row ck-row--cep">
            <div class="ck-field">
              <label for="cep">CEP</label>
              <input id="cep" name="cep" type="text" inputmode="numeric" required placeholder="00000-000" value="<?= $V('cep') ?>" />
            </div>
            <div class="ck-field">
              <label for="cidade">Cidade</label>
              <input id="cidade" name="cidade" type="text" required placeholder="Cidade" value="<?= $V('cidade') ?>" />
            </div>
            <div class="ck-field ck-uf">
              <label for="uf">UF</label>
              <input id="uf" name="uf" type="text" maxlength="2" required placeholder="SC" value="<?= $V('uf') ?>" />
            </div>
          </div>
          <div class="ck-field">
            <label for="logradouro">Logradouro</label>
            <input id="logradouro" name="logradouro" type="text" required placeholder="Rua / Avenida" value="<?= $V('logradouro') ?>" />
          </div>
          <div class="ck-row">
            <div class="ck-field ck-num">
              <label for="numero">Número</label>
              <input id="numero" name="numero" type="text" required placeholder="Nº" value="<?= $V('numero') ?>" />
            </div>
            <div class="ck-field">
              <label for="complemento">Complemento <span class="opt">(opcional)</span></label>
              <input id="complemento" name="complemento" type="text" placeholder="Sala, andar…" value="<?= $V('complemento') ?>" />
            </div>
          </div>
          <div class="ck-field">
            <label for="bairro">Bairro</label>
            <input id="bairro" name="bairro" type="text" required placeholder="Bairro" value="<?= $V('bairro') ?>" />
          </div>

          <div class="ck-accept">
            <label class="ck-check">
              <input type="checkbox" name="aceite" id="aceite" required <?= isset($_POST['aceite'])?'checked':'' ?> />
              <span>Li e aceito o <button type="button" class="ck-link" id="abrirContrato">Contrato de Assinatura e os Termos de Uso</button>.</span>
            </label>
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
        <div class="ck-price"><span class="cur">R$</span><span class="val"><?= $brl0($plano['valor']) ?></span><span class="per">/mês</span></div>
        <p class="ck-cycle">Cobrança mensal no cartão · <b>contrato de 12 meses</b><br>Total no período: <b>R$ <?= $brl2($anual) ?></b></p>
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

  <!-- ============ MODAL DO CONTRATO ============ -->
  <div id="contrato-modal" class="ct-modal" aria-hidden="true">
    <div class="ct-backdrop" data-close></div>
    <div class="ct-dialog" role="dialog" aria-modal="true" aria-label="Contrato de Assinatura">
      <div class="ct-actions">
        <button type="button" class="btn btn--line" id="baixarPdf"><span>Baixar PDF</span></button>
        <button type="button" class="btn btn--primary" id="aceitarFechar"><span>Li e aceito</span></button>
        <button type="button" class="ct-x" data-close aria-label="Fechar">&times;</button>
      </div>
      <div class="ct-paper">
        <h1 class="ct-h1">CONTRATO DE ASSINATURA DE PLATAFORMA (CRM) E PRESTAÇÃO DE SERVIÇOS</h1>
        <p class="ct-meta">Plano <span id="ct-plano"></span> · Versão 1 · Gerado em <span id="ct-data"></span></p>

        <p><b>CONTRATADA:</b> ALIERE CURADORIA IMOBILIARIA LTDA, sociedade empresária limitada inscrita no CNPJ sob o nº 67.242.488/0001-34, com sede na Rua Alberto Werner, nº 529, Vila Operária, Itajaí/SC, CEP 88.303-160, e-mail societario@sevensc.com.br, doravante denominada simplesmente <b>CONTRATADA</b>.</p>

        <p><b>CONTRATANTE:</b> <span id="ct-nome" class="ct-fill"></span>, inscrito(a) no CPF/CNPJ sob o nº <span id="ct-doc" class="ct-fill"></span>, com endereço em <span id="ct-endereco" class="ct-fill"></span>, e-mail <span id="ct-email" class="ct-fill"></span>, telefone <span id="ct-fone" class="ct-fill"></span>, doravante denominado(a) simplesmente <b>CONTRATANTE</b>.</p>

        <p>As partes acima têm, entre si, justo e contratado o presente Contrato de Assinatura e Prestação de Serviços, que se regerá pelas cláusulas seguintes:</p>

        <h2 class="ct-h2">Cláusula 1 — Objeto</h2>
        <p>1.1. O presente contrato tem por objeto a concessão de licença de uso, na modalidade assinatura, da plataforma de CRM denominada <b>"CRM de Alta Conversão"</b> (a "Plataforma"), incluindo funil de vendas, atendimento via WhatsApp, automações e demais funcionalidades do plano contratado, bem como a <b>configuração e operação da camada de rastreamento e integrações</b> (Meta Pixel + Conversions API, Google Ads via API, GA4 e GTM server-side), executadas pela CONTRATADA.</p>
        <p>1.2. Plano contratado: <b><span id="ct-plano2"></span></b> — <span id="ct-usuarios"></span>.</p>

        <h2 class="ct-h2">Cláusula 2 — Preço, forma de pagamento e vigência</h2>
        <p>2.1. Pela assinatura, a CONTRATANTE pagará a mensalidade de <b>R$ <span id="ct-mensal"></span></b>, cobrada mensalmente por cartão de crédito, por meio do processador de pagamentos Asaas.</p>
        <p>2.2. O presente contrato tem <b>vigência de 12 (doze) meses</b>, com cobrança mensal, totalizando o valor global de <b>R$ <span id="ct-anual"></span></b> no período.</p>
        <p>2.3. A primeira cobrança ocorre no ato da contratação e as demais nas datas mensais subsequentes, renovando-se a assinatura automaticamente ao término da vigência, salvo manifestação em contrário de qualquer das partes.</p>

        <h2 class="ct-h2">Cláusula 3 — Rescisão e multa proporcional</h2>
        <p>3.1. Considerando que o valor é apurado com base no período anual de 12 (doze) meses, em caso de <b>rescisão antecipada e imotivada pela CONTRATANTE</b> antes do término da vigência, será devida <b>multa rescisória equivalente a <span id="ct-multapct"></span>% do valor das mensalidades vincendas</b>, isto é, proporcional aos meses faltantes para o encerramento do contrato.</p>
        <p>3.2. A multa é calculada pela fórmula: <b><span id="ct-multapct2"></span>% × R$ <span id="ct-mensal2"></span> × (nº de meses restantes até o 12º mês)</b>. Exemplo: restando 8 meses, a multa seria R$ <span id="ct-multaex"></span>.</p>
        <p>3.3. Não haverá multa em caso de rescisão por descumprimento contratual pela CONTRATADA, nem ao término natural da vigência.</p>
        <p>3.4. O <b>cancelamento deverá ser solicitado exclusivamente por e-mail</b>, para o endereço <b>cancelamento@vibradados.com.br</b>. Considera-se como data do cancelamento a do recebimento da solicitação nesse e-mail, data essa utilizada para o cálculo de eventual multa proporcional prevista nesta cláusula.</p>

        <h2 class="ct-h2">Cláusula 4 — Obrigações da CONTRATADA</h2>
        <p>4.1. Disponibilizar a Plataforma e realizar a configuração e operação do rastreamento e integrações descritos na Cláusula 1, empregando os melhores esforços para o funcionamento adequado dos serviços.</p>
        <p>4.2. Prestar suporte à CONTRATANTE dentro do escopo do plano contratado.</p>

        <h2 class="ct-h2">Cláusula 5 — Obrigações da CONTRATANTE</h2>
        <p>5.1. Fornecer dados verídicos e os acessos necessários (contas de anúncio, WhatsApp e afins) para a configuração dos serviços.</p>
        <p>5.2. Utilizar a Plataforma de forma lícita, responsabilizando-se pelo conteúdo e pelas comunicações realizadas com seus clientes, em conformidade com a legislação aplicável.</p>
        <p>5.3. Manter em dia o pagamento das mensalidades. O não pagamento poderá ensejar a suspensão do acesso à Plataforma.</p>

        <h2 class="ct-h2">Cláusula 6 — Proteção de dados (LGPD)</h2>
        <p>6.1. As partes obrigam-se a tratar os dados pessoais a que tiverem acesso em conformidade com a Lei nº 13.709/2018 (LGPD), utilizando-os exclusivamente para as finalidades deste contrato e adotando medidas de segurança adequadas.</p>

        <h2 class="ct-h2">Cláusula 7 — Propriedade intelectual</h2>
        <p>7.1. A Plataforma, sua tecnologia, marcas e materiais são de titularidade da CONTRATADA, sendo concedida à CONTRATANTE apenas licença de uso, pessoal e intransferível, durante a vigência do contrato.</p>

        <h2 class="ct-h2">Cláusula 8 — Aceite eletrônico e foro</h2>
        <p>8.1. A CONTRATANTE declara ter lido e aceito integralmente este contrato por meio de aceite eletrônico, cujo registro (data, hora e IP) tem validade jurídica nos termos da MP 2.200-2/2001 e do Marco Civil da Internet (Lei nº 12.965/2014).</p>
        <p>8.2. Para dirimir eventuais controvérsias oriundas deste contrato, fica eleito o foro da Comarca de <b><span id="ct-foro"></span></b>, cidade em que a CONTRATANTE manifesta o presente aceite, com renúncia a qualquer outro, por mais privilegiado que seja.</p>

        <p class="ct-sign"><span id="ct-local"></span>, <span id="ct-data2"></span>.</p>
        <p class="ct-sign2">Aceite eletrônico registrado no ato da contratação (data, hora e IP), com envio da confirmação ao e-mail da CONTRATANTE.</p>
      </div>
    </div>
  </div>

  <footer class="ck-footer">
    <img src="/assets/vibra-logo-white.png" alt="Vibra Marketing" />
    <span>© 2026 Vibra Marketing · CRM de Alta Conversão · Operado por ALIERE CURADORIA IMOBILIARIA LTDA</span>
  </footer>

  <script>
    (function(){
      var PLANO = <?= json_encode(['nome'=>$plano['nome'],'usuarios'=>$plano['usuarios'],'mensal'=>$plano['valor'],'anual'=>$anual]) ?>;
      var MULTA = <?= (int)$MULTA_PCT ?>;
      var $ = function(s){ return document.querySelector(s); };
      var money = function(v){ return v.toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2}); };

      /* --------- validação CPF/CNPJ --------- */
      function vCPF(c){ c=(c||'').replace(/\D/g,''); if(c.length!==11||/^(\d)\1+$/.test(c))return false;
        var s=0,i,d; for(i=0;i<9;i++)s+=+c[i]*(10-i); d=(s*10)%11%10; if(d!==+c[9])return false;
        s=0; for(i=0;i<10;i++)s+=+c[i]*(11-i); d=(s*10)%11%10; return d===+c[10]; }
      function vCNPJ(c){ c=(c||'').replace(/\D/g,''); if(c.length!==14||/^(\d)\1+$/.test(c))return false;
        var b=[5,4,3,2,9,8,7,6,5,4,3,2],i,n=0; for(i=0;i<12;i++)n+=+c[i]*b[i]; var r=n%11; if(+c[12]!==(r<2?0:11-r))return false;
        b.unshift(6); n=0; for(i=0;i<13;i++)n+=+c[i]*b[i]; r=n%11; return +c[13]===(r<2?0:11-r); }
      function docOk(v){ v=(v||'').replace(/\D/g,''); return v.length===11?vCPF(v):(v.length===14?vCNPJ(v):false); }

      var docEl=$('#cpfCnpj'), docHint=$('#docHint');
      function checaDoc(){
        var v=docEl.value.replace(/\D/g,'');
        if(!v){ docHint.textContent=''; docEl.classList.remove('ok','bad'); return; }
        if(docOk(v)){ docHint.textContent = v.length===11?'CPF válido ✓':'CNPJ válido ✓'; docHint.className='ck-hint ok'; docEl.classList.remove('bad'); docEl.classList.add('ok'); }
        else { docHint.textContent = v.length<11?'Continue digitando…':(v.length===11?'CPF inválido':(v.length<14?'Continue digitando…':'CNPJ inválido')); docHint.className='ck-hint bad'; docEl.classList.remove('ok'); if(v.length>=11)docEl.classList.add('bad'); }
      }
      docEl.addEventListener('input', checaDoc); if(docEl.value)checaDoc();

      /* --------- máscara + autofill CEP (ViaCEP) --------- */
      var cepEl=$('#cep'), cepBuscado='';
      function buscaCep(){
        var c=cepEl.value.replace(/\D/g,''); if(c.length!==8 || c===cepBuscado) return;
        cepBuscado=c;
        cepEl.classList.add('loading');
        fetch('https://viacep.com.br/ws/'+c+'/json/').then(function(r){return r.json();}).then(function(d){
          cepEl.classList.remove('loading');
          if(d.erro){ cepBuscado=''; return; }
          $('#logradouro').value=d.logradouro||$('#logradouro').value;
          $('#bairro').value=d.bairro||$('#bairro').value;
          $('#cidade').value=d.localidade||'';
          $('#uf').value=(d.uf||'').toUpperCase();
          if($('#numero') && !$('#numero').value) $('#numero').focus();
        }).catch(function(){ cepEl.classList.remove('loading'); cepBuscado=''; });
      }
      cepEl.addEventListener('input', function(){
        var c=cepEl.value.replace(/\D/g,'').slice(0,8);
        cepEl.value = c.length>5 ? c.slice(0,5)+'-'+c.slice(5) : c;
        if(c.length===8) buscaCep();
      });
      cepEl.addEventListener('blur', buscaCep);

      /* --------- contrato (modal) --------- */
      var modal=$('#contrato-modal');
      function fmtDoc(v){ v=(v||'').replace(/\D/g,''); if(v.length===11)return v.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/,'$1.$2.$3-$4'); if(v.length===14)return v.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/,'$1.$2.$3/$4-$5'); return v; }
      function endereco(){
        var l=$('#logradouro').value, n=$('#numero').value, cp=$('#complemento').value, b=$('#bairro').value, c=$('#cidade').value, u=$('#uf').value, cep=$('#cep').value;
        var p=[]; if(l)p.push(l); if(n)p.push('nº '+n); if(cp)p.push(cp); if(b)p.push(b);
        var loc=[c,u].filter(Boolean).join('/'); if(loc)p.push(loc); if(cep)p.push('CEP '+cep);
        return p.join(', ') || '—';
      }
      function preencheContrato(){
        var hoje=new Date().toLocaleDateString('pt-BR');
        var set=function(id,val){ var el=$(id); if(el)el.textContent=val; };
        set('#ct-plano',PLANO.nome); set('#ct-plano2',PLANO.nome); set('#ct-usuarios',PLANO.usuarios);
        set('#ct-data',hoje); set('#ct-data2',hoje);
        set('#ct-nome',$('#nome').value||'—'); set('#ct-doc',fmtDoc($('#cpfCnpj').value)||'—');
        set('#ct-endereco',endereco()); set('#ct-email',$('#email').value||'—'); set('#ct-fone',$('#celular').value||'—');
        set('#ct-mensal',money(PLANO.mensal)); set('#ct-mensal2',money(PLANO.mensal)); set('#ct-anual',money(PLANO.anual));
        set('#ct-multapct',MULTA); set('#ct-multapct2',MULTA);
        set('#ct-multaex',money(MULTA/100*PLANO.mensal*8));
        var loc=[$('#cidade').value,$('#uf').value].filter(Boolean).join('/')||'—';
        set('#ct-foro',loc); set('#ct-local',loc);
      }
      function abrir(){ preencheContrato(); modal.classList.add('open'); modal.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; }
      function fechar(){ modal.classList.remove('open'); modal.setAttribute('aria-hidden','true'); document.body.style.overflow=''; }
      $('#abrirContrato').addEventListener('click', abrir);
      modal.querySelectorAll('[data-close]').forEach(function(el){ el.addEventListener('click', fechar); });
      $('#aceitarFechar').addEventListener('click', function(){ $('#aceite').checked=true; fechar(); });
      $('#baixarPdf').addEventListener('click', function(){ window.print(); });
      document.addEventListener('keydown', function(e){ if(e.key==='Escape'&&modal.classList.contains('open'))fechar(); });

      /* --------- validação no submit --------- */
      $('#ckForm').addEventListener('submit', function(e){
        if(!docOk($('#cpfCnpj').value)){ e.preventDefault(); checaDoc(); docEl.focus(); alert('CPF ou CNPJ inválido. Confira os números.'); return; }
        if(!$('#aceite').checked){ e.preventDefault(); alert('É necessário ler e aceitar o contrato para continuar.'); }
      });
    })();
  </script>
</body>
</html>
