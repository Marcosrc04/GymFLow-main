<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["id"])) {
    header("Location: ../../../login.php");
    exit();
}
$primeiro_nome = explode(' ', $_SESSION["nome"])[0];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treino com IA | GymFlow</title>
    <link rel="shortcut icon" href="../../../arquivos/imagem/GF.ico" type="image/x-icon">
    <link rel="stylesheet" href="../../../arquivos/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            background: #0a0a0a;
        }

        /* TOPBAR */
        .topbar {
            width: 100%;
            background: #111;
            border-bottom: 1px solid rgba(255, 204, 0, 0.1);
            padding: 0 32px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-esquerda {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .topbar-logo-box {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #f5c518, #ffd84d);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .topbar-titulo h1 {
            color: #fff;
            font-size: 17px;
            font-weight: 700;
            line-height: 1.2;
        }

        .topbar-titulo p {
            color: #888;
            font-size: 11px;
        }

        .topbar-direita {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .topbar-usuario .nome {
            color: #f5c518;
            font-size: 14px;
            font-weight: 600;
        }

        .topbar-usuario .cargo {
            color: #888;
            font-size: 11px;
        }

        .btn-sair {
            border: 1px solid rgba(245, 197, 24, 0.4);
            color: #f5c518;
            padding: 7px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            transition: 0.2s;
        }

        .btn-sair:hover {
            background: #f5c518;
            color: #111;
        }

        /* HAMBURGER */
        .hamburger-btn {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: 6px;
            background: #1e1e1e;
            border-radius: 8px;
            border: 1px solid rgba(255, 204, 0, 0.15);
        }

        .hamburger-btn span {
            display: block;
            width: 20px;
            height: 2px;
            background: #f5c518;
            border-radius: 2px;
            transition: 0.3s;
        }

        .hamburger-btn.aberto span:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
        }

        .hamburger-btn.aberto span:nth-child(2) {
            opacity: 0;
        }

        .hamburger-btn.aberto span:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
        }

        /* LAYOUT */
        .layout {
            display: flex;
            min-height: calc(100vh - 64px);
        }

        /* SIDEBAR */
        .sidebar {
            width: 240px;
            background: #111;
            border-right: 1px solid rgba(255, 204, 0, 0.08);
            padding: 28px 16px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
            background-image: url(../../../arquivos/imagem/listras-da-barra.png);
            background-size: cover;
            background-position: center;
        }

        .sidebar-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #555;
            padding: 0 12px;
            margin: 16px 0 8px;
        }

        .sidebar-label:first-child {
            margin-top: 0;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 14px;
            border-radius: 10px;
            color: #aaa;
            font-size: 14px;
            font-weight: 500;
            transition: 0.2s;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: rgba(245, 197, 24, 0.08);
            color: #f5c518;
        }

        .sidebar a.ativo {
            background: rgba(245, 197, 24, 0.12);
            color: #f5c518;
            border: 1px solid rgba(245, 197, 24, 0.2);
        }

        .sidebar a .icon {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 199;
        }

        .sidebar-overlay.ativo {
            display: block;
        }

        /* MAIN */
        .main {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            flex: 1;
            padding: 36px;
            min-width: 0;
            overflow-x: hidden;
        }

        /* PAGE HEADER */
        .page-header {
            margin-bottom: 36px;
        }

        .page-header .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #f5c518;
            margin-bottom: 10px;
        }

        .page-header .eyebrow::before {
            content: '';
            display: inline-block;
            width: 20px;
            height: 2px;
            background: #f5c518;
        }

        .page-header .saudacao {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 48px;
            line-height: 1;
            color: #fff;
            letter-spacing: 1px;
        }

        .page-header .saudacao span {
            color: #f5c518;
        }

        .page-header .sub {
            color: #666;
            font-size: 14px;
            margin-top: 8px;
            max-width: 480px;
        }

        /* FORM CARD */
        .treino-wrap {
            max-width: 740px;
        }

        .form-card {
            background: #161616;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 20px;
            padding: 36px;
            animation: fadeUp 0.4s ease both;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .form-secao-titulo {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 18px;
            letter-spacing: 1px;
            color: #fff;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-secao-sub {
            color: #555;
            font-size: 12px;
            margin-bottom: 18px;
        }

        .form-secao-divider {
            border: none;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            margin-bottom: 20px;
        }

        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }

        .form-row-full {
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .form-group label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #666;
        }

        .form-group select,
        .form-group input {
            background: #0f0f0f;
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 12px;
            padding: 13px 16px;
            color: #e0e0e0;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            outline: none;
            transition: 0.2s;
            appearance: none;
            -webkit-appearance: none;
        }

        .form-group select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23f5c518' stroke-width='2' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 40px;
        }

        .form-group select:focus,
        .form-group input:focus {
            border-color: rgba(245, 197, 24, 0.5);
            box-shadow: 0 0 0 3px rgba(245, 197, 24, 0.08);
        }

        .form-group select option {
            background: #1a1a1a;
        }

        .form-group input::placeholder {
            color: #3a3a3a;
        }

        /* DIAS SELECTOR */
        .dias-selector {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .dias-selector input[type="radio"] {
            display: none;
        }

        .dias-selector label {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f0f0f;
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            color: #555;
            cursor: pointer;
            transition: 0.2s;
            letter-spacing: 0;
            text-transform: none;
        }

        .dias-selector input[type="radio"]:checked+label {
            background: #f5c518;
            color: #111;
            border-color: #f5c518;
        }

        .dias-selector label:hover {
            border-color: rgba(245, 197, 24, 0.4);
            color: #f5c518;
        }

        /* CHIPS FOCO */
        .focos-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .focos-chips input[type="checkbox"] {
            display: none;
        }

        .focos-chips label {
            padding: 7px 15px;
            background: #0f0f0f;
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 999px;
            font-size: 13px;
            font-weight: 500;
            color: #666;
            cursor: pointer;
            transition: 0.2s;
            letter-spacing: 0;
            text-transform: none;
        }

        .focos-chips input[type="checkbox"]:checked+label {
            background: rgba(245, 197, 24, 0.1);
            border-color: rgba(245, 197, 24, 0.4);
            color: #f5c518;
        }

        .focos-chips label:hover {
            border-color: rgba(245, 197, 24, 0.3);
            color: #f5c518;
        }

        /* BTN GERAR */
        .btn-gerar {
            width: 100%;
            padding: 17px;
            background: linear-gradient(90deg, #f5c518, #ffd84d);
            color: #111;
            border: none;
            border-radius: 12px;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 20px;
            letter-spacing: 2px;
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 4px;
        }

        .btn-gerar:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 28px rgba(245, 197, 24, 0.28);
        }

        .btn-gerar.loading {
            opacity: 0.7;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* RESULTADO */
        #resultado-container {
            display: none;
            max-width: 740px;
            margin-top: 28px;
        }

        #resultado-container.visivel {
            display: block;
            animation: fadeUp 0.4s ease both;
        }

        .resultado-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .resultado-header h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 28px;
            letter-spacing: 1px;
            color: #f5c518;
        }

        .resultado-badge {
            padding: 4px 12px;
            background: rgba(245, 197, 24, 0.1);
            border: 1px solid rgba(245, 197, 24, 0.2);
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #f5c518;
            text-transform: uppercase;
        }

        .resultado-conteudo {
            background: #161616;
            border: 1px solid rgba(255, 204, 0, 0.1);
            border-radius: 16px;
            padding: 28px;
            color: #c8c8c8;
            font-size: 14px;
            line-height: 1.9;
        }

        .loading-dots {
            display: inline-flex;
            gap: 4px;
            align-items: center;
        }

        .loading-dots span {
            width: 6px;
            height: 6px;
            background: #111;
            border-radius: 50%;
            animation: bounce 1s infinite;
        }

        .loading-dots span:nth-child(2) {
            animation-delay: 0.15s;
        }

        .loading-dots span:nth-child(3) {
            animation-delay: 0.3s;
        }

        @keyframes bounce {

            0%,
            80%,
            100% {
                transform: scale(0.7);
                opacity: 0.5
            }

            40% {
                transform: scale(1);
                opacity: 1
            }
        }

        .btn-novo-treino {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 16px;
            padding: 11px 18px;
            background: transparent;
            border: 1px solid rgba(255, 204, 0, 0.25);
            border-radius: 10px;
            color: #f5c518;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-novo-treino:hover {
            background: rgba(245, 197, 24, 0.08);
            border-color: #f5c518;
        }

        @media(max-width:768px) {
            .hamburger-btn {
                display: flex;
            }

            .topbar-usuario {
                display: none;
            }

            .sidebar {
                position: fixed;
                top: 64px;
                left: -260px;
                width: 240px;
                height: calc(100vh - 64px);
                z-index: 200;
                transition: left 0.3s ease;
                overflow-y: auto;
            }

            .sidebar.aberta {
                left: 0;
            }

            .main {
                padding: 20px 16px;
            }

            .page-header .saudacao {
                font-size: 34px;
            }

            .form-row-2 {
                grid-template-columns: 1fr;
            }

            .form-card {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <!-- TOPBAR -->
    <div class="topbar">
        <div class="topbar-esquerda">
            <button class="hamburger-btn" id="hamburger-btn"><span></span><span></span><span></span></button>
            <div class="topbar-logo-box">🏋️</div>
            <div class="topbar-titulo">
                <h1>GymFlow</h1>
                <p>Área do Aluno</p>
            </div>
        </div>
        <div class="topbar-direita">
            <div class="topbar-usuario">
                <div class="nome"><?php echo htmlspecialchars($_SESSION["nome"]); ?></div>
                <div class="cargo">Aluno</div>
            </div>
            <a href="../../../logout.php" class="btn-sair">Sair</a>
        </div>
    </div>

    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <div class="layout">
        <aside class="sidebar" id="sidebar">
            <span class="sidebar-label">Menu</span>
            <a href="../dashboard-aluno.php"><span class="icon">📊</span> Dashboard</a>
            <a href="../caixa.php"><span class="icon">💳</span> Mensalidades</a>
            <a href="gerar-treino.php" class="ativo"><span class="icon">🤖</span> Treino IA</a>
            <span class="sidebar-label">Conta</span>
            <a href="../perfil.php"><span class="icon">👤</span> Meu Perfil</a>
            <a href="../../../logout.php"><span class="icon">🚪</span> Sair</a>
        </aside>

        <main class="main">
            <div class="page-header">
                <div class="eyebrow">Inteligência Artificial</div>
                <div class="saudacao">Treino <span>Personalizado</span></div>
                <p class="sub">Diga seus objetivos e a IA monta um plano completo pra você, <?php echo htmlspecialchars($primeiro_nome); ?>.</p>
            </div>

            <div class="treino-wrap">
                <div class="form-card">

                    <div class="form-secao-titulo"><span>🎯</span> Configurações</div>
                    <div class="form-secao-sub">Preencha os campos abaixo para personalizar seu treino</div>
                    <hr class="form-secao-divider">

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Objetivo</label>
                            <select id="objetivo">
                                <option value="">Selecione...</option>
                                <option value="hipertrofia">💪 Hipertrofia</option>
                                <option value="emagrecimento">🔥 Emagrecimento</option>
                                <option value="condicionamento">⚡ Condicionamento</option>
                                <option value="força">🏋️ Força</option>
                                <option value="resistência">🏃 Resistência</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nível</label>
                            <select id="nivel">
                                <option value="">Selecione...</option>
                                <option value="iniciante">🌱 Iniciante</option>
                                <option value="intermediário">⚡ Intermediário</option>
                                <option value="avançado">🔥 Avançado</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row-full">
                        <div class="form-group">
                            <label>Dias por semana</label>
                            <div class="dias-selector" style="margin-top:4px">
                                <?php for ($i = 1; $i <= 7; $i++): ?>
                                    <input type="radio" name="dias" id="dia<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo $i === 3 ? 'checked' : ''; ?>>
                                    <label for="dia<?php echo $i; ?>"><?php echo $i; ?></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-row-full">
                        <div class="form-group">
                            <label>Foco muscular</label>
                            <div class="focos-chips" style="margin-top:4px">
                                <?php
                                $grupos = ['Peito', 'Costas', 'Ombros', 'Bíceps', 'Tríceps', 'Pernas', 'Glúteos', 'Abdômen', 'Panturrilha', 'Corpo todo'];
                                foreach ($grupos as $g):
                                ?>
                                    <input type="checkbox" id="foco_<?php echo $g; ?>" value="<?php echo $g; ?>">
                                    <label for="foco_<?php echo $g; ?>"><?php echo $g; ?></label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-row-full">
                        <div class="form-group">
                            <label>Observações (opcional)</label>
                            <input type="text" id="extras" placeholder="Ex: tenho lesão no joelho, prefiro sem equipamento...">
                        </div>
                    </div>

                    <hr style="border:none;border-top:1px solid rgba(255,255,255,0.05);margin:4px 0 20px">

                    <button class="btn-gerar" id="btn-gerar" onclick="gerarTreino()">
                        <span>🤖</span> GERAR MEU TREINO COM IA
                    </button>
                </div>

                <div id="resultado-container">
                    <div class="resultado-header">
                        <h2>SEU TREINO</h2>
                        <span class="resultado-badge">Gerado por IA</span>
                    </div>
                    <div class="resultado-conteudo" id="resultado-texto"></div>
                    <button class="btn-novo-treino" onclick="novoTreino()">↩ Gerar outro treino</button>
                </div>
            </div>
        </main>
    </div>

    <script>
        const hamburgerBtn = document.getElementById('hamburger-btn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        hamburgerBtn.addEventListener('click', () => {
            hamburgerBtn.classList.toggle('aberto');
            sidebar.classList.toggle('aberta');
            overlay.classList.toggle('ativo');
        });
        overlay.addEventListener('click', () => {
            hamburgerBtn.classList.remove('aberto');
            sidebar.classList.remove('aberta');
            overlay.classList.remove('ativo');
        });

        async function gerarTreino() {
            const objetivo = document.getElementById('objetivo').value;
            const nivel = document.getElementById('nivel').value;
            const dias = document.querySelector('input[name="dias"]:checked')?.value || '3';
            const extras = document.getElementById('extras').value;
            const focos = [...document.querySelectorAll('.focos-chips input:checked')].map(el => el.value).join(', ') || 'corpo todo';

            if (!objetivo || !nivel) {
                alert('Selecione o objetivo e o nível antes de continuar.');
                return;
            }

            const btn = document.getElementById('btn-gerar');
            btn.classList.add('loading');
            btn.innerHTML = `<span class="loading-dots"><span></span><span></span><span></span></span> GERANDO...`;

            const container = document.getElementById('resultado-container');
            const textoEl = document.getElementById('resultado-texto');
            container.classList.add('visivel');
            textoEl.textContent = '⏳ Montando seu treino personalizado...';

            const prompt = `Você é um personal trainer especialista. Monte um plano de treino completo com as seguintes características:
- Objetivo: ${objetivo}
- Nível: ${nivel}
- Dias por semana: ${dias}
- Foco muscular: ${focos}
${extras ? `- Observações: ${extras}` : ''}

Estruture assim:
1. Divida os dias com nome (Ex: Dia 1 - Peito e Tríceps)
2. Liste exercícios com séries e repetições
3. Dê dicas de descanso entre séries
4. Ao final, adicione uma dica de nutrição rápida

Seja objetivo, prático e motivador. Use emojis para deixar mais dinâmico.`;

            try {
                const response = await fetch('api-treino.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        prompt
                    })
                });
                const data = await response.json();
                const texto = data.texto || data.erro || 'Não foi possível gerar o treino.';
                textoEl.innerHTML = formatarTexto(texto);
            } catch (err) {
                textoEl.textContent = '❌ Erro ao conectar. Tente novamente.';
            }

            btn.classList.remove('loading');
            btn.innerHTML = `<span>🤖</span> GERAR MEU TREINO COM IA`;
            container.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        function formatarTexto(texto) {
            return texto
                .replace(/\*\*(.*?)\*\*/g, '<strong style="color:#f5c518">$1</strong>')
                .replace(/^#{1,3} (.+)$/gm, '<strong style="color:#f5c518;font-size:16px;display:block;margin-top:16px">$1</strong>')
                .replace(/\n/g, '<br>');
        }

        function novoTreino() {
            document.getElementById('resultado-container').classList.remove('visivel');
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    </script>
</body>

</html>