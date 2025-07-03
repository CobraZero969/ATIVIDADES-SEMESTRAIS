<?php
session_start();

$respostas = json_decode($_POST['respostas'], true);
$municipios = $_SESSION['municipios_rodada'] ?? [];

$acertos = 0;
$corretos = [];
$incorretos = [];

foreach ($municipios as $m) {
    $nome = $m['nome'];
    $uf = $m['uf'];
    $regiao = $m['regiao'];
    $classificado = false;

    foreach ($respostas as $regiaoSelecionada => $itens) {
        foreach ($itens as $item) {
            if ($item['nome'] === $nome) {
                if ($regiao === $regiaoSelecionada) {
                    $acertos++;
                    $corretos[] = "$regiao: $nome-$uf";
                } else {
                    $incorretos[] = "$nome-$uf";
                }
                $classificado = true;
                break 2;
            }
        }
    }

    if (!$classificado) {
        $incorretos[] = "$nome-$uf";
    }
}

$_SESSION['pontos'] += $acertos * 10;
$_SESSION['acertos'] = $acertos;
$_SESSION['rodada']++;

// ✅ MUDANÇA AQUI:
if ($_SESSION['rodada'] > 10) {
    header("Location: final.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Resumo da Rodada</title>
</head>
<body>
    <h2>Resumo da Rodada</h2>
    <p>✅ Acertos: <?= $acertos ?></p>
    <p>🏅 Pontuação atual: <?= $_SESSION['pontos'] ?></p>

    <h4>Municípios classificados corretamente:</h4>
    <?php if ($corretos): ?>
        <p><?= implode(' | ', $corretos) ?></p>
    <?php else: ?>
        <p>Nenhum correto.</p>
    <?php endif; ?>

    <h4>Municípios não classificados ou incorretos:</h4>
    <?php if ($incorretos): ?>
        <p><?= implode(' | ', $incorretos) ?></p>
    <?php else: ?>
        <p>Nenhum incorreto.</p>
    <?php endif; ?>

    <form action="jogo.php" method="GET">
        <button type="submit">Próxima Rodada</button>
    </form>
</body>
</html>
