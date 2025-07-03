<?php
session_start();

// Carrega o ranking salvo (caso exista)
$ranking = file_exists("ranking.json") ? json_decode(file_get_contents("ranking.json"), true) : [];

// Filtra apenas partidas completas
$ranking_filtrado = array_filter($ranking, function ($jogador) {
    return isset($jogador['completo']) && $jogador['completo'] === true;
});

// Ordena por pontuação decrescente
usort($ranking_filtrado, function ($a, $b) {
    return $b['pontos'] <=> $a['pontos'];
});
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Jogo das Regiões</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f4f4f4;
        }
        h1, h2 {
            color: #333;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            background: #fff;
            margin-bottom: 5px;
            padding: 10px;
            border-left: 4px solid #4caf50;
        }
        form {
            margin-top: 30px;
        }
        input[type="text"] {
            padding: 8px;
            width: 250px;
        }
        button {
            padding: 8px 15px;
            background-color: #4caf50;
            border: none;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        footer {
            margin-top: 50px;
            color: #888;
        }
    </style>
</head>
<body>

    <h1>🏆 Ranking das Partidas Finalizadas</h1>

    <?php if (count($ranking_filtrado) > 0): ?>
        <ul>
            <?php foreach ($ranking_filtrado as $registro): ?>
                <li><?= htmlspecialchars($registro['nome']) ?> - <?= $registro['pontos'] ?> pontos</li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Nenhuma partida finalizada ainda.</p>
    <?php endif; ?>

    <h2>👤 Novo Jogador</h2>
    <form action="jogo.php" method="POST">
        <label for="nome">Digite seu nome:</label><br>
        <input type="text" name="nome" id="nome" required>
        <button type="submit">JOGAR</button>
    </form>

    <footer>
        © 2025 | Nome do Aluno
    </footer>

</body>
</html>
