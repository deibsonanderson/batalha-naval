<?php
session_start();
unset ( $_SESSION ["jogador1"], $_SESSION ["jogador2"] );
unset ( $_SESSION ["placar1"], $_SESSION ["tabela1"], $_SESSION ["mascara1"], $_SESSION ["contador1"], $_SESSION ["play"], $_SESSION ["placar2"], $_SESSION ["tabela2"], $_SESSION ["mascara2"], $_SESSION ["contador2"] );
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>BATALHA NAVAL</title>
	<link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />
</head>
<body style="background: url(imagens/background/BN.jpg) no-repeat center fixed">
	<form method="post" action="iframe.php"><!-- O Iframe foi desativado temporariamente  -->
		<table class="center">
			<tr>
				<td align="center" colspan="2" height="200"><img src="imagens/inicial/logo2.png"></td>
			</tr>
			<tr>
				<td align="center" colspan="2"><img src="imagens/inicial/conbatentes.png"></td>
			</tr>
			<tr>
				<td align="center"><img src="imagens/inicial/jogador1.png"></td>
				<td align="center"><input type="text" name="jogador1" value="Jogador 1" maxlength="10" size="7"></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="image" src="imagens/inicial/start1.png"></td>
			</tr>
		</table>
	</form>
</body>
</html>