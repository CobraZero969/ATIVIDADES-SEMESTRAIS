<?php
session_start();

// Inicia o jogo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome'])) {
    $_SESSION['nome'] = $_POST['nome'];
    $_SESSION['pontos'] = 0;
    $_SESSION['rodada'] = 1;
    $_SESSION['resultados'] = [];
}

$rodada = $_SESSION['rodada'] ?? 1;

$arquivo = 'dados_municipios.json';

if (!file_exists($arquivo)) die("❌ Arquivo $arquivo não encontrado.");

$conteudo = file_get_contents($arquivo);
$dados = json_decode($conteudo, true);

if (!is_array($dados)) die("❌ JSON inválido!");

$regioes = ['Norte', 'Nordeste', 'Centro-Oeste', 'Sudeste', 'Sul'];
$municipios_rodada = [];

// Um de cada região
foreach ($regioes as $regiao) {
    $filtrados = array_values(array_filter($dados, fn($m) => $m['regiao'] === $regiao));
    if (!$filtrados) die("❌ Sem municípios para $regiao");
    $municipios_rodada[] = $filtrados[array_rand($filtrados)];
}

// Um extra
$extra = $regioes[array_rand($regioes)];
$filtrados = array_values(array_filter($dados, fn($m) => $m['regiao'] === $extra));
$municipios_rodada[] = $filtrados[array_rand($filtrados)];

// Embaralha
shuffle($municipios_rodada);
$_SESSION['municipios_rodada'] = $municipios_rodada;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Rodada <?= $rodada ?> - Jogo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }

        .cruz {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-template-rows: 1fr 1fr 1fr;
            gap: 15px;
            width: 700px;
            margin: 40px auto;
        }

        .area {
            min-height: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            font-weight: bold;
            color: #fff;
            border: 4px solid transparent;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .area:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
            cursor: pointer;
        }

        /* Cores com bordas */
        .area[data-regiao="Norte"] {
            background: #4CAF50;
            border-color: #388E3C;
        }
        .area[data-regiao="Nordeste"] {
            background: #FF9800;
            border-color: #F57C00;
        }
        .area[data-regiao="Centro-Oeste"] {
            background: #2196F3;
            border-color: #1976D2;
        }
        .area[data-regiao="Sudeste"] {
            background: #9C27B0;
            border-color: #7B1FA2;
        }
        .area[data-regiao="Sul"] {
            background: #F44336;
            border-color: #D32F2F;
        }
        .area[data-regiao="???"] {
            background: #9E9E9E;
            border-color: #616161;
        }

        .drag {
            background: #f5f5f5;
            color: #333;
            padding: 6px 12px;
            margin: 5px;
            cursor: grab;
            border: 1px solid #999;
            border-radius: 4px;
        }

        #zona-municipios {
            margin: 20px auto;
            width: 700px;
            border: 2px dashed #666;
            min-height: 60px;
            padding: 10px;
            border-radius: 8px;
        }

        button {
            margin-top: 20px;
            padding: 10px 20px;
            background: #2196F3;
            border: none;
            border-radius: 6px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #1976D2;
        }
    </style>
</head>
<body>

<h2>Rodada <?= $rodada ?> de 10</h2>
<h3>Pontuação: <?= $_SESSION['pontos'] ?? 0 ?> pontos</h3>
<h4>Tempo restante: <span id="timer">15</span> segundos</h4>

<form action="calcula_resultado.php" method="POST" id="form">
    <input type="hidden" name="respostas" id="respostas">

    <div id="zona-municipios">
        <h4>Arraste os municípios para as regiões:</h4>
        <?php foreach ($municipios_rodada as $m): ?>
            <div class="drag"
                 draggable="true"
                 data-nome="<?= htmlspecialchars($m['nome']) ?>"
                 data-regiao="<?= htmlspecialchars($m['regiao']) ?>"
                 data-uf="<?= htmlspecialchars($m['uf']) ?>">
                <?= htmlspecialchars($m['nome']) ?> - <?= htmlspecialchars($m['uf']) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="cruz">
        <div class="area" data-regiao="???">???</div>
        <div class="area" data-regiao="Norte">Norte</div>
        <div class="area" data-regiao="Nordeste">Nordeste</div>
        <div class="area" data-regiao="Centro-Oeste">Centro-Oeste</div>
        <div class="area" data-regiao="Sudeste">Sudeste</div>
        <div class="area" data-regiao="Sul">Sul</div>
    </div>

    <button type="submit" id="enviar">Salvar</button>
</form>

<script>
let tempo = 15;
let timer = setInterval(() => {
    document.getElementById('timer').textContent = tempo;
    if (--tempo < 0) {
        clearInterval(timer);
        document.getElementById('enviar').click();
    }
}, 1000);

let dragged = null;

document.querySelectorAll('.drag').forEach(el => {
    el.addEventListener('dragstart', e => {
        dragged = el;
    });
});

document.querySelectorAll('.area').forEach(area => {
    area.addEventListener('dragover', e => e.preventDefault());
    area.addEventListener('drop', e => {
        e.preventDefault();
        if (dragged) {
            area.appendChild(dragged);
            dragged = null;
        }
    });
});

let origem = document.getElementById('zona-municipios');
origem.addEventListener('dragover', e => e.preventDefault());
origem.addEventListener('drop', e => {
    e.preventDefault();
    if (dragged) {
        origem.appendChild(dragged);
        dragged = null;
    }
});

document.getElementById('form').addEventListener('submit', e => {
    const respostas = {};
    document.querySelectorAll('.area').forEach(area => {
        const regiao = area.dataset.regiao;
        const drags = area.querySelectorAll('.drag');
        respostas[regiao] = [];
        drags.forEach(d => {
            respostas[regiao].push({ nome: d.dataset.nome, uf: d.dataset.uf });
        });
    });
    document.getElementById('respostas').value = JSON.stringify(respostas);
});
</script>

</body>
</html>
