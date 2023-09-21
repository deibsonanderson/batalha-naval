<?php
session_start();

unset($_SESSION["jogador1"], $_SESSION["jogador2"]);
unset($_SESSION["placar1"],$_SESSION["tabela1"],$_SESSION["mascara1"],$_SESSION["contador1"],$_SESSION["play"],$_SESSION["placar2"],$_SESSION["tabela2"],$_SESSION["mascara2"],$_SESSION["contador2"]);
$_SESSION["jogador1"] = $_POST["jogador1"];
$_SESSION["jogador2"] = $_POST["jogador2"];
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>BATALHA NAVAL</title>
</head>

<body background="imagens/background/BN.jpg">
	<!-- EMBED SRC="sounds/preview.mp3" hidden="true" VOLUME="50" loop="true"></EMBED-->
	<table border="0" align="center">
		<tr>
			<td background="imagens/inicial/lateral.png" width="300" align="center">
				<table>
					<tr>
						<td align="center"><img src="imagens/inicial/logo2.png"></td>
					</tr>
					<tr>
						<td align="center"><a href="index.php"><img src="imagens/inicial/start1.png" border="none"></a></td>
					</tr>
				</table>
			</td>
			<td><iframe src="core.php" width="900" height="600" align="right"></iframe></td>
		</tr>
	</table>

</body>
</html>