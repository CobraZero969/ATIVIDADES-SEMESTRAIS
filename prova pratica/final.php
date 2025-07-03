<?php
session_start();
$pontosFinais = $_SESSION['pontos'] ?? 0;
// Limpa a sessão para reiniciar depois
session_destroy();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Parabéns! 🎉</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            text-align: center;
            padding: 50px;
            overflow: hidden;
        }

        h1 {
            font-size: 48px;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        p {
            font-size: 22px;
            color: #333;
            margin-bottom: 40px;
        }

        button {
            background: #2196F3;
            color: #fff;
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #1976D2;
        }

        /* Confetes simples */
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background: red;
            opacity: 0.7;
            animation: fall 3s linear infinite;
        }

        @keyframes fall {
            0% { transform: translateY(-20px) rotate(0deg); }
            100% { transform: translateY(100vh) rotate(360deg); }
        }
    </style>
</head>
<body>

    <h1>🎉 Parabéns! 🎉</h1>
    <p>Você finalizou o Jogo das Regiões.</p>
    <p><strong>🏆 Pontuação Final: <?= $pontosFinais ?> pontos</strong></p>

    <form action="index.php" method="GET">
        <button type="submit">🔄 Jogar Novamente</button>
    </form>

    <!-- Confetes gerados por JS -->
    <script>
        const colors = ['#FF5252', '#FFEB3B', '#4CAF50', '#2196F3', '#9C27B0'];
        for (let i = 0; i < 100; i++) {
            let confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + 'vw';
            confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
            confetti.style.width = confetti.style.height = Math.random() * 8 + 4 + 'px';
            document.body.appendChild(confetti);
        }
    </script>

</body>
</html>
