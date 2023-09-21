<?php
session_start();
if($_GET["play"] == 1){
    $jogador = $_SESSION["jogador1"];
    $backgroud = 'bgcolor="#E8E4D9" style="background:url(imagens/background/world_war_i.jpg) no-repeat center fixed"';
} else {
    $jogador = $_SESSION["jogador2"];
    $backgroud = 'bgcolor="#DCD1A3" style="background:url(imagens/background/vietnam_war.jpg) no-repeat center fixed"';
}
?>    
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>BATALHA NAVAL</title>
</head>
<body <?php echo $backgroud; ?> >
    <form method="post" action="iframe.php">
        <table border="0" align="center">
            <tr><td align="center" ><img src="imagens/inicial/logo2.png"></td></tr>
            <tr><td align="center"><h1><?php echo $jogador; ?> Venceu !!!</h1></td></tr>            
            <tr><td align="center" ><input type="image" src="imagens/inicial/start1.png"></td></tr>
        </table>
    </form>
</body>
</html>