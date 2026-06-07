# Vibra Marketing — Landing Page

Landing page premium da **Vibra Marketing** — posicionamento, autoridade e crescimento.
Estética dark / tech-luxury / editorial. HTML + CSS + JavaScript puro, com GSAP, ScrollTrigger e Lenis.

🔗 **Produção:** https://vibradados.com.br

## Estrutura

```
index.html        Marcação semântica + SEO + dados estruturados
styles.css        Design system, layout, animações e responsividade
main.js           Loader, smooth scroll (Lenis), reveals (GSAP), contadores,
                  cursor custom, botões magnéticos, tilt 3D, parallax
assets/
  favicon.svg                Ícone (símbolo "V" em vetor)
  og.png                     Imagem social (1200×630)
  logo-vibra-marketing.png   Logo oficial completo (ativo da marca)
CNAME             Domínio customizado (GitHub Pages)
robots.txt        / sitemap.xml — SEO técnico
```

## Rodar localmente

Por usar fontes e bibliotecas via CDN, basta um servidor estático:

```bash
python3 -m http.server 8080
# abra http://localhost:8080
```

## Deploy

Hospedado via **GitHub Pages** servindo a raiz (`/`) do repositório na branch principal.
O arquivo `CNAME` aponta para `vibradados.com.br`.

## Personalização rápida

- **Cores da marca:** variáveis `--blue`, `--cyan`, `--magenta` no topo de `styles.css`.
- **Logo oficial:** substitua `assets/favicon.svg` e o markup `<svg>` do símbolo no `index.html`.
- **Métricas/depoimentos:** editáveis diretamente no `index.html`.
- **CTA / WhatsApp:** ajuste o `href` do botão "Quero Construir Autoridade".
