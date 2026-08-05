<?php
/* ============================================================
   SimedPrev Saúde — Manual do Closer (material interno)
   Porta de acesso por senha (sessão PHP). O conteúdo do manual
   só é enviado ao navegador APÓS autenticação — antes disso,
   o código-fonte da página contém apenas a tela de login.
   ============================================================ */

session_start();

// Hash bcrypt da senha (a senha em claro NÃO fica no arquivo).
$SP_HASH = '$2y$12$1GAI0OeDM/C5Lrf6Ol7qcezijEH7ytcD2/bp9QGVRxBSTGkF8d2yi';

$erro = false;

if (isset($_POST['senha'])) {
    if (password_verify($_POST['senha'], $SP_HASH)) {
        session_regenerate_id(true);
        $_SESSION['sp_ok'] = true;
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
        exit;
    }
    $erro = true;
}

if (isset($_GET['sair'])) {
    $_SESSION = array();
    session_destroy();
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

$autenticado = !empty($_SESSION['sp_ok']);

// Não indexar em nenhuma hipótese
header('X-Robots-Tag: noindex, nofollow, noarchive');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="theme-color" content="#050505" />
  <meta name="robots" content="noindex, nofollow, noarchive" />
  <meta name="referrer" content="no-referrer" />

  <title><?php echo $autenticado ? 'Manual do Closer — SimedPrev Saúde' : 'Acesso restrito — SimedPrev Saúde'; ?></title>

  <link rel="icon" href="/assets/favicon.svg" type="image/svg+xml" />

  <link rel="preconnect" href="https://api.fontshare.com" crossorigin />
  <link rel="preconnect" href="https://cdn.fontshare.com" crossorigin />
  <link href="https://api.fontshare.com/v2/css?f[]=clash-display@600,700,500,400&f[]=general-sans@400,500,600&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="/styles.css?v=6" />
  <link rel="stylesheet" href="/treinamento-closer-interno/treinamento.css?v=1" />
</head>
<body class="js">
  <div class="bg-grain" aria-hidden="true"></div>
  <div class="bg-aura" aria-hidden="true">
    <div class="aura aura--1"></div>
    <div class="aura aura--2"></div>
  </div>
  <div class="bg-grid" aria-hidden="true"></div>

<?php if (!$autenticado): ?>

  <!-- ============ TELA DE LOGIN ============ -->
  <main class="sp-gate">
    <div class="sp-gate__card">
      <div class="sp-gate__lock" aria-hidden="true">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
      </div>
      <div class="sp-gate__brand">SimedPrev <b>Saúde</b></div>
      <div class="sp-gate__sub">Material Interno</div>
      <form method="post" autocomplete="off">
        <input type="password" name="senha" placeholder="Digite a senha de acesso" autofocus aria-label="Senha de acesso" />
        <button type="submit">Acessar</button>
      </form>
      <?php if ($erro): ?>
        <p class="sp-gate__err">Senha incorreta</p>
      <?php else: ?>
        <p class="sp-gate__err">&nbsp;</p>
      <?php endif; ?>
      <p class="sp-gate__foot">Acesso restrito ao time comercial · não distribuir</p>
    </div>
  </main>

<?php else: ?>

  <!-- ============ MANUAL (conteúdo protegido) ============ -->
  <header class="nav" style="position:relative;padding-top:22px;padding-bottom:0">
    <a class="sp-wordmark" href="/treinamento-closer-interno/">SimedPrev <b>Saúde</b></a>
  </header>

  <main>
    <!-- ===== CAPA ===== -->
    <header class="doc-header">
      <span class="doc-eyebrow">Programa de Treinamento Comercial · Fase 1</span>
      <h1>A Call de Vendas de Alta Conversão</h1>
      <p class="subtitle">Manual de fechamento por closer baseado na metodologia da <b>Venda Desafiadora (Challenger Sale)</b> — do primeiro segundo da ligação ao Pix na call. Adaptado para a operação comercial do convênio médico por telefone e WhatsApp.</p>
    </header>

    <div class="sp-cover-cards">
      <div class="sp-cover-card"><span class="num">01</span><span class="t">Fundamento</span><span class="d">A ciência do perfil desafiador e por que o "amigão" perde</span></div>
      <div class="sp-cover-card"><span class="num">02</span><span class="t">Mentalidade</span><span class="d">Neutralidade, sinais e o único inimigo real: o medo</span></div>
      <div class="sp-cover-card"><span class="num">03</span><span class="t">A Call</span><span class="d">Os 6 estágios: abertura, diagnóstico, Pit 01, solução, Pit 02, fechamento</span></div>
      <div class="sp-cover-card"><span class="num">04</span><span class="t">Aplicação</span><span class="d">Roteiros, objeções e números adaptados ao produto</span></div>
    </div>

    <div class="doc-meta">
      <span class="sp-tag">Uso interno</span>
      <span><b>Agosto de 2026</b> · Versão 1.0</span>
      <span>Fase 1 de 3 do Programa Comercial</span>
      <span>Treinamento do Time de Vendas — Closers</span>
    </div>

    <!-- ===== SUMÁRIO ===== -->
    <nav class="doc-toc" aria-label="Sumário">
      <div class="doc-toc__box">
        <p class="doc-toc__title">Sumário · Manual do Closer</p>
        <ol>
          <li><a href="#s1"><b>01</b><span>O fundamento científico — a Venda Desafiadora</span><span class="pt">Parte 1</span></a></li>
          <li><a href="#s2"><b>02</b><span>A mentalidade do closer de alta conversão</span><span class="pt">Parte 2</span></a></li>
          <li><a href="#s3"><b>03</b><span>A anatomia da call — visão geral dos 6 estágios</span><span class="pt">Parte 3</span></a></li>
          <li><a href="#s4"><b>04</b><span>Estágio 1 — Abertura: o bastão do controle</span><span class="pt">Parte 3</span></a></li>
          <li><a href="#s5"><b>05</b><span>Estágio 2 — Diagnóstico: o arquétipo do psicólogo</span><span class="pt">Parte 3</span></a></li>
          <li><a href="#s6"><b>06</b><span>Estágio 3 — Pit 01: o compromisso antes do preço</span><span class="pt">Parte 3</span></a></li>
          <li><a href="#s7"><b>07</b><span>Estágio 4 — Apresentação da solução</span><span class="pt">Parte 3</span></a></li>
          <li><a href="#s8"><b>08</b><span>Estágio 5 — Pit 02: ancoragem, tabela e condição única</span><span class="pt">Parte 3</span></a></li>
          <li><a href="#s9"><b>09</b><span>Estágio 6 — Fechamento, sinal e o fim do follow-up</span><span class="pt">Parte 3</span></a></li>
          <li><a href="#s10"><b>10</b><span>Públicos, arsenal de valor e matriz de objeções</span><span class="pt">Parte 4</span></a></li>
          <li><a href="#s11"><b>11</b><span>A call modelo SimedPrev Saúde (roteiro completo)</span><span class="pt">Parte 4</span></a></li>
          <li><a href="#s12"><b>12</b><span>Checklist do closer, métricas e referências</span><span class="pt">Parte 4</span></a></li>
        </ol>
      </div>
    </nav>

    <div class="sp-thesis">
      <div class="sp-thesis__inner">
        <p class="lbl">A tese central deste manual</p>
        <p>"O Pix é só um efeito. Ele é o efeito de tudo que a gente construiu. Muita gente diz 'tô ruim na conversão' — mas errou lá na entrada, errou no diagnóstico, não gerou valor. Não é sobre ser agressivo: é sobre ser interessado e entender como ajudar aquela pessoa."</p>
      </div>
    </div>
    <p class="sp-thesis src">Material fundamentado na pesquisa CEB/Gartner publicada em <em>The Challenger Sale</em> (Dixon &amp; Adamson), na regra 7-38-55 de Mehrabian e no método NEPQ de Jeremy Miner — adaptado à venda do SimedPrev Saúde por telefone e WhatsApp.</p>

    <article class="doc">

      <!-- ================= PARTE 1 ================= -->
      <section id="s1">
        <p class="sp-part-eyebrow">01 · Parte 1</p>
        <h2>O Fundamento Científico: a Venda Desafiadora</h2>
        <p>Antes de qualquer técnica, você precisa entender por que este método funciona. Ele não nasceu de opinião: nasceu da maior pesquisa já feita sobre performance de vendedores — e ela derruba quase tudo que o mercado brasileiro acredita sobre vender.</p>
        <ol class="plain">
          <li><b>1.1</b> &nbsp;A pesquisa CEB/Gartner: 6.000 vendedores B2B analisados</li>
          <li><b>1.2</b> &nbsp;Os 5 perfis de vendedor — e onde cada um termina</li>
          <li><b>1.3</b> &nbsp;Por que o "amigão" é estatisticamente o pior perfil</li>
          <li><b>1.4</b> &nbsp;Os 4 pilares do vendedor desafiador</li>
          <li><b>1.5</b> &nbsp;Tensão construtiva: o envelope de tudo</li>
        </ol>

        <h3><span class="n">1.1</span> — A pesquisa que mudou o jogo</h3>
        <p>No início dos anos 2010, a consultoria americana CEB (hoje parte da <b>Gartner</b>) conduziu um estudo com mais de <b>6.000 vendedores B2B em dezenas de empresas</b>, com uma pergunta: o que separa o vendedor de alta performance de todos os outros?</p>
        <p>O resultado saiu no livro <em>The Challenger Sale</em> (A Venda Desafiadora), de Matthew Dixon e Brent Adamson: todo vendedor se encaixa predominantemente em um de <b>cinco perfis comportamentais</b>:</p>
        <div class="doc-table-wrap">
          <table>
            <thead><tr><th>Perfil</th><th>Comportamento típico</th></tr></thead>
            <tbody>
              <tr><td><b>O Desafiador</b> (Challenger)</td><td>Ensina algo novo ao cliente, personaliza a mensagem, assume o controle da conversa e não tem medo de gerar tensão produtiva. Ama debater e tirar o cliente da zona de conforto.</td></tr>
              <tr><td><b>O Trabalhador Duro</b> (Hard Worker)</td><td>Chega cedo, faz mais ligações que todo mundo, se esforça, não desiste. Aposta no volume.</td></tr>
              <tr><td><b>O Construtor de Relacionamento</b> (Relationship Builder)</td><td>O "amigão". Cria rapport, agrada, evita conflito, quer ser querido pelo cliente. É o perfil mais comum na cultura brasileira.</td></tr>
              <tr><td><b>O Lobo Solitário</b> (Lone Wolf)</td><td>Segue os próprios instintos, ignora processo e playbook. Às vezes entrega, mas é imprevisível e não escala.</td></tr>
              <tr><td><b>O Solucionador de Problemas</b> (Problem Solver)</td><td>Detalhista, confiável, focado no pós-venda e em resolver tudo — às vezes mais atendente do que vendedor.</td></tr>
            </tbody>
          </table>
        </div>

        <h3><span class="n">1.2</span> — O resultado que assustou até os americanos</h3>
        <p>Quando a CEB cruzou os perfis com os resultados de vendas, o dado central apareceu: entre os vendedores de <b>alta performance</b>, quase <b>40% eram Desafiadores</b>. O Trabalhador Duro ficou na casa dos <b>17%</b>. E o Construtor de Relacionamento — o perfil que o senso comum diria ser o melhor — foi o <b>pior de todos: apenas 7%</b>.</p>
        <p class="sp-figtitle">Participação de cada perfil entre os vendedores de ALTA performance</p>
        <div class="sp-barchart">
          <div class="sp-bar-row"><span class="bl">Desafiador</span><div class="sp-bar-track"><span class="sp-bar sp-bar--win" style="width:100%"></span><span class="pv">39%</span></div></div>
          <div class="sp-bar-row"><span class="bl">Lobo Solitário</span><div class="sp-bar-track"><span class="sp-bar" style="width:64%"></span><span class="pv">25%</span></div></div>
          <div class="sp-bar-row"><span class="bl">Trabalhador Duro</span><div class="sp-bar-track"><span class="sp-bar" style="width:44%"></span><span class="pv">17%</span></div></div>
          <div class="sp-bar-row"><span class="bl">Solucionador</span><div class="sp-bar-track"><span class="sp-bar" style="width:31%"></span><span class="pv">12%</span></div></div>
          <div class="sp-bar-row"><span class="bl">Amigão (Relacionam.)</span><div class="sp-bar-track"><span class="sp-bar sp-bar--lose" style="width:18%"></span><span class="pv">7%</span></div></div>
        </div>
        <p class="sp-figcap">Fonte: pesquisa CEB/Gartner com 6.000+ vendedores B2B, publicada em The Challenger Sale (Dixon &amp; Adamson). Percentuais aproximados.</p>
        <div class="sp-box sp-box--dark">
          <span class="lbl">Leia o gráfico do jeito certo</span>
          <p>Se você for um construtor de relacionamento e for ruim, será muito ruim — é o pior perfil e o que mais concentra medianos. Se você for um desafiador, mesmo sendo iniciante, já larga na frente da grande maioria. E em vendas complexas, a vantagem do desafiador fica ainda maior.</p>
        </div>

        <h3><span class="n">1.3</span> — Por que o "amigão" perde (e por que isso dói no Brasil)</h3>
        <p>Nós somos brasileiros. Nossa cultura comercial inteira foi construída sobre rapport, simpatia e amizade. A pesquisa mostra que esse é justamente o caminho da mediocridade em vendas consultivas. Entenda o mecanismo:</p>
        <div class="sp-quote">
          <p>"Quando um vendedor fala 'tudo bem? tudo joia?', o que vem no subconsciente do cliente? — 'Tá querendo puxar meu saco. Ele quer meu dinheiro. Beleza, vamos ver se você vai conseguir me convencer.' Você começou a call perdendo."</p>
          <span class="cap">Fundamento do método · o ser humano toma cerca de 2.000 decisões por hora — o julgamento sobre você começa no primeiro segundo</span>
        </div>
        <h4>O contraste que resume tudo: a vendedora de loja vs. o médico</h4>
        <div class="sp-cards">
          <div class="sp-card sp-card--red"><h5>Varejo (ticket baixo)</h5><p>"Oi, tudo bem, dona Maria? Vem cá, dona Maria! 12 vezes no cartão!" — Sorriso, elogio, insistência. Funciona para produto de prateleira, não para venda consultiva.</p></div>
          <div class="sp-card sp-card--teal"><h5>Médico (venda consultiva)</h5><p>Ele nem pergunta se está tudo bem. Olha a ficha e diz: <b>"Como posso te ajudar?"</b> Neutro, direto, investigativo. Quanto mais consultiva a venda, mais a postura se aproxima da do médico.</p></div>
        </div>
        <div class="sp-box sp-box--dark">
          <span class="lbl">Regra de ouro nº 1</span>
          <p>Quanto mais consultiva a venda, mais você se comporta como médico e menos como vendedor de loja. O objetivo do closer não é criar amizade — é criar diagnóstico, autoridade e decisão.</p>
        </div>
        <ul>
          <li><b>O amigo não pode cobrar ação.</b> Se você passa a call inteira construindo um "espaço de amizade", com que autoridade você chega ao final e diz "faz o Pix aqui"? O cliente pensa: "ué, tu não era meu amigo? Agora tá me pressionando?" Você mesmo destruiu o campo para o fechamento.</li>
          <li><b>As pessoas não ouvem os amigos.</b> Se ouvir amigos resolvesse, ninguém precisaria de especialista. As pessoas dão mais peso à opinião de uma autoridade neutra de fora do que à de quem é próximo.</li>
          <li><b>Quem quer agradar não desafia.</b> E é o desafio — mostrar ao cliente algo que ele não sabia sobre o próprio problema — que gera valor percebido e justifica o preço.</li>
        </ul>

        <h3><span class="n">1.4</span> — Os 4 pilares do vendedor desafiador</h3>
        <p>A pesquisa isolou o que o desafiador faz de diferente. São quatro comportamentos — os três primeiros são os pilares formais do método (Teach, Tailor, Take Control) e o quarto é o envelope que embala todos:</p>
        <div class="sp-cards">
          <div class="sp-card sp-card--teal"><h5>1 · Personalizar (Tailor)</h5><p>Ele nunca explica o produto duas vezes do mesmo jeito. Cada apresentação é montada sobre o diagnóstico daquele cliente. Se você sabe exatamente a hora em que pausa e a hora em que fala, você está decorando — e decorar é um erro.</p></div>
          <div class="sp-card sp-card--teal"><h5>2 · Ensinar (Teach)</h5><p>Ele ensina — mas não sobre o produto: sobre como o mundo funciona. Mostra ao cliente ângulos do próprio problema que o cliente não via. É por isso que o cliente compra: você chegou a pontos que ele nem sabia que não sabia.</p></div>
          <div class="sp-card sp-card--teal"><h5>3 · Assumir o controle (Take Control)</h5><p>Do primeiro segundo ao Pix, o bastão da condução é do closer: "Eu sou responsável por conduzir a nossa conversa e vou te explicar como ela vai funcionar." Sem pedir licença, sem puxar saco — e sem agressividade.</p></div>
          <div class="sp-card sp-card--red"><h5>4 · Tensão construtiva</h5><p>O envelope de tudo. É o desconforto produtivo que faz o cliente sair da inércia: "Joga limpo comigo." "Por que tu não resolveu isso antes?" Tensão não é grosseria — é sinceridade com acolhimento.</p></div>
        </div>
        <h4>Recapitulando — teste rápido de autoavaliação</h4>
        <p>Responda mentalmente antes de seguir para a Parte 2:</p>
        <ol class="plain">
          <li>Na sua última venda perdida, você foi amigão ou desafiador?</li>
          <li>Você sabe de cor a hora em que pausa e a hora em que fala na sua apresentação? (Se sim, você está decorando — e não personalizando.)</li>
          <li>Qual foi a última vez que um cliente te disse "nunca tinha pensado nisso"? Essa frase é o termômetro do pilar Ensinar.</li>
          <li>Você termina suas calls com um compromisso concreto (pagamento, sinal, contrato) ou com "qualquer coisa me chama"?</li>
        </ol>

        <h3><span class="n">1.5</span> — Tensão construtiva na prática</h3>
        <p>Tensão construtiva é a habilidade que diferencia o desafiador de todos os outros perfis: colocar o cliente frente a frente com a própria realidade, sem perder o vínculo. É pressão a favor do cliente, não contra ele.</p>
        <div class="doc-table-wrap">
          <table>
            <thead><tr><th>SEM tensão (o amigão)</th><th>COM tensão construtiva (o desafiador)</th></tr></thead>
            <tbody>
              <tr><td>"Sem problema! Pensa com calma e qualquer coisa me chama, tá?"</td><td>"Joga sincero comigo: não é o financeiro, né? O que realmente está te segurando?"</td></tr>
              <tr><td>"Entendo, a decisão é sua, fica à vontade."</td><td>"Tu me disse há dois minutos que isso era prioridade máxima. Agora me diz que vai deixar pra depois. Me ajuda a entender."</td></tr>
              <tr><td>"Vou te mandar a proposta por e-mail."</td><td>"Não existe proposta aqui — existe o teu caso. E o teu caso a gente resolve agora ou tu me diz, olhando pra ele, que não quer resolver."</td></tr>
              <tr><td>Aceita a objeção como verdade e agenda follow-up.</td><td>Trata a objeção como fuga, investiga a verdade por trás dela e lapida tudo dentro da call.</td></tr>
            </tbody>
          </table>
        </div>
        <div class="sp-box sp-box--teal">
          <span class="lbl">Aplicação SimedPrev Saúde</span>
          <p>Nosso cliente é a família de classe C/D da região de Itajaí que gasta em média R$ 364 por mês com saúde (POF/IBGE) sem ter plano nenhum — farmácia cara, consulta particular avulsa, fila do posto. A tensão construtiva aqui é mostrar essa conta que ele nunca fez: "Quanto tu gastou na última ida à farmácia? E quando a tua filha teve febre, quanto tempo levou pra conseguir consulta? Tu tá pagando caro pra não ter nada." O desconforto de ver o próprio número é o que tira essa família da inércia.</p>
        </div>
        <div class="sp-box sp-box--red">
          <span class="lbl">Cuidado — o que a tensão construtiva NÃO é</span>
          <p>Não é gritar, não é humilhar, não é encurralar. A regra do método: ser incisivo dizendo o que precisa ser dito, num tom que acolhe ("estou te abraçando com o tom de voz"). Quem aplica o método percebe que ele é menos agressivo do que parece — porque a pressão foi construída camada por camada, com o consentimento do cliente em cada etapa. No fundo, se ele chegou até aqui, é porque quer.</p>
        </div>
        <div class="sp-box sp-box--dark">
          <span class="lbl">Síntese da Parte 1</span>
          <p>O mercado inteiro compete em simpatia. A ciência mostra que quem ganha compete em diagnóstico, ensino e controle. Nosso time não vai ser o mais simpático da região — vai ser o mais interessado, o mais preparado e o mais direto.</p>
        </div>
        <div class="sp-secfoot"><span><b>Parte 1</b> · O Fundamento Científico</span><span>Uso interno · SimedPrev Saúde</span></div>
      </section>

      <!-- ================= PARTE 2 ================= -->
      <section id="s2">
        <p class="sp-part-eyebrow">02 · Parte 2</p>
        <h2>A Mentalidade do Closer de Alta Conversão</h2>
        <p>Técnica sem mentalidade vira teatro. Antes de decorar qualquer script, o closer precisa internalizar cinco convicções que mudam a forma de estar na call — porque venda é psicologia, e o cliente sente quem você é antes de ouvir o que você diz.</p>
        <ol class="plain">
          <li><b>2.1</b> &nbsp;Não pareça um vendedor</li>
          <li><b>2.2</b> &nbsp;A neutralidade do especialista</li>
          <li><b>2.3</b> &nbsp;Não ignore o óbvio: leia os sinais</li>
          <li><b>2.4</b> &nbsp;O único inimigo real: o medo do cliente</li>
          <li><b>2.5</b> &nbsp;Interessado, não interesseiro</li>
        </ol>

        <h3><span class="n">2.1</span> — Não pareça um vendedor</h3>
        <p>Pesquise no Google "vendedor de call center": você verá um sujeito de fone de ouvido, uniforme e sorriso ensaiado. Se você parece isso para o cliente, é impossível convencê-lo de que quer ajudá-lo — e não apenas vender.</p>

        <h3><span class="n">2.2</span> — A neutralidade do especialista</h3>
        <p>O médico não puxa assunto, não elogia sua roupa, não implora atenção. Ele pergunta, examina, conclui e prescreve — e você obedece. Essa é a posição que o closer ocupa desde o primeiro segundo:</p>
        <div class="doc-table-wrap">
          <table>
            <thead><tr><th>Postura de vendedor (começa perdendo)</th><th>Postura de especialista (começa neutro)</th></tr></thead>
            <tbody>
              <tr><td>"Oi, tudo bem?? Que prazer falar contigo!! Obrigado pelo seu tempo!!"</td><td>"Fala, João, tudo bem? Tá me ouvindo bem? Perfeito. Meu nome é Ana, eu sou responsável por conduzir a nossa conversa e vou te explicar como ela vai funcionar."</td></tr>
              <tr><td>Agradece demais, pede desculpa por ligar, pede licença para falar.</td><td>Conduz com naturalidade. Quem agradece no final é o cliente.</td></tr>
              <tr><td>Quer ser gostado.</td><td>Quer ser útil. (E por isso acaba sendo gostado.)</td></tr>
            </tbody>
          </table>
        </div>

        <h3><span class="n">2.3</span> — Não ignore o óbvio: leia os sinais</h3>
        <p>O maior problema das calls de vendas é ignorar os sinais. O cliente comunica o tempo todo — pelo tom, pela pressa, pelas pausas, pelo que evita responder. O closer mediano segue o script; o closer de alta conversão ajusta a rota a cada sinal.</p>
        <div class="sp-box sp-box--teal">
          <span class="lbl">Sinais no telefone / WhatsApp — nossa realidade</span>
          <p><b>Sinais de abertura:</b> o cliente responde com frases longas, faz perguntas sobre a rede, menciona a família ("minha filha vive com dor de garganta"). <b>Sinais de fuga:</b> respostas monossilábicas, "aham" mecânico, barulho de quem está fazendo outra coisa, "me manda no WhatsApp". <b>Sinais de compra:</b> pergunta de preço no meio da call, pergunta "como funciona pra usar?", menciona uma necessidade com data ("preciso fazer uns exames esse mês"). Cada sinal desses muda o próximo passo — nunca o ignore para "terminar o script".</p>
        </div>
        <div class="sp-quote">
          <p>"A call às vezes é um detalhezinho que eu perco. São várias camadas. Não importa o que foi feito fora da call: a call de vendas é o elo. É escala de eficiência, não de volume — se o cara não comprou, algo foi feito errado lá dentro."</p>
          <span class="cap">Mentalidade central do método</span>
        </div>
        <ul>
          <li><b>Elimine os símbolos de vendedor.</b> Na call por vídeo: sem headset aparente, sem cenário de telemarketing. No telefone: sem "voz de atendente", sem script lido, sem vícios de operador ("um minutinho", "estou verificando no sistema").</li>
          <li><b>Os maiores vendedores não parecem vendedores.</b> Observe os grandes closers de qualquer mercado: eles falam como especialistas, não como vendedores. Os que mais fecham costumam ser justamente os que parecem "o cara técnico" — a pessoa que domina o assunto e conversa de igual para igual, sem figurino de vendas.</li>
          <li><b>Fale de igual para igual.</b> Autoridade não vem de formalidade — vem de domínio do assunto e de sinceridade. Quem sabe, conversa; quem não sabe, recita.</li>
        </ul>

        <h3><span class="n">2.4</span> — O único inimigo real: o medo</h3>
        <p>Quando você faz tudo certo na call e mesmo assim perde, existe um único culpado possível: o medo do cliente. A função biológica do medo é fazer a pessoa parar para não perder algo — quem tem medo de pular de paraquedas não pula; quem tem medo de perder dinheiro não compra.</p>
        <p>Por isso "vender para quem tem mentalidade de escassez" é mais difícil: quando o dinheiro é tudo que a pessoa tem, ela o protege como se protege a família. O problema não é o CPF nem a renda: é o medo e a inércia. Existe o cliente de poucos recursos que é decidido ("toma aqui, eu acredito") — esse fecha. E existe o cliente com dinheiro que trava qualquer decisão de R$ 10 mil — esse é o "pobre" da tese, independente do saldo.</p>
        <div class="sp-cards">
          <div class="sp-card sp-card--red"><h5>Cliente travado (mentalidade de escassez)</h5><p>Vê a mensalidade como perda. Precisa "pensar", consultar todo mundo, adiar. O medo decide por ele. Com ele, o closer precisa reduzir o risco percebido e aumentar o custo de não agir.</p></div>
          <div class="sp-card sp-card--teal"><h5>Cliente decidido (mentalidade de abundância)</h5><p>Vê a mensalidade como troca: dinheiro por solução. Já tomou decisões na vida, decide rápido — para o sim e para o não. É o cliente ideal, mesmo quando tem pouco.</p></div>
        </div>
        <div class="sp-box sp-box--teal">
          <span class="lbl">Aplicação SimedPrev Saúde — vendemos para a classe C/D, e isso muda tudo</span>
          <p>Nosso público é exatamente o que mais sente medo de perder dinheiro — e é por isso que o produto foi desenhado para desarmá-lo. As armas do closer contra o medo: <b>(1)</b> uma mensalidade cobre a família inteira — pais e filhos menores de 18 na mesma casa, R$ 89/mês no plano com fidelidade (menos de R$ 3 por dia pela família toda); <b>(2)</b> a família já gasta em média R$ 364/mês com saúde sem ter nada em troca — o convênio não é gasto novo, é redução de um gasto que já existe: o assinante paga o preço de custo negociado com as clínicas, sem taxa de intermediação; <b>(3)</b> o extrato mensal de economia prova em números que "o convênio se paga sozinho"; <b>(4)</b> existe opção sem fidelidade (R$ 119) para quem precisa testar antes de confiar. O closer que entende isso nunca vende "mais uma conta no fim do mês" — vende o fim de um desperdício.</p>
        </div>

        <h3><span class="n">2.5</span> — Interessado, não interesseiro</h3>
        <p>A frase que resume a ética do método: "pessoas boas compram de pessoas boas". O closer dedica o tempo que for preciso para entender de verdade o caso do cliente, enquanto a maioria mal escuta e quer logo vender. Se você consegue ajudar e deixa o cliente ir embora sem agir, você é que está deixando de ajudar.</p>
        <div class="sp-box sp-box--dark">
          <span class="lbl">Síntese da Parte 2</span>
          <p>O cliente não compra de quem precisa da venda; compra de quem pode ajudá-lo. Neutralidade na entrada, leitura de sinais o tempo todo, guerra contra o medo — e interesse genuíno do início ao fim.</p>
          <p><b>Se pode ajudar, diga que pode</b> — e conduza até a ação. Parar antes do fechamento por "educação" é abandonar o cliente na porta da solução.</p>
          <p><b>Se não pode ajudar, diga que não pode.</b> Encerrar a call com quem não faz sentido também é o método. Tempo não volta — nem o seu, nem o dele.</p>
          <p><b>A venda é um conteúdo personalizado.</b> Uma call de vendas nada mais é do que um conteúdo feito sob medida para uma pessoa tomar uma ação. Quem pensa assim para de "empurrar" e começa a "entregar".</p>
        </div>
        <div class="sp-secfoot"><span><b>Parte 2</b> · Mentalidade do Closer</span><span>Uso interno · SimedPrev Saúde</span></div>
      </section>

      <!-- ================= PARTE 3 ================= -->
      <section id="s3">
        <p class="sp-part-eyebrow">03 · Parte 3</p>
        <h2>A Anatomia da Call: os 6 Estágios</h2>
        <p>A call de alta conversão é uma <b>arquitetura</b>: cada estágio prepara o seguinte, e cada um tem um único objetivo. O Pix do final é só a consequência de seis etapas bem executadas. Quem pula etapa, perde no fim — e acha que o problema foi o fechamento.</p>
        <ol class="plain">
          <li><b>E1</b> &nbsp;Abertura — tomar o bastão do controle</li>
          <li><b>E2</b> &nbsp;Diagnóstico — descer camadas como um psicólogo</li>
          <li><b>E3</b> &nbsp;Pit 01 — compromisso do cliente com ele mesmo (sem preço)</li>
          <li><b>E4</b> &nbsp;Apresentação — entregável, benefício, dor</li>
          <li><b>E5</b> &nbsp;Pit 02 — ancoragem, preço de tabela e condição única</li>
          <li><b>E6</b> &nbsp;Fechamento — pagamento na call e morte do follow-up</li>
        </ol>

        <h3>O mapa completo da call</h3>
        <p>Guarde este mapa. As setas descendentes são o caminho da conversão; as saídas laterais são os <b>filtros</b> — momentos em que o closer decide, conscientemente, se vale avançar. Avançar com a pessoa errada é a origem de toda objeção "insuperável" do final.</p>
        <div class="sp-flow">
          <div class="sp-flow-note">
            <span class="lbl">Regra das etapas</span>
            <p>Só avance quando o objetivo da etapa atual estiver cumprido.</p>
            <p>Objeção no fim = etapa mal feita lá atrás.</p>
            <p>O preço só aparece no estágio 5.</p>
          </div>
          <div class="sp-flow-stage dark"><span class="t">E1 · ABERTURA (1–2 min)</span><span class="d">Bastão do controle · inversão de polaridade · zero puxa-saco</span></div>
          <div class="sp-flow-arrow"></div>
          <div class="sp-flow-stage teal"><span class="t">E2 · DIAGNÓSTICO (o coração da call)</span><span class="d">Psicólogo · descer camadas · elevar nível de consciência</span></div>
          <div class="sp-flow-arrow"></div>
          <div class="sp-flow-stage dark"><span class="t">E3 · PIT 01 — SEM PREÇO</span><span class="d">Compromisso do cliente com ele mesmo · antecipar objeções</span></div>
          <div class="sp-flow-filter"><div class="fbox">FILTRO: sem grana, sem prioridade?<em>encerre com respeito</em></div></div>
          <div class="sp-flow-arrow"></div>
          <div class="sp-flow-stage teal"><span class="t">E4 · APRESENTAÇÃO DA SOLUÇÃO</span><span class="d">Entregável → benefício → dor · curta · checkpoint de engajamento</span></div>
          <div class="sp-flow-arrow"></div>
          <div class="sp-flow-stage dark"><span class="t">E5 · PIT 02 — O PIT OFICIAL</span><span class="d">Ancoragem → "vale ou não vale?" → tabela → condição única</span></div>
          <div class="sp-flow-filter"><div class="fbox">"Não vale"? volte ao diagnóstico<em>algo ficou aberto</em></div></div>
          <div class="sp-flow-arrow"></div>
          <div class="sp-flow-stage dark"><span class="t">E6 · FECHAMENTO NA CALL</span><span class="d">"Pix ou cartão?" · sinal + contrato · lapidar tudo dentro da call</span></div>
          <div class="sp-flow-arrow"></div>
          <div class="sp-flow-final"><span class="t">PAGAMENTO CONFIRMADO</span><span class="d">conversão = dinheiro na conta, não promessa</span></div>
        </div>
        <p class="sp-figcap">Figura 2 — O funil interno da call de alta conversão: seis estágios, dois filtros conscientes e um único destino aceitável.</p>
        <div class="sp-secfoot"><span><b>Parte 3</b> · A Anatomia da Call</span><span>Uso interno · SimedPrev Saúde</span></div>
      </section>

      <!-- ===== ESTÁGIO 1 ===== -->
      <section id="s4">
        <p class="sp-part-eyebrow">04 · Parte 3 · Estágio 1</p>
        <h2>E1 — Abertura: o bastão do controle</h2>
        <p>Muitos vendedores começam a call perdendo — e nunca percebem. A abertura tem um único objetivo: estabelecer, nos primeiros 60 segundos, que quem conduz a conversa é você. Sem pedir licença, sem puxar saco, sem parecer vendedor.</p>
        <h4>Os 3 movimentos da abertura</h4>
        <ol class="plain">
          <li><b>Checagem técnica neutra.</b> "Fala, João, tudo bem? Tá me ouvindo bem aí?" — funcional, sem bajulação. Não é "que prazer imenso falar contigo".</li>
          <li><b>Inversão de polaridade (opcional, poderosa).</b> Antes de começar, você demonstra que a sua atenção é um recurso valioso que está sendo dedicado a ele: "Perfeito. Só vou responder uma última mensagem aqui rapidinho — se quiser pegar uma água, fica à vontade." Ou o áudio estratégico: "Pessoal, vou entrar em call agora com o João e vou me doar o máximo aqui pra ele; quando eu sair, respondo vocês." Em 10 segundos o cliente entende: você não é um vendedor faminto — é um profissional disputado que agora é todo dele.</li>
          <li><b>Assumir o comando com clareza.</b> "Seja muito bem-vindo. Meu nome é [nome], eu sou responsável por conduzir a nossa conversa, e vou te explicar como ela vai funcionar, tá?" — Pronto. Acabou a abertura. O bastão é seu.</li>
        </ol>
        <div class="sp-box sp-box--dark">
          <span class="lbl">Por que o "tudo bem? tudo joia?" destrói a call</span>
          <p>O ser humano toma cerca de 2.000 decisões por hora — a maioria inconsciente. Nos primeiros segundos, o cliente já decidiu se você é: (a) alguém puxando saco porque quer o dinheiro dele → ele assume o controle ("vamos ver se você me convence"), ou (b) um especialista neutro → ele entrega o controle e se abre. Não existe terceira opção, e não existe segundo primeiro-segundo.</p>
        </div>
        <div class="sp-script">
          <span class="lbl">Script E1 · Abertura por telefone — lead de anúncio (versão base)</span>
          <p><span class="who">CLOSER:</span> Fala, Dona Márcia, tudo bem? Tá me ouvindo bem aí?</p>
          <p><span class="who who--cli">CLIENTE:</span> Tô sim.</p>
          <p><span class="who">CLOSER:</span> Perfeito. Márcia, meu nome é [nome], eu sou especialista aqui da SimedPrev, em Itajaí. A senhora deixou seu contato pedindo pra entender o cartão de saúde, certo? Então deixa eu te explicar como funciona a nossa conversa: primeiro eu vou te fazer algumas perguntas rápidas pra entender como é a saúde da sua família hoje — porque o plano é montado em cima disso. Depois, se eu enxergar que faz sentido pro seu caso, eu te mostro exatamente como funciona e quanto fica. E se não fizer sentido, eu vou te falar com a mesma sinceridade. Fechado?</p>
          <p><span class="who who--cli">CLIENTE:</span> Fechado.</p>
          <p class="obs">Repare: em 30 segundos, o closer definiu o roteiro, pediu o primeiro "sim" e já plantou a neutralidade ("se não fizer sentido, eu te falo"). Nenhum elogio, nenhum pedido de desculpa por ligar. Somos nós que ligamos — não existe pré-vendedor: o closer conduz do primeiro "alô" ao pagamento. As duas versões completas de abertura (lead de anúncio e base gratuita SimedPrev) estão na seção 4.1.</p>
        </div>

        <h3>E1 — Abertura no WhatsApp e os erros que custam a call</h3>
        <div class="sp-box sp-box--teal">
          <span class="lbl">Adaptação ao WhatsApp — a inversão de polaridade escrita</span>
          <p>Antes da ligação agendada, uma única mensagem: "Márcia, aqui é o [nome], da SimedPrev. Às 15h eu te ligo como combinado — vou reservar esse horário todo pra entender o seu caso com calma. Qualquer coisa que a senhora quiser adiantar (quantas pessoas na família, se alguém faz acompanhamento médico), pode me mandar aqui." Isso posiciona a ligação como uma <b>consulta agendada</b>, não como telemarketing — e ainda começa o diagnóstico antes do "alô".</p>
        </div>
        <div class="sp-box sp-box--dark">
          <span class="lbl">Objetivo cumprido quando…</span>
          <p>O cliente aceitou o formato da conversa ("Fechado") e entendeu que você conduz. Se ele ainda está no comando — apressando, interrogando, dispersando — não avance: reafirme o formato com calma e só então entre no diagnóstico.</p>
        </div>
        <h4>Os 5 erros de abertura mais comuns (e o que comunicam)</h4>
        <div class="doc-table-wrap">
          <table>
            <thead><tr><th>Erro</th><th>O que o cliente decodifica</th></tr></thead>
            <tbody>
              <tr><td>"Oi, tudo bem?? Obrigado pelo seu tempinho!"</td><td>"Ele precisa mais de mim do que eu dele." O bastão já é do cliente.</td></tr>
              <tr><td>Pedir desculpa por ligar / "vou ser rapidinho"</td><td>"Nem ele acha que essa conversa vale meu tempo."</td></tr>
              <tr><td>Começar apresentando a empresa e o produto</td><td>"Lá vem o telemarketing." Defesas ativadas, respostas monossilábicas.</td></tr>
              <tr><td>Ler script com voz de atendente</td><td>"É produção em massa — não é sobre mim." Zero personalização percebida.</td></tr>
              <tr><td>Não combinar o formato da conversa</td><td>Sem contrato de condução, o cliente interroga, apressa e dispersa a call inteira.</td></tr>
            </tbody>
          </table>
        </div>
        <div class="sp-secfoot"><span><b>Parte 3</b> · Estágio 1 — Abertura</span><span>Uso interno · SimedPrev Saúde</span></div>
      </section>

      <!-- ===== ESTÁGIO 2 ===== -->
      <section id="s5">
        <p class="sp-part-eyebrow">05 · Parte 3 · Estágio 2</p>
        <h2>E2 — Diagnóstico: o arquétipo do psicólogo</h2>
        <p>O mercado trata o diagnóstico como um quebra-gelo de dois minutos antes de "apresentar os planos". O método ensina o oposto: o diagnóstico é <b>o coração da call</b> — a etapa mais longa da conversa, em que não se apresenta absolutamente nada. É ali que a venda acontece.</p>
        <h4>Por que o psicólogo?</h4>
        <p>Qual é o profissional para quem as pessoas mais se abrem? O psicólogo. E como ele se porta? Atento, caderno na mão, perna cruzada, sem julgamento, devolvendo perguntas. O primeiro objetivo do diagnóstico é <b>fazer o cliente se abrir</b> — e o corpo (ou a voz) do closer é a ferramenta que autoriza essa abertura.</p>
        <h4>A regra 7-38-55 — e como ela muda no telefone</h4>
        <p>Os estudos clássicos do psicólogo Albert Mehrabian (UCLA, 1967) sobre a comunicação de sentimentos e atitudes chegaram à proporção famosa: <b>7% palavras, 38% tom de voz, 55% expressão corporal</b>. Você "fala" o tempo todo — inclusive quando está calado. No nosso canal (telefone/WhatsApp), o componente visual desaparece — e o tom de voz herda quase todo o peso:</p>
        <div class="sp-meh">
          <p class="cap">Presencial / vídeo</p>
          <div class="sp-meh-bar">
            <div class="sp-meh-seg s1" style="width:7%">7%</div>
            <div class="sp-meh-seg s2" style="width:38%">TOM DE VOZ · 38%</div>
            <div class="sp-meh-seg s3" style="width:55%">EXPRESSÃO CORPORAL · 55%</div>
          </div>
          <p class="cap">Telefone / WhatsApp (nossa operação)</p>
          <div class="sp-meh-bar">
            <div class="sp-meh-seg s1" style="width:15%">≈15% palavras</div>
            <div class="sp-meh-seg s2" style="width:85%">TOM DE VOZ, RITMO, PAUSAS E ESCUTA ATIVA · ≈85%</div>
          </div>
        </div>
        <p class="sp-figcap">Proporções de Mehrabian válidas para comunicação de sentimentos/atitudes — exatamente o terreno onde a decisão de compra é tomada.</p>
        <p class="sp-figcap">Figura 3 — No telefone, o tom carrega a call: energia, pausas estratégicas e os sinais de escuta ("aham", "entendi", silêncio atento) substituem a linguagem corporal.</p>
        <div class="sp-box sp-box--teal">
          <span class="lbl">Tradução do "corpo de psicólogo" para o telefone</span>
          <p><b>Escuta audível:</b> "aham", "entendi", "caramba…" nos momentos certos — o cliente precisa ouvir que você está inclinado pra frente.</p>
          <p><b>Pausa pós-resposta:</b> espere 1–2 segundos depois que ele termina; o silêncio atento puxa mais verdade ("…e na real, doutor, tem também a questão do meu marido…").</p>
          <p><b>Anote de verdade e diga que anota:</b> "Peraí, deixa eu anotar isso." Nada comunica importância como alguém registrando sua vida.</p>
          <p><b>Espelhe a última frase:</b> "Três meses esperando a consulta…?" — a repetição em tom de pergunta reabre a camada.</p>
        </div>

        <h3>E2 — Descer camadas: os níveis de consciência</h3>
        <p>Diagnosticar é descer camadas. Na superfície, o cliente diz o que acha que quer ("quero um plano barato"). Nas camadas fundas está o que ele realmente precisa — inclusive coisas que <b>ele não sabe que não sabe</b>. É lá que mora a venda.</p>
        <div class="sp-pyr">
          <div class="sp-pyr-l l1"><span class="t">Sabe que sabe</span><span class="d">"Farmácia tá cara." · "Consulta particular é R$ 250."</span></div>
          <div class="sp-pyr-l l2"><span class="t">Sabe que não sabe</span><span class="d">"Plano de saúde deve ser caro demais pra mim." (nunca cotou)</span></div>
          <div class="sp-pyr-l l3"><span class="t">Não sabe que não sabe</span><span class="d">"Você já gasta R$ 364/mês com saúde — sem ter nada em troca."</span></div>
        </div>
        <p class="sp-figcap">Figura 4 — A venda acontece na camada de baixo: quando o closer mostra ao cliente algo do próprio problema que ele não via, deixa de ser vendedor e vira autoridade.</p>
        <h4>O teste do diagnóstico bem feito</h4>
        <div class="sp-cards">
          <div class="sp-card sp-card--red"><h5>Diagnóstico raso</h5><p>No final, o closer pergunta: "É isso mesmo, né? Te entendi?" e o cliente responde: <b>"É isso aí mesmo."</b> Péssimo sinal: vocês têm o mesmo nível de consciência — e ninguém paga por quem sabe o mesmo que ele.</p></div>
          <div class="sp-card sp-card--teal"><h5>Diagnóstico profundo</h5><p>No final, é o <b>cliente</b> quem diz: <b>"Caramba… isso é muito pior do que eu imaginava. Eu não sabia disso."</b> Você abriu o capô, mostrou o problema que ele não via — agora ele precisa de você.</p></div>
        </div>
        <h4>As ferramentas de investigação</h4>
        <ul>
          <li><b>"Deixa eu ver se eu entendi…"</b> — repita o que o cliente disse, propositalmente simplificado. Ele corrige, completa e desce mais uma camada. Fazer-se de desentendido é técnica, não fraqueza.</li>
          <li><b>Volte ao ponto mal explicado.</b> "Calma, volta um pouquinho — quando tu disse que a tua mãe adiou o exame, foi por quê?" Os detalhes evitados são as dores maiores.</li>
          <li><b>Nunca fale do produto no diagnóstico.</b> "Ah, porque aqui a gente tem uma rede de 40 clínicas…" — errado. O diagnóstico é sobre a pessoa, não sobre nós. Produto agora = cliente esfria e você vira vendedor de novo.</li>
          <li><b>Leia o contexto financeiro sem perguntar.</b> No alto ticket, lê-se o relógio, a roupa, a história do negócio. No nosso caso: bairro, profissão, quantos filhos, onde se consulta hoje, se paga farmácia à vista ou parcelado. Você monta o mapa de capacidade de pagamento sem nunca constranger.</li>
        </ul>

        <h3>E2 — O roteiro de diagnóstico do convênio médico</h3>
        <p>No nosso ticket, o diagnóstico é comprimido — mas continua sendo a etapa mais longa da call: <b>8 a 12 minutos</b> de investigação verdadeira, muito mais do que qualquer concorrente dedica. A estrutura de camadas é a mesma do alto ticket. Estas são as perguntas, na ordem:</p>
        <div class="doc-table-wrap">
          <table>
            <thead><tr><th>Camada</th><th>Perguntas</th><th>O que você está extraindo</th></tr></thead>
            <tbody>
              <tr><td><b>1 · Cenário da família</b> (2 min)</td><td>"Quem mora com a senhora hoje? Idades?" · "Alguém faz acompanhamento médico — pressão, diabetes, criança pequena?"</td><td>Nº de vidas, dependentes, urgências latentes. Cada nome citado vira argumento no Pit 01.</td></tr>
              <tr><td><b>2 · Como resolvem saúde hoje</b> (3 min)</td><td>"Hoje, quando alguém adoece, o que a senhora faz? Posto, particular, farmácia?" · "Quanto tempo levou a última consulta que precisou?" · "Já teve plano? Por que saiu?"</td><td>A dor operacional: fila, demora, desamparo. E o histórico com planos (preço? carência? decepção?).</td></tr>
              <tr><td><b>3 · A conta que ele nunca fez</b> (3 min)</td><td>"Na última ida à farmácia, gastou quanto?" · "E consulta particular, pagou quanto na última?" · "Somando farmácia, consulta e exame — quanto acha que a família gasta por mês?"</td><td>O número dele (normalmente subestimado). Você anota e devolve: "A média das famílias aqui da região é R$ 364/mês. A senhora tá perto disso?" — camada "não sabia que não sabia".</td></tr>
              <tr><td><b>4 · A dor com data</b> (2 min)</td><td>"Tem alguma coisa que a senhora ou alguém da casa vem adiando — exame, dentista, check-up?" · "Adiando por quê?"</td><td>Urgência concreta. "Minha filha precisa de exame de sangue" é o gatilho que fecha no mesmo dia.</td></tr>
              <tr><td><b>5 · O decisor e o dinheiro</b> (1–2 min)</td><td>"Essas decisões da casa a senhora resolve ou decide junto com alguém?" · (ler sinais de forma de pagamento ao longo da conversa)</td><td>Antecipe AGORA a objeção do cônjuge e a forma de pagamento — nunca deixe para o final.</td></tr>
            </tbody>
          </table>
        </div>
        <div class="sp-box sp-box--dark">
          <span class="lbl">Objetivo cumprido quando…</span>
          <p>Você tem: nº de vidas, gasto mensal atual dele em reais, uma dor com nome e data, quem decide, e um cliente que acabou de dizer alguma versão de "nossa, nunca tinha parado pra fazer essa conta". Sem esses cinco itens, não avance ao Pit 01.</p>
        </div>
        <div class="sp-secfoot"><span><b>Parte 3</b> · Estágio 2 — Diagnóstico</span><span>Uso interno · SimedPrev Saúde</span></div>
      </section>

      <!-- ===== ESTÁGIO 3 ===== -->
      <section id="s6">
        <p class="sp-part-eyebrow">06 · Parte 3 · Estágio 3</p>
        <h2>E3 — Pit 01: o compromisso antes do preço</h2>
        <p>Esta é a inovação central do método — a camada que quase nenhum closer conhece. O Pit 01 é um "pitch que o cliente não precisa pagar": antes de apresentar qualquer solução, você faz o cliente se comprometer com ele mesmo a resolver o problema que o diagnóstico revelou.</p>
        <div class="sp-quote">
          <p>"É uma loucura: o vendedor passa a call inteira sem nunca colocar o cara de frente com ele mesmo — e a única vez que coloca, está apresentando produto e pedindo dinheiro. Óbvio que o cara dá fuga. No fim, ele nunca sabe se eu quero ajudar ou se quero o dinheiro dele. No Pit 01 não: é sobre ele. Eu não vendi nada ainda."</p>
          <span class="cap">A lógica do Pit 01</span>
        </div>
        <h4>A sequência do Pit 01 (6 passos)</h4>
        <ol class="plain">
          <li><b>Recapitule o cenário com as palavras dele.</b> "Deixa eu juntar o que tu me falou: vocês são quatro, tua mãe toma remédio de pressão todo mês, a consulta da tua filha demorou três meses no posto, e vocês gastam uns R$ 350 por mês nisso tudo…"</li>
          <li><b>Projete a consequência de não agir.</b> "…e do jeito que tá, o próximo aperto vai ser igual: fila, farmácia cara e o susto do particular. Pode ser que dê tudo certo — mas, se não der, não vai ser porque tu não sabia."</li>
          <li><b>Confirme a relevância.</b> "Isso é relevante pra ti resolver? Por quê?" — e deixe o cliente falar. Quanto mais ele argumenta, mais ele se vende.</li>
          <li><b>Cheque o plano B.</b> "E se não resolver por esse caminho — tu tem algum outro plano pra isso?" (Quase nunca tem. Ouvir "não" da boca dele fecha a porta da fuga futura.)</li>
          <li><b>Meça a predisposição — a pergunta certa.</b> "De 0 a 10, o quanto tu quer resolver isso?" — atenção: o quanto ele QUER resolver, não o quanto quer o resultado (do resultado todo mundo quer 10). Se vier um "6", confronte com carinho: "Diante de tudo que tu me contou… tu quer 6?"</li>
          <li><b>Antecipe as objeções finais — todas.</b> Dinheiro, decisor, prioridade e concorrência são tratados AQUI, quando ainda não há preço na mesa (detalhe nas páginas seguintes).</li>
        </ol>

        <h3>E3 — A congruência em ação: o Pit 01 na nossa call</h3>
        <div class="sp-box sp-box--dark">
          <span class="lbl">O princípio psicológico: congruência</span>
          <p>O ser humano odeia parecer incongruente. Se o cliente acabou de dizer, com a própria boca, que o problema é sério, que é relevante, que não tem plano B e que quer resolver "10" — ele não consegue, minutos depois, dizer "vou pensar" sem se contradizer. É como sair correndo do hospital por causa do preço do remédio depois de descrever a dor ao médico: incongruente. O Pit 01 constrói a congruência que o fechamento vai cobrar.</p>
        </div>
        <div class="sp-script">
          <span class="lbl">Script E3 · Pit 01 — SimedPrev Saúde</span>
          <p><span class="who">CLOSER:</span> Márcia, deixa eu juntar tudo que a senhora me contou. São quatro pessoas na casa. O Pedro tem 6 anos e vive com dor de garganta; a última consulta dele a senhora esperou quase dois meses. A sua mãe compra remédio de pressão todo mês na farmácia, sem desconto nenhum. E quando a gente somou, a senhora se assustou: passa de R$ 300 por mês só de farmácia e consulta avulsa. Foi isso?</p>
          <p><span class="who who--cli">CLIENTE:</span> É… é isso mesmo.</p>
          <p><span class="who">CLOSER:</span> Então me responde com sinceridade: resolver isso — ter onde levar o Pedro na semana que ele precisar, e parar de pagar o preço cheio da farmácia — é relevante pra senhora ou dá pra continuar como tá?</p>
          <p><span class="who who--cli">CLIENTE:</span> Não, do jeito que tá não dá…</p>
          <p><span class="who">CLOSER:</span> De 0 a 10, o quanto a senhora QUER resolver isso agora?</p>
          <p><span class="who who--cli">CLIENTE:</span> Ah… 9, 10.</p>
          <p><span class="who">CLOSER:</span> Perfeito. Última coisa antes de eu te mostrar o que eu tenho: se eu te apresentar algo que resolve exatamente isso e que cabe no seu bolso, tem mais alguém que precisa participar dessa decisão, ou a senhora resolve?</p>
          <p class="obs">O compromisso está firmado ANTES do preço existir. Toda fuga do final acaba de ficar incongruente.</p>
        </div>
        <div class="sp-box sp-box--teal">
          <span class="lbl">Por que isso muda a taxa de conversão</span>
          <p>Quando o preço aparecer, lá no Pit 02, ele não vai cair sobre um estranho desconfiado — vai cair sobre alguém que acabou de declarar, em voz alta, que tem um problema sério, que quer resolvê-lo agora, que não tem plano B e que teria como pagar. Contra esse conjunto de declarações, a fuga do final não tem onde se apoiar. É por isso que quem aplica o Pit 01 relata que o fechamento "fica fácil": o trabalho pesado já foi feito aqui.</p>
        </div>

        <h3>E3 — A antecipação de objeções: o cofre do Pit 01</h3>
        <p>80% das calls morrem no fim por três frases: <em>"preciso ver o dinheiro"</em>, <em>"preciso falar com meu marido/esposa"</em>, <em>"agora não é prioridade"</em>. O closer faixa-preta mata as três <b>antes de mostrar o preço</b> — quando confrontar ainda não parece pressão de venda.</p>
        <div class="doc-table-wrap">
          <table>
            <thead><tr><th>Objeção futura</th><th>Como antecipar no Pit 01 (sem preço na mesa)</th></tr></thead>
            <tbody>
              <tr><td>💰 <b>Dinheiro</b></td><td>"Vou ser sincero contigo: faz sentido eu te mostrar algo que tu não tem como pagar? Não, né — eu tomaria teu tempo à toa. Então me diz: se fizer sentido, o pagamento hoje seria como — Pix, cartão?" → O cliente que responde "tenho, seria no Pix" acabou de eliminar a objeção financeira do final. Regra do método: "Já era — ele não pode mais me dizer no fim que não tem grana."</td></tr>
              <tr><td>👥 <b>Decisor (cônjuge/sócio)</b></td><td>"Essa decisão a senhora toma ou decide com alguém?" Se decide junto: "Perfeito — ele consegue participar da nossa conversa agora? Posso esperar." Ou: "O que exatamente ele precisaria ouvir pra dizer não a isso que a senhora acabou de me dizer que precisa?" → Traga o decisor pra DENTRO da call, nunca para depois dela.</td></tr>
              <tr><td>🕐 <b>Prioridade</b></td><td>A escala 0-10 + plano B já resolveram. Quem disse "quero 10" e "não tenho outro plano" não consegue dizer "não é prioridade" vinte minutos depois.</td></tr>
              <tr><td>🔎 <b>"Tô pesquisando outros"</b></td><td>"Ótimo — o que exatamente tu tá comparando? … E algum deles fez com você essa análise que a gente acabou de fazer, ou já chegaram te empurrando plano? … Tu busca resolver isso ou busca pagar barato? Porque se for pra pagar barato, eu já te digo: não é comigo." → Isola a concorrência pelo diagnóstico, não pelo preço.</td></tr>
            </tbody>
          </table>
        </div>
        <div class="sp-box sp-box--red">
          <span class="lbl">O filtro do restaurante</span>
          <p>"Imagina que eu te convido pra jantar, tu abre o cardápio e descobre que não tem dinheiro pra comer ali. Tu gostaria que eu te levasse num restaurante desses?" — Se a resposta do cliente à pergunta do dinheiro revelar que ele <b>não tem</b> nem terá capacidade de pagamento, o método manda: <b>encerre a call com respeito</b>. "Então a gente não avança hoje, e tá tudo bem — não vou te mostrar uma coisa pra te deixar na vontade." Tempo é o único recurso que não volta; conversão é eficiência.</p>
        </div>
        <div class="sp-box sp-box--teal">
          <span class="lbl">Aplicação SimedPrev — o filtro quase nunca elimina</span>
          <p>No nosso ticket, a pergunta do restaurante muda de função: R$ 89/mês pela família inteira raramente é impagável — mas a pergunta continua obrigatória, porque ela <b>revela a forma de pagamento</b> (Pix, cartão, débito) e o dia do mês em que a família tem dinheiro (dia 5? dia 10?). O closer usa isso no fechamento: "A senhora me disse que o dinheiro entra dia 5 — vamos deixar a cobrança pro dia 5." Objeção de vencimento morta antes de nascer.</p>
        </div>
        <div class="sp-box sp-box--dark">
          <span class="lbl">Objetivo cumprido quando…</span>
          <p>O cliente declarou: relevância ("preciso resolver"), predisposição (nota alta), ausência de plano B, quem decide (e o decisor está na call ou neutralizado) e como pagaria. Só então — e nunca antes — você apresenta a solução.</p>
        </div>
        <div class="sp-secfoot"><span><b>Parte 3</b> · Estágio 3 — Pit 01</span><span>Uso interno · SimedPrev Saúde</span></div>
      </section>

      <!-- ===== ESTÁGIO 4 ===== -->
      <section id="s7">
        <p class="sp-part-eyebrow">07 · Parte 3 · Estágio 4</p>
        <h2>E4 — Apresentação: entregável → benefício → dor</h2>
        <p>Agora — e só agora — o cliente está pronto para receber uma solução. Faz sentido falar de emagrecimento com quem não quer emagrecer? Não. Então a apresentação só existe para quem passou pelo Pit 01. E ela é curta, personalizada e cirúrgica.</p>
        <h4>A fórmula de cada frase da apresentação</h4>
        <div class="sp-pit">
          <div class="sp-pit-card c1"><span class="t">Entregável</span><p>o que o produto tem</p></div>
          <div class="sp-pit-card c2"><span class="t">Benefício</span><p>o que isso gera pra ELE</p></div>
          <div class="sp-pit-card c3"><span class="t">Dor do diagnóstico</span><p>a frase que ELE disse</p></div>
        </div>
        <p class="sp-figcap">Figura 5 — Nunca apresente entregável solto ("temos 40 clínicas"). Sempre a cadeia completa, terminando na dor que o próprio cliente verbalizou.</p>
        <div class="doc-table-wrap">
          <table>
            <thead><tr><th>❌ Característica solta</th><th>✅ Entregável → benefício → dor</th></tr></thead>
            <tbody>
              <tr><td>"O cartão dá acesso a mais de 40 clínicas."</td><td>"A senhora vai ter mais de 40 clínicas aqui na região — ou seja, na semana que o Pedro amanhecer com dor de garganta, a senhora liga e agenda, sem repetir aqueles dois meses de espera que me contou."</td></tr>
              <tr><td>"Tem desconto de 15 a 30% em farmácia."</td><td>"O remédio de pressão da sua mãe, que hoje sai preço cheio todo mês, entra com desconto — só isso já morde boa parte da mensalidade."</td></tr>
              <tr><td>"São 800 exames a preço de custo."</td><td>"Aquele exame que a senhora vem adiando: pelo convênio sai a preço de custo — e como assinou hoje, a gente já solicita o agendamento agora, na própria call."</td></tr>
              <tr><td>"Tem extrato mensal de economia."</td><td>"E todo mês chega um extrato mostrando quanto a família economizou — a senhora não vai precisar acreditar em mim: vai ver o número."</td></tr>
            </tbody>
          </table>
        </div>
        <h4>Regras da apresentação</h4>
        <div class="sp-box sp-box--red">
          <span class="lbl">Frase proibida nº 1 do método</span>
          <p>"Faz sentido pra você?" / "É isso que você busca?" — cancele do vocabulário. O médico que te diagnostica não pergunta se a cirurgia "faz sentido": ele prescreve. Perguntar isso no final joga fora toda a autoridade construída e devolve o bastão ao cliente. Substitua por: <b>"Tu entendeu como isso resolve o teu caso? O que tu entendeu?"</b></p>
        </div>
        <ul>
          <li><b>Apresente só o que resolve a dor dele.</b> Se o produto tem 10 entregáveis e 3 resolvem o caso, fale dos 3. O resto é "encher linguiça" — aumenta o "balelômetro", esfria o cliente e desconecta. (Academia e lazer só entram se apareceram no diagnóstico.)</li>
          <li><b>Checkpoint de engajamento a cada bloco:</b> "Tu entendeu como isso resolve aquilo que tu me falou? O QUE tu entendeu?" — Fazer o cliente explicar com as palavras dele é fazê-lo se vender. E se ele resumir errado ("ah, é um cartão de desconto"), corrija na hora: "Não! Isso não é um cartãozinho de desconto — é a família inteira, pais e filhos, com porta aberta em 40 clínicas sem fila, a preço de custo."</li>
          <li><b>A confiança nasce em você, não no cliente.</b> "Confia: isso aqui resolve o que a senhora me trouxe." Dito com a firmeza de quem fez um diagnóstico profundo — porque fez.</li>
        </ul>
        <div class="sp-secfoot"><span><b>Parte 3</b> · Estágio 4 — Apresentação</span><span>Uso interno · SimedPrev Saúde</span></div>
      </section>

      <!-- ===== ESTÁGIO 5 ===== -->
      <section id="s8">
        <p class="sp-part-eyebrow">08 · Parte 3 · Estágio 5</p>
        <h2>E5 — Pit 02: ancoragem, tabela e condição única</h2>
        <p>O Pit 02 é o pitch oficial — o momento do preço. São <b>três camadas em sequência</b>: valor (ancoragem), preço cheio (tabela) e, só se merecida, a condição única. Quem mostra preço sem ancorar vende número; quem ancora vende diferença.</p>
        <div class="sp-pit">
          <div class="sp-pit-card c1"><span class="t">1 · Ancoragem</span><p>Empilhe o valor de mercado de cada entregável, um a um.</p><p>"Isso é o que VALE."</p><p><b>Pergunta-chave: "Vale ou não vale?"</b></p><p>Sem o "vale" dele, não mostre preço.</p></div>
          <div class="sp-pit-card c2"><span class="t">2 · Preço de tabela</span><p>O preço real, sustentado com naturalidade.</p><p>Dá margem de barganha — e muita gente fecha aqui.</p><p><b>"Quer fazer no Pix ou no cartão?"</b></p></div>
          <div class="sp-pit-card c3"><span class="t">3 · Condição única</span><p>Só para quem jogou limpo. Só DENTRO da call.</p><p>Se desceu pra condição única, o pagamento (ou sinal) é na hora. Sem exceção.</p><p><b>A call precisa ter um motivo para comprar AGORA.</b></p></div>
        </div>
        <h4>A ancoragem — o empilhamento de valor</h4>
        <p>O empilhamento usa os preços que a família conhece do balcão: consulta de <b>R$ 250</b> por <b>R$ 150</b>; ressonância de <b>R$ 950</b> por <b>R$ 550</b>; ultrassom de <b>R$ 180</b> por <b>R$ 90</b> — serviço por serviço que apareceu no diagnóstico. Some a economia do ano na frente do cliente e só então pergunte: <em>"vale ou não vale?"</em> Só depois do "vale" vem o preço — que, ao lado da economia empilhada, parece pequeno.</p>
        <div class="sp-script">
          <span class="lbl">Script E5 · Ancoragem SimedPrev Saúde (números reais da nossa região)</span>
          <p><span class="who">CLOSER:</span> Márcia, olha a conta. A consulta do Pedro: R$ 250 no balcão, R$ 150 pelo convênio — R$ 100 de economia por consulta. O ultrassom que a senhora adia: R$ 180 lá fora, R$ 90 aqui. Uma ressonância, se precisar: R$ 950 no particular, R$ 550 pelo convênio. E vale pra senhora, pro seu marido e pros dois pequenos — a família inteira. Só com o que a senhora me contou que usa num ano, a economia passa fácil de R$ 1.500. Concorda com essa conta?</p>
          <p><span class="who who--cli">CLIENTE:</span> Nossa… fazendo assim, dá isso mesmo.</p>
          <p><span class="who">CLOSER:</span> Então me diz: consultas, exames, procedimentos e até cirurgia com internação e anestesista inclusos, tudo a preço de custo, pra família inteira, com a gente agendando — vale ou não vale?</p>
          <p><span class="who who--cli">CLIENTE:</span> Vale, claro.</p>
          <p><span class="who">CLOSER:</span> Então vamos lá: o convênio é R$ 119 por mês, sem fidelidade nenhuma — a senhora cancela quando quiser. Agora, se a senhora fechar o plano anual, cai pra R$ 89 por mês pra família toda — menos de R$ 3 por dia. A adesão é de R$ 99, paga uma vez só, e já sai daqui com o primeiro agendamento feito. A senhora quer fazer no Pix ou no cartão?</p>
          <p class="obs">Empilhou a economia → "vale ou não vale?" → tabela (R$ 119 sem fidelidade / R$ 89 anual + adesão R$ 99) → "Pix ou cartão?". A condição única de R$ 49,90 NÃO aparece aqui — é a carta da manga do quadro abaixo.</p>
        </div>
        <div class="sp-box sp-box--red">
          <span class="lbl">A regra da condição única — R$ 49,90</span>
          <p>A condição única é <b>R$ 49,90/mês (adesão R$ 49,90)</b> — e NÃO é oferta de abertura: só desce para ela quem tentou contornar ("tá caro", "vou pesquisar") depois de um Pit 01 bem feito. É carta única, jogada <b>dentro da call</b>: quem "vai pensar" volta amanhã para a tabela (R$ 119 / R$ 89). Uso interno: R$ 49,90 é também o valor de retenção — cliente de R$ 119/R$ 89 que pedir cancelamento recebe os mesmos R$ 49,90 para ficar.</p>
        </div>
        <div class="sp-secfoot"><span><b>Parte 3</b> · Estágio 5 — Pit 02</span><span>Uso interno · SimedPrev Saúde</span></div>
      </section>

      <!-- ===== ESTÁGIO 6 ===== -->
      <section id="s9">
        <p class="sp-part-eyebrow">09 · Parte 3 · Estágio 6</p>
        <h2>E6 — Fechamento: toda objeção é uma fuga</h2>
        <p>Em call de vendas, não existe "não" — o cliente que não quer diz "vou pensar". Se não existe não, toda objeção do final é, por definição, uma mentira educada: uma fuga. A primeira regra do fechamento é não aceitar a objeção como verdade — e investigar o que ela esconde.</p>
        <div class="sp-quote">
          <p>"A objeção é uma fuga. Eu preciso encontrar a verdadeira realidade daquilo. — 'Joga limpo comigo: não é o teu financeiro. O que é de verdade?'"</p>
          <span class="cap">Tensão construtiva aplicada à objeção</span>
        </div>
        <h4>Os três níveis de fechamento</h4>
        <div class="doc-table-wrap">
          <table>
            <thead><tr><th>Nível</th><th>Frase</th><th>Avaliação</th></tr></thead>
            <tbody>
              <tr><td><b>Faixa branca</b></td><td>"E aí, vamos fechar? O que você acha?"</td><td>Pergunta aberta — devolve o bastão e convida a fuga.</td></tr>
              <tr><td><b>Intermediário</b></td><td>"Como prefere fazer: Pix ou cartão?"</td><td>Alternativa — pressupõe o sim; escolhe-se só o meio.</td></tr>
              <tr><td><b>Faixa preta</b></td><td>"A senhora me falou que o dinheiro entra dia 5 e que usa Pix — então faz assim: a adesão de R$ 99 a senhora faz agora no Pix, e a mensalidade de R$ 89 já deixo agendada pro dia 5."</td><td>Usa a informação de pagamento colhida no Pit 01. Não pergunta se — executa o combinado.</td></tr>
            </tbody>
          </table>
        </div>
        <h4>O sinal: compromisso vale mais que o valor</h4>
        <p>Na nossa operação, o "sinal" é a adesão. Contrato sem dinheiro na conta "não quer dizer nada" — conversão real é pagamento. A adesão (R$ 99) paga na call é o que sela o compromisso: cliente que pagou a adesão está fechado; cliente que "assina e paga depois" é cliente de follow-up — e follow-up não existe. Se o cliente não pode pagar tudo hoje, a adesão no Pix agora + mensalidade agendada para o dia em que o dinheiro entra resolve.</p>
        <ul>
          <li><b>Empresa pagando para funcionários.</b> Quando quem contrata é uma empresa (benefício para a equipe), o interlocutor é o dono ou o RH: fecha-se a condição e o compromisso na call — lista de funcionários, contrato e data de pagamento definidos ali — e o pagamento segue o fluxo do financeiro da empresa. Pergunta-chave: "O que o teu sócio pode dizer que derrubaria isso que TU, responsável pela área, já aprovou?" O que não pode: sair da call sem data, sem lista e sem responsável.</li>
          <li><b>"Preciso falar com meu marido/esposa" no fechamento.</b> Se chegou até aqui, o Pit 01 falhou em mapear o decisor — corrija agora, não amanhã: traga o cônjuge para a call (script abaixo).</li>
        </ul>

        <h3>E6 — Scripts de fechamento: cônjuge e cartão</h3>
        <div class="sp-script">
          <span class="lbl">Script E6 · Trazendo o cônjuge para dentro da call</span>
          <p><span class="who who--cli">CLIENTE:</span> Eu gostei, mas preciso falar com meu marido antes.</p>
          <p><span class="who">CLOSER:</span> Claro, Márcia — decisão da família se toma junto. Ele tá em casa agora? … Então faz assim: coloca no viva-voz, que em dois minutos eu explico pra ele o que a gente montou — é melhor do que a senhora ter que repetir tudo sozinha depois.</p>
          <p><span class="who who--cli">CLIENTE:</span> Ele não tá agora…</p>
          <p><span class="who">CLOSER:</span> Sem problema. Me responde uma coisa: diante de tudo que a gente viu — a consulta do Pedro, o ultrassom, a economia da família — o que exatamente ele precisaria ouvir pra dizer não? … Então vamos garantir que ele ouça isso direito: hoje às 19h vocês dois estão em casa? Eu ligo, cinco minutos, e a gente resolve juntos. Fechado às 19h?</p>
          <p class="obs">A call a três é agendada DENTRO da call, com dia e hora — nunca "me liga depois que eu falar com ele". E o compromisso da cliente com o horário já é um mini-fechamento.</p>
        </div>
        <div class="sp-script">
          <span class="lbl">Script E6 · Matando o "vou ver e te falo amanhã" (cartão)</span>
          <p><span class="who who--cli">CLIENTE:</span> Deixa eu ver meu cartão e amanhã te confirmo.</p>
          <p><span class="who">CLOSER:</span> Ver o quê exatamente do cartão, Márcia? É limite? O cartão é seu ou de outra pessoa?</p>
          <p><span class="who who--cli">CLIENTE:</span> É meu… é mais o limite mesmo.</p>
          <p><span class="who">CLOSER:</span> E a senhora acredita que tem esse limite ou não tem?</p>
          <p><span class="who who--cli">CLIENTE:</span> Acho que tenho.</p>
          <p><span class="who">CLOSER:</span> Então faz assim: abre o aplicativo aí agora comigo na linha — leva um minutinho — e a gente já resolve isso juntos. Se não tiver limite, eu mesmo te falo e a gente vê o Pix. Fechado?</p>
          <p class="obs">Tudo que aconteceria "amanhã" no follow-up é puxado para DENTRO da call — com tom de quem ajuda, não de quem pressiona.</p>
        </div>
        <div class="sp-box sp-box--dark">
          <span class="lbl">Regra comum aos dois scripts</span>
          <p>Nada sai da call sem virar compromisso com dia e hora: ou o pagamento acontece agora, ou a call a três está marcada ("hoje às 19h"), ou o app do cartão foi aberto na linha. Pendência exportada para "amanhã" é venda perdida disfarçada de educação.</p>
        </div>

        <h3>E6 — A morte do follow-up e o "diz: não quero"</h3>
        <p>O mercado repete que "follow-up é rei". O método responde: follow-up é rei para quem ignora os sinais dentro da call. A definição que muda tudo: <b>follow-up é uma sequência de ações que não foram lapidadas dentro da call.</b></p>
        <div class="sp-cards">
          <div class="sp-card sp-card--red"><h5>O ciclo do follow-up</h5><p>"Vou ver o cartão" → amanhã: "não tinha limite" → "e uma entrada?" → "também não consegui" → semana que vem → nunca. Cada follow-up é uma pendência que o closer aceitou terceirizar para longe da sua presença — onde o medo do cliente decide sozinho.</p></div>
          <div class="sp-card sp-card--teal"><h5>A lapidação na call</h5><p>Todo "depois eu vejo" vira pergunta agora: "Ver o quê? Com quem? O que pode dar errado? Quer abrir o app agora?" Quando a call termina, não existe pendência — existe cliente pago ou decisão declarada. "Cliente que ficou de follow não fechou: ele não fechou."</p></div>
        </div>
        <h4>Não existe "proposta" — existe o teu caso</h4>
        <p>Se o cliente diz "gostei da sua proposta, me manda por e-mail", corrija na hora: "Eu não te apresentei uma proposta — a gente passou essa call inteira falando do TEU caso, e o que eu te mostrei resolve o que TU me disse que precisa. Não tem o que analisar num PDF: tem uma decisão sobre a tua família, e ela é tua." Proposta se arquiva; caso se resolve.</p>
        <h4>O fechamento "diz: não quero" — o último recurso</h4>
        <div class="sp-script">
          <span class="lbl">Script E6 · Último recurso (usar com tom de acolhimento, nunca de raiva)</span>
          <p><span class="who who--cli">CLIENTE:</span> Ah, não sei… não consigo decidir agora…</p>
          <p><span class="who">CLOSER:</span> Márcia, então vamos fazer o seguinte. Olha pra tudo que a gente conversou — o Pedro, a espera de dois meses, a farmácia da sua mãe, os R$ 350 por mês que já saem do seu bolso — e me diz: "não quero resolver isso". Diz com a sua boca: "não quero". Porque se a senhora disser, eu encerro agora e a senhora nunca mais ouve falar de mim — e eu vou saber que fiz tudo que eu podia. Mas se a senhora não consegue dizer… é porque a senhora quer. E aí quem tá te segurando não sou eu: é o medo. Vamos resolver juntos?</p>
          <p class="obs">Quase ninguém consegue verbalizar o "não quero" olhando para a própria dor — a incongruência é insuportável. E quem diz "não quero" de verdade te libera para o próximo cliente: também é vitória.</p>
        </div>
        <div class="sp-box sp-box--teal">
          <span class="lbl">E quando o cliente sai sem comprar?</span>
          <p>Se o cliente está genuinamente em fase de pesquisa (declarado no Pit 01), o método permite deixá-lo ir sem nem apresentar preço: "Beleza — vai lá, pesquisa. Só leva uma coisa: alguém dos que tu vai conversar fez contigo a análise que a gente fez aqui?" Ele sai comparando todos com você — e todos vão parecer vendedores. Quando voltar (e volta), a call recomeça do compromisso, não do zero.</p>
        </div>
        <div class="sp-box sp-box--dark">
          <span class="lbl">Síntese do fechamento</span>
          <p>Conversão é dinheiro na conta. Contrato é intenção; pagamento é decisão. "Tu diz que quer viajar quando compra a passagem — não quando deixa tudo salvo nas abas do navegador."</p>
        </div>
        <div class="sp-secfoot"><span><b>Parte 3</b> · Estágio 6 — Fechamento</span><span>Uso interno · SimedPrev Saúde</span></div>
      </section>

      <!-- ================= PARTE 4 ================= -->
      <section id="s10">
        <p class="sp-part-eyebrow">10 · Parte 4</p>
        <h2>Aplicação Total: SimedPrev Saúde</h2>
        <p>Método sem contexto é palestra. Esta parte transforma tudo que você leu em ferramentas de trabalho da nossa operação: a matriz de objeções do convênio médico, a call modelo cronometrada do "alô" ao Pix, o checklist do closer e as métricas que vamos acompanhar.</p>
        <ol class="plain">
          <li><b>4.1</b> &nbsp;Os dois públicos e as duas aberturas (anúncio × base gratuita)</li>
          <li><b>4.2</b> &nbsp;O arsenal de valor — preços de custo que fecham vendas</li>
          <li><b>4.3</b> &nbsp;Matriz de objeções — as 9 da nossa operação</li>
          <li><b>4.4</b> &nbsp;A call modelo SimedPrev Saúde (ritmo e duração)</li>
          <li><b>4.5</b> &nbsp;Checklist · frases proibidas e obrigatórias · métricas</li>
          <li><b>4.7</b> &nbsp;Referências do material</li>
        </ol>

        <h3><span class="n">4.1</span> — Os dois públicos e as duas aberturas</h3>
        <p>Não temos pré-vendedores: é o closer quem liga, do primeiro "alô" ao pagamento. E os leads chegam por dois caminhos muito diferentes — o que exige duas aberturas diferentes. Errar o script para o público errado queima a call no primeiro minuto.</p>
        <div class="sp-cards">
          <div class="sp-card sp-card--red"><h5>Público A — Lead de anúncio</h5><p>Veio de campanha (Google/Meta), preencheu formulário, nunca usou o SimedPrev. Não conhece a rede nem os preços. A call parte do zero: diagnóstico completo, construção de valor do início. Desconfiança maior — a prova social local ("clínica X, que a senhora conhece") pesa muito.</p></div>
          <div class="sp-card sp-card--teal"><h5>Público B — Base gratuita SimedPrev</h5><p>Milhares de cadastros que já usam o SimedPrev sem pagar mensalidade — hoje pagam, em cada uso, o preço com taxa de intermediação. Já conhecem e confiam. A call não vende um produto novo: vende um upgrade com conta na mesa — pagar o preço de custo em tudo.</p></div>
        </div>
        <h4>A conta que sustenta as duas conversas: os três níveis de preço</h4>
        <div class="doc-table-wrap">
          <table>
            <thead><tr><th>Serviço (exemplos reais)</th><th>Balcão particular</th><th>SimedPrev gratuito (hoje)</th><th>Assinante SimedPrev Saúde (custo)</th></tr></thead>
            <tbody>
              <tr><td>Consulta com especialista</td><td>R$ 250</td><td>R$ 200</td><td>R$ 150</td></tr>
              <tr><td>Ressonância magnética</td><td>R$ 950</td><td>R$ 690</td><td>R$ 580</td></tr>
              <tr><td>Ultrassom</td><td>R$ 180</td><td>R$ 120</td><td>R$ 90</td></tr>
            </tbody>
          </table>
        </div>
        <p>O SimedPrev gratuito já é mais barato que o balcão (é assim que ele se sustenta hoje, pela taxa de intermediação). O assinante do SimedPrev Saúde elimina a taxa: paga o custo efetivo negociado com as clínicas parceiras — e leva junto agendamento centralizado, extrato de economia e a família inteira coberta.</p>
        <div class="sp-script">
          <span class="lbl">Abertura A · Lead de anúncio (nunca usou)</span>
          <p><span class="who">CLOSER:</span> Fala, Dona Márcia, tudo bem? Tá me ouvindo bem aí? Perfeito. Márcia, meu nome é [nome], sou especialista da SimedPrev Saúde, aqui de Itajaí. A senhora preencheu nosso formulário pedindo pra entender o convênio médico da família, certo? Então deixa eu te explicar como funciona a nossa conversa: primeiro te faço algumas perguntas pra entender como é a saúde da sua família hoje — porque o convênio é montado em cima disso. Se eu enxergar que faz sentido pro seu caso, te mostro exatamente como funciona e quanto fica. E se não fizer sentido, te falo com a mesma sinceridade. Fechado?</p>
        </div>
        <div class="sp-script">
          <span class="lbl">Abertura B · Base gratuita (já usa o SimedPrev)</span>
          <p><span class="who">CLOSER:</span> Fala, Dona Márcia, tudo bem? Aqui é o [nome], da SimedPrev — a senhora usou a gente na consulta com a Dra. [nome] há uns meses, lembra? Que bom. Márcia, eu tô ligando porque a senhora faz parte da nossa base e chegou uma condição que muda a sua conta: hoje, naquela consulta, a senhora pagou R$ 200. Existe agora o SimedPrev Saúde, em que a senhora passa a pagar o que a clínica cobra DO CONVÊNIO — os R$ 150, o preço de custo. Antes de te explicar os detalhes, deixa eu entender rapidinho como tá a saúde da família hoje, porque a conta muda de tamanho dependendo do que vocês usam. Pode ser?</p>
        </div>
        <p>O Público B dispensa apresentação da empresa — a confiança já existe. A força da call está na CONTA: puxe do histórico o que a pessoa já pagou e mostre, item a item, quanto teria pago como assinante. O diagnóstico continua obrigatório: é ele que revela o que a família ainda adia.</p>
        <div class="sp-secfoot"><span><b>Parte 4</b> · Os Dois Públicos</span><span>Uso interno · SimedPrev Saúde</span></div>

        <h3><span class="n">4.2</span> — O arsenal de valor: preços que fecham vendas</h3>
        <p>Estes números vêm das nossas tabelas reais de consultas, exames, procedimentos e cirurgias. Decore os que mais aparecem no diagnóstico — a ancoragem do Pit 02 é montada com eles. Coluna "hoje": preço atual SimedPrev (com taxa). Coluna "assinante": o custo efetivo que só o assinante paga.</p>
        <div class="doc-table-wrap">
          <table>
            <thead><tr><th>Serviço</th><th>Preço SimedPrev hoje</th><th>Assinante (custo)</th><th>Economia por uso</th></tr></thead>
            <tbody>
              <tr><td>Consulta com especialista (mediana da rede, 400+ opções)</td><td>R$ 210</td><td>R$ 155</td><td>R$ 55</td></tr>
              <tr><td>Hemograma completo</td><td>R$ 12</td><td>R$ 8</td><td>~30%</td></tr>
              <tr><td>Ultrassom pélvico / transvaginal</td><td>R$ 90–95</td><td>R$ 40–55</td><td>até R$ 50</td></tr>
              <tr><td>Raio X (por incidência)</td><td>R$ 90</td><td>R$ 70</td><td>R$ 20</td></tr>
              <tr><td>Mamografia digital bilateral</td><td>R$ 145</td><td>R$ 110</td><td>R$ 35</td></tr>
              <tr><td>Ressonância magnética (coluna, crânio, articulação)</td><td>R$ 560</td><td>R$ 440</td><td>R$ 120</td></tr>
              <tr><td>Colonoscopia</td><td>R$ 690</td><td>R$ 600</td><td>R$ 90</td></tr>
              <tr><td>Colocação de DIU Mirena</td><td>R$ 1.540</td><td>R$ 1.300</td><td>R$ 240</td></tr>
              <tr><td>Vasectomia</td><td>R$ 1.800</td><td>R$ 1.550</td><td>R$ 250</td></tr>
              <tr><td>Cirurgia de hérnia inguinal (unilateral)</td><td>R$ 3.700</td><td>R$ 3.200</td><td>R$ 500</td></tr>
              <tr><td>Cirurgia de varizes (bilateral)</td><td>R$ 8.200–8.500</td><td>R$ 7.000–7.500</td><td>até R$ 1.500</td></tr>
              <tr><td>Retirada de pedra na vesícula (por vídeo)</td><td>R$ 8.600</td><td>R$ 7.500</td><td>R$ 1.100</td></tr>
            </tbody>
          </table>
        </div>
        <p>Lembre: o preço "hoje" já é menor que o balcão particular (consulta R$ 250, ressonância R$ 950, ultrassom R$ 180…). Na call com o Público A, ancore no balcão; com o Público B, ancore no que ele já paga.</p>
        <div class="sp-box sp-box--dark">
          <span class="lbl">A vantagem que ninguém tem: cirurgia de verdade</span>
          <p>São mais de 120 cirurgias tabeladas — vesícula, hérnia, varizes, útero, vasectomia — com internação e anestesista já inclusos no preço. Cartão de desconto nenhum oferece isso; plano tradicional cobra R$ 1.500+/mês da família para isso. É o argumento que encerra a comparação com concorrente.</p>
        </div>
        <div class="sp-box sp-box--teal">
          <span class="lbl">Como usar na call</span>
          <p><b>(1)</b> No diagnóstico, anote cada serviço que a família usa ou adia. <b>(2)</b> Na ancoragem, empilhe a economia item a item, com os números desta página. <b>(3)</b> No fechamento, lembre: assinou, já pode solicitar o agendamento de consulta, exame, procedimento e até cirurgia — na mesma ligação. A rede também cobre fisioterapia, psicologia, fonoaudiologia e procedimentos ginecológicos, oftalmológicos e de otorrino.</p>
        </div>
        <div class="sp-secfoot"><span><b>Parte 4</b> · O Arsenal de Valor</span><span>Uso interno · SimedPrev Saúde</span></div>

        <h3><span class="n">4.3</span> — Matriz de objeções do convênio médico</h3>
        <p>Lembre da regra: objeção é fuga — o trabalho é achar a verdade por trás dela. E a melhor objeção é a que você antecipou no Pit 01 e nunca apareceu.</p>
        <div class="doc-table-wrap">
          <table>
            <thead><tr><th>Objeção</th><th>O que geralmente esconde</th><th>Resposta do método</th></tr></thead>
            <tbody>
              <tr><td><b>"Preciso falar com meu marido / minha esposa."</b></td><td>Medo de decidir sozinha ou fuga educada. (Se fosse real, apareceria no Pit 01.)</td><td>"Claro. Ele tá em casa agora? Coloca no viva-voz que eu explico pra ele em dois minutos — é melhor do que a senhora ter que repetir tudo." Se impossível: "O que exatamente ele precisaria ouvir pra dizer não a isso que a senhora me disse que a família precisa?" + agendar call a três com dia e hora NA linha.</td></tr>
              <tr><td><b>"Não tenho dinheiro agora."</b></td><td>Ou verdade (filtro falhou) ou medo de compromisso mensal.</td><td>"Entendo. Me ajuda com uma conta: a senhora me disse que gasta uns R$ 350 por mês com farmácia e consulta. Eu não tô te pedindo dinheiro novo — tô te mostrando como gastar MENOS do que já sai — menos de R$ 3 por dia pela família inteira. O que exatamente falta?" Se for o dia do vencimento: ajustar cobrança pro dia que o dinheiro entra.</td></tr>
              <tr><td><b>"Vou pensar."</b></td><td>A fuga clássica. Não existe "não" em call — existe "vou pensar".</td><td>"Pensar no quê, exatamente? Me fala qual parte ficou em dúvida que eu resolvo agora." Listou? Resolva uma a uma. Não listou? → fechamento "diz: não quero".</td></tr>
              <tr><td><b>"Me manda no WhatsApp que eu vejo."</b></td><td>Versão digital do "vou pensar". PDF não fecha venda.</td><td>"Mando sim — o contrato e o resumo do que a gente combinou. Mas me diz: o que no WhatsApp vai te responder que eu, aqui na linha, não posso responder agora?"</td></tr>
              <tr><td><b>"Já tenho um cartão de desconto" (concorrente)</b></td><td>Comparação rasa: acha que é tudo igual.</td><td>"Ótimo — e ele agenda a consulta pra senhora ou te dá um livrinho de descontos? A diferença é essa: lá é desconto; aqui é a gente cuidando do agendamento, 40 clínicas da região, 800 exames a preço de custo e até cirurgia com internação e anestesista inclusos — isso nenhum cartão de desconto tem. Quanto a senhora economizou com o seu no mês passado? Não sabe? Aqui chega extrato."</td></tr>
              <tr><td><b>"Plano de saúde é caro."</b></td><td>Trauma de preço de plano tradicional (R$ 400+/vida). Não sabe que somos outra categoria.</td><td>"Concordo — plano tradicional pra sua família passaria de R$ 1.500 por mês. Por isso a gente não é plano de saúde: é convênio médico. R$ 89 pela família inteira, não por pessoa — com cirurgia, internação e anestesista inclusos, coisa que cartão de desconto nenhum tem." (Educar: pilar Ensinar.)</td></tr>
              <tr><td><b>"Será que funciona mesmo? Não confio."</b></td><td>Medo de golpe — legítimo no nosso público.</td><td>"A SimedPrev é daqui de Itajaí, já tem milhares de famílias cadastradas — a senhora pode visitar a clínica X, que é da rede, hoje. E tem a opção sem fidelidade: testa por R$ 119 e cancela quando quiser. Quem não entrega não te dá porta de saída."</td></tr>
              <tr><td><b>"Deixa pro mês que vem."</b></td><td>Inércia — o medo ganhando por adiamento.</td><td>"O que muda no mês que vem? … E o exame do Pedro, espera mais um mês também? A condição que eu abri hoje é da nossa conversa — mês que vem é tabela. Adiar aqui tem preço, e quem paga é a senhora."</td></tr>
              <tr><td><b>"Só R$ 89 pra família toda? Deve ser fraco."</b></td><td>Desconfiança inversa — âncora de plano tradicional.</td><td>"Boa pergunta. É R$ 89 porque a gente não embute hospital caro na mensalidade — é a rede de clínicas daqui, negociada no atacado, e a senhora paga o preço de custo em cada serviço. Não paga o pronto-socorro que não usa; paga a consulta, o exame e a cirurgia que precisar, pelo preço que a clínica faz pro convênio."</td></tr>
            </tbody>
          </table>
        </div>
        <div class="sp-secfoot"><span><b>Parte 4</b> · Matriz de Objeções</span><span>Uso interno · SimedPrev Saúde</span></div>
      </section>

      <!-- ===== CALL MODELO ===== -->
      <section id="s11">
        <p class="sp-part-eyebrow">11 · Parte 4</p>
        <h2>4.4 — A call modelo SimedPrev Saúde</h2>
        <p>A estrutura de 40–60 min do alto ticket, comprimida para o nosso ticket e canal: <b>20 a 30 minutos</b>. O tempo de cada estágio é proporcional — e o diagnóstico continua sendo o rei, com quase metade da call.</p>
        <p class="sp-tl-title">Linha do tempo da call de 25 minutos</p>
        <div class="sp-tl">
          <div class="sp-tl-seg e1" style="width:8%"><span class="s">E1</span></div>
          <div class="sp-tl-seg e2" style="width:40%"><span class="s">E2 · DIAGNÓSTICO</span><span class="m">10 min</span></div>
          <div class="sp-tl-seg e3" style="width:20%"><span class="s">E3 · PIT 01</span><span class="m">5 min</span></div>
          <div class="sp-tl-seg e4" style="width:12%"><span class="s">E4</span><span class="m">3 min</span></div>
          <div class="sp-tl-seg e5" style="width:10%"><span class="s">E5</span><span class="m">2,5 min</span></div>
          <div class="sp-tl-seg e6" style="width:10%"><span class="s">E6</span><span class="m">2,5 min</span></div>
        </div>
        <div class="sp-tl-ends"><span>0 min</span><span>— quase metade da call é escutar —</span><span>25 min</span></div>
        <p class="sp-figcap">E1 · Abertura 2 min — E2 · Diagnóstico 10 min — E3 · Pit 01 5 min — E4 · Apresentação 3 min — E5 · Pit 02 2,5 min — E6 · Fechamento 2,5 min</p>
        <p class="sp-figcap">Meta por closer: 80–100 contratos/mês ≈ 4–5 fechamentos/dia. A eficiência da call — não o volume de ligações — é o que sustenta a meta.</p>
        <p class="sp-figcap">Figura 7 — O ritmo da call SimedPrev. Se a call está com 15 minutos e você já falou de preço, você pulou o diagnóstico — volte.</p>
        <h4>Os marcos que precisam acontecer (em ordem)</h4>
        <ol class="plain">
          <li><b>Min 0–2:</b> Bastão do controle tomado; formato aceito ("Fechado?").</li>
          <li><b>Min 2–12:</b> As 5 camadas do diagnóstico; cliente fez "a conta que nunca fez" e se assustou com o próprio número.</li>
          <li><b>Min 12–17:</b> Pit 01 completo: relevância declarada, nota 8+, sem plano B, decisor neutralizado, forma de pagamento conhecida.</li>
          <li><b>Min 17–20:</b> Apresentação: só os entregáveis que resolvem as dores citadas, cada um fechando a cadeia entregável→benefício→dor; checkpoint "o que a senhora entendeu?".</li>
          <li><b>Min 20–22:</b> Ancoragem (economia real: consulta 250→150, ultrassom 180→90, ressonância 950→550) → "vale ou não vale?" → oferta: R$ 119 sem fidelidade / R$ 89 anual + adesão R$ 99. Condição única R$ 49,90 só se houver contorno.</li>
          <li><b>Min 22–25:</b> "Pix ou cartão?" → pagamento NA CALL → boas-vindas: "Assinou, já pode usar: a gente agenda AGORA a consulta, o exame, o procedimento ou até a cirurgia que a família precisar. Qual a gente marca primeiro?"</li>
        </ol>
        <div class="sp-box sp-box--dark">
          <span class="lbl">O fechamento perfeito termina com uso, não com pagamento</span>
          <p>Assim que assina, o cliente já pode solicitar agendamento de consultas, exames, procedimentos e até cirurgias — e o closer usa isso como fecho: o primeiro agendamento é feito no mesmo telefonema. Cliente que usa na primeira semana não cancela, indica — e valida o "convênio que se paga sozinho" no primeiro extrato.</p>
        </div>
        <div class="sp-secfoot"><span><b>Parte 4</b> · A Call Modelo</span><span>Uso interno · SimedPrev Saúde</span></div>
      </section>

      <!-- ===== CHECKLIST / MÉTRICAS / REFERÊNCIAS ===== -->
      <section id="s12">
        <p class="sp-part-eyebrow">12 · Parte 4</p>
        <h2>4.5 — Checklist do closer · frases proibidas e obrigatórias</h2>
        <div class="sp-check">
          <div class="sp-check-col pre">
            <h5>Antes da call</h5>
            <ul>
              <li>Nome do lead, origem (base / mídia / indicação) e o que já declarou no formulário</li>
              <li>Mensagem de inversão de polaridade enviada no WhatsApp</li>
              <li>Ambiente silencioso, energia alta — a voz é 85% do jogo</li>
              <li>Bloco de anotações aberto (o diagnóstico "vai embaixo do braço" até o fim)</li>
            </ul>
          </div>
          <div class="sp-check-col pre">
            <h5>Durante a call</h5>
            <ul>
              <li>Bastão do controle tomado no 1º minuto</li>
              <li>Diagnóstico completo ANTES de qualquer menção a produto</li>
              <li>A conta mensal DELE anotada em reais</li>
              <li>Escala 0–10 perguntada (e confrontada se &lt; 8)</li>
              <li>Decisor e forma de pagamento mapeados antes do preço</li>
              <li>Ancoragem antes da tabela; "vale ou não vale?" antes do número</li>
              <li>Fechamento por alternativa ("Pix ou cartão?")</li>
              <li>Zero pendências exportadas para fora da call</li>
            </ul>
          </div>
        </div>
        <div class="sp-check">
          <div class="sp-check-col no">
            <h5>Frases proibidas</h5>
            <ul>
              <li>"Faz sentido pra você?" / "É isso que você busca?"</li>
              <li>"Tudo joia? Obrigado pelo seu tempinho!"</li>
              <li>"Vou te mandar uma proposta."</li>
              <li>"Sem problema, pensa com calma e me avisa!"</li>
              <li>"Qual seria um bom horário pra eu te ligar amanhã?"</li>
              <li>"É só um cartãozinho de desconto…"</li>
              <li>Explicar o produto igual, com as mesmas pausas, em toda call</li>
            </ul>
          </div>
          <div class="sp-check-col yes">
            <h5>Frases obrigatórias</h5>
            <ul>
              <li>"Eu sou responsável por conduzir a nossa conversa."</li>
              <li>"Deixa eu ver se eu entendi…" (e repetir "errado" de propósito)</li>
              <li>"Quanto a senhora gastou na última vez?"</li>
              <li>"De 0 a 10, quanto a senhora QUER resolver isso?"</li>
              <li>"Vale ou não vale?"</li>
              <li>"Tu entendeu como isso resolve o teu caso? O que tu entendeu?"</li>
              <li>"Joga limpo comigo."</li>
              <li>"Pix ou cartão?"</li>
            </ul>
          </div>
        </div>

        <h3>4.6 — Métricas da operação</h3>
        <div class="doc-table-wrap">
          <table>
            <thead><tr><th>Indicador</th><th>Definição</th><th>Referência</th></tr></thead>
            <tbody>
              <tr><td><b>Conversão em call</b></td><td>Pagamentos ÷ calls completas</td><td>Método promete dobrar a taxa atual (ex.: 30% → 60%). Referência de elite do método: 80–90%.</td></tr>
              <tr><td><b>Pix na call</b></td><td>% de fechamentos com pagamento DENTRO da call</td><td>Meta do método: ~90% dos fechamentos pagos na hora (integral ou sinal).</td></tr>
              <tr><td><b>Calls "de follow"</b></td><td>Calls encerradas com pendência exportada</td><td>Meta: zero. Cliente de follow = cliente que não fechou.</td></tr>
              <tr><td><b>Tempo de diagnóstico</b></td><td>Minutos de E2 por call</td><td>Mínimo 8 min. Diagnóstico curto = conversão baixa lá na frente.</td></tr>
              <tr><td><b>Contratos/closer/mês</b></td><td>Meta da operação</td><td>80–100 contratos (≈ 4–5/dia útil) · marco de validação do projeto: 300 contratos · meta: 1.000 em 6 meses.</td></tr>
            </tbody>
          </table>
        </div>

        <h3>4.7 — Referências e fontes do material</h3>
        <div class="sp-box sp-box--dark">
          <span class="lbl">A mensagem final do treinamento</span>
          <p>"Começa a tratar a call de vendas como o ápice total do teu negócio. Tudo que a empresa faz — marketing, parceria, estrutura — existe pra gerar call de venda. É justo que muito custe aquilo que muito vale. E vai chegar um momento no mercado em que não vai ser sobre o que tu vende: vai ser sobre o diagnóstico. Qualquer closer mediano se esconde atrás de um PowerPoint — nenhum se esconde atrás de um bom diagnóstico."</p>
        </div>
        <p><em>Este é o material da Fase 1 do Programa de Treinamento Comercial SimedPrev Saúde. As Fases 2 e 3 serão desenvolvidas na sequência, sobre esta base metodológica.</em></p>
        <ul>
          <li><b>DIXON, Matthew; ADAMSON, Brent.</b> The Challenger Sale: Taking Control of the Customer Conversation (Portfolio/Penguin, 2011). No Brasil: A Venda Desafiadora. Pesquisa CEB (hoje Gartner) com mais de 6.000 vendedores B2B; os 5 perfis; pilares Teach–Tailor–Take Control e a tensão construtiva; ~40% dos top performers são Desafiadores vs. 7% Construtores de Relacionamento.</li>
          <li><b>MEHRABIAN, Albert.</b> Silent Messages (1971) e estudos UCLA (1967) — a regra 7%-38%-55% sobre comunicação de sentimentos e atitudes (palavras, tom de voz, expressão corporal). Nota técnica: a proporção se aplica à comunicação de atitudes/emoções — exatamente o terreno da decisão de compra — e não a toda comunicação humana.</li>
          <li><b>MINER, Jeremy.</b> Método NEPQ — Neuro-Emotional Persuasion Questioning (7th Level) — venda por perguntas emocionais: "Tu quer? Quanto tu quer? Por que não resolveu antes? Por que agora é o momento?"</li>
          <li><b>Dados do produto:</b> materiais internos e tabelas de preços SimedPrev Saúde (agosto/2026) — rede de 40+ clínicas na região de Itajaí, 800+ exames a preço de custo, tabelas reais de consultas, exames, procedimentos e cirurgias (com internação e anestesista inclusos), preços R$ 119 / R$ 89 / condição única R$ 49,90, adesão R$ 99, gasto médio familiar com saúde de R$ 364/mês (POF/IBGE), metas comerciais da operação.</li>
        </ul>
        <div class="sp-secfoot"><span><b>Parte 4</b> · Referências</span><span>Uso interno · SimedPrev Saúde</span></div>
      </section>

    </article>

    <footer class="doc-footer">
      <span class="wm">SimedPrev <b>Saúde</b></span>
      <span>Treinamento do Time de Vendas · Agosto 2026 · Uso interno · não distribuir · <a href="?sair" style="color:inherit;text-decoration:underline">sair</a></span>
    </footer>
  </main>

<?php endif; ?>
</body>
</html>
