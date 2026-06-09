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
</head>
<body>

<div class="topbar">
    <div class="topbar-esquerda">
        <button class="hamburger-btn" id="hamburger-btn"><span></span><span></span><span></span></button>
        <div class="topbar-logo-box">🏋️</div>
        <div class="topbar-titulo"><h1>GymFlow</h1><p>Área do Aluno</p></div>
    </div>
    <div class="topbar-direita">
        <div class="topbar-usuario">
            <div class="nome"><?php echo htmlspecialchars($_SESSION["nome"]); ?></div>
            <div class="cargo">Aluno</div>
        </div>
        <a href="/GymFlow-main/logout.php" class="btn-sair">Sair</a>
    </div>
</div>

<div class="sidebar-overlay" id="sidebar-overlay"></div>

<div class="layout">
        <aside class="sidebar" id="sidebar">
        <span class="sidebar-label">Menu</span>
        <a href="/GymFlow-main/paginas/aluno/dashboard-aluno.php"><span class="icon">📊</span> Dashboard</a>
        <a href="/GymFlow-main/paginas/aluno/gerar_treino_com_ia/gerar-treino.php" class="ativo"><span class="icon">🏋️</span> Meus Treinos</a>
        <a href="/GymFlow-main/paginas/aluno/caixa.php"><span class="icon">💳</span> Mensalidades</a>
        <span class="sidebar-label">Conta</span>
        <a href="/GymFlow-main/paginas/aluno/perfil.php"><span class="icon">👤</span> Meu Perfil</a>
        <a href="/GymFlow-main/logout.php"><span class="icon">🚪</span> Sair</a>
    </aside>

    <main class="main main-center">
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
                        <div class="dias-selector">
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
                        <div class="focos-chips">
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

                <hr class="form-secao-divider">

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
        const nivel    = document.getElementById('nivel').value;
        const dias     = document.querySelector('input[name="dias"]:checked')?.value || '3';
        const extras   = document.getElementById('extras').value;
        const focos    = [...document.querySelectorAll('.focos-chips input:checked')].map(el => el.value).join(', ') || 'corpo todo';

        if (!objetivo || !nivel) {
            alert('Selecione o objetivo e o nível antes de continuar.');
            return;
        }

        const btn = document.getElementById('btn-gerar');
        btn.classList.add('loading');
        btn.innerHTML = `<span class="loading-dots"><span></span><span></span><span></span></span> GERANDO...`;

        const container = document.getElementById('resultado-container');
        const textoEl   = document.getElementById('resultado-texto');
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
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ prompt })
            });
            const data  = await response.json();
            const texto = data.texto || data.erro || 'Não foi possível gerar o treino.';
            textoEl.innerHTML = formatarTexto(texto);
        } catch (err) {
            textoEl.textContent = '❌ Erro ao conectar. Tente novamente.';
        }

        btn.classList.remove('loading');
        btn.innerHTML = `<span>🤖</span> GERAR MEU TREINO COM IA`;
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function formatarTexto(texto) {
        return texto
            .replace(/\*\*(.*?)\*\*/g, '<strong style="color:#f5c518">$1</strong>')
            .replace(/^#{1,3} (.+)$/gm, '<strong style="color:#f5c518;font-size:16px;display:block;margin-top:16px">$1</strong>')
            .replace(/\n/g, '<br>');
    }

    function novoTreino() {
        document.getElementById('resultado-container').classList.remove('visivel');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>

</body>
</html>