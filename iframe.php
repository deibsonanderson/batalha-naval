<?php
session_start();
unset ( $_SESSION ["jogador1"], $_SESSION ["jogador2"] );
$_SESSION ["jogador1"] = $_POST ["jogador1"];
$_SESSION ["jogador2"] = 'Maquina';
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>BATALHA NAVAL</title>
	<link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />
</head>
<body background="imagens/background/BN.jpg">
	<!-- EMBED SRC="sounds/preview.mp3" hidden="true" VOLUME="50" loop="true"></EMBED-->
	<table class="center">
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
			<td><iframe src="core.php" width="900" height="600" ></iframe></td>
		</tr>
	</table>
</body>
</html>