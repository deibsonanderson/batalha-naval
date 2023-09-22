<?php
session_start();

function debug($valor, $notDie = false) {
	echo "<pre>";
	var_dump ( $valor );
	if (! $notDie) die ();
}

//************************************** TEMPLATES **********************************************
function montarCelula($adversario, $celula, $linha, $coluna) {
	if ($celula['codigo'] == 0) {
    	return ($adversario) ? '<a href="core.php?linha=' . $linha . '&coluna=' .$coluna. '"><img src="imagens/grid/0.png" style="border:none" width="25" height="25"></a>' : '<img src="imagens/grid/0.png" style="border:none" width="13" height="13">';
    } else {
    	$style = 'style="' . definirRotacao( $celula ['vertical'], $celula ['direcao'] ) . '"';
    	return ($adversario) ? '<img src="imagens/grid/' . $celula['codigo'] . '.png" width="25" height="25" title="imagem '. $celula['codigo'] .'.png" '.$style.' >' : '<img src="imagens/grid/' . $celula['codigo'] . '.png" width="13" height="13" title="imagem '. $celula['codigo'] .'.png" '.$style.' >';
    }
}

function definirRotacao($isVertical, $isDirecao) {
	if ($isVertical == 0 && $isDirecao == 0) {
		return 'transform: rotate(180deg);';
	} else if ($isVertical == 1 && $isDirecao == 0) {
		return 'transform: rotate(270deg);';
	} else if ($isVertical == 0 && $isDirecao == 1) {
		return 'transform: rotate(0deg);';
	} else if ($isVertical == 1 && $isDirecao == 1) {
		return 'transform: rotate(90deg);';
	}
}

function montarGrids($adversario, $mascara){
    $html = '';
    $indice = 0;
    for ($l = 0; $l < 10; $l ++) {
        $html .= '<tr>';
        $html .= ($adversario) ? '<td width="25" height="25" align="center" ><b>' . retornarLetras($l) . '</b></td>' : '';
        for ($c = 0; $c < 10; $c ++) {
        	$html .= '<td>' . montarCelula($adversario, $mascara[$l][$c], $l, $c) . '</td>';
            ++ $indice;
        }
        $html .= '</tr>';
    }
    return $html;
}

function montarCabecalhoGrid() {
    $html = '<tr><td width="25" height="25" align="center"><b>& </b></td>';
    for ($i = 1; $i < 11; $i ++) {
        $html .= '<td width="25" height="25" align="center" ><b>' . str_pad($i, 2, '0', STR_PAD_LEFT) . '</b></td>';
    }
    return $html.'</tr>';
}

function montarTabelas($play, $jogador, $texto, $adversario){
    if ($_SESSION["play"] == $play) {
        $mascara = $_SESSION["mascara1"];
    } else {
        $mascara = $_SESSION["mascara2"];
    }
    
    $html = '<b>'.$texto.$jogador.'</b>';
    $html .= '<table border="1" align="center" bordercolor="#000000" bgcolor="#000000" style="color: #FFFFFF">';
    $html .= ($adversario) ? montarCabecalhoGrid() : '';
    $html .= montarGrids($adversario, $mascara);
    $html .= '</table>';
    return $html;
}

function retornarLetras($indece) {
    $letras = array(0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 5 => 'F', 6 => 'G', 7 => 'H', 8 => 'I', 9 => 'J');
    return $letras[$indece];
}

function prepararBackGround($play){
    return ($play == 1) ? 'bgcolor="#E8E4D9" style="background:url(imagens/background/world_war_i.jpg) no-repeat center fixed"' :
    'bgcolor="#DCD1A3" style="background:url(imagens/background/vietnam_war.jpg) no-repeat center fixed"';
}

function reproduzirSomAtaque($som){
	return ($som == 1)?'<EMBED SRC="sounds/Bomb2.wav" hidden="true" VOLUME="50"></EMBED>':'';
}

//************************************** CORE **********************************************
//bloco responsavel por gerar o mapa aleatorio
//  NAVIO        (11) = 1 PONTO   (Opicional)
//  DESTROYER    (21;22) =  2 PONTOS
//  SUBMARINO    (31;32;33) = 3 PONTOS
//  ENCOURAÇADO  (41;42;43;44) =  4 PONTOS
//  PORTA-AVIÕES (51;52;53;54;55) =  5 PONTOS
// BLOCO DE CONTROLE DA PROGRAMAÇÃO DO JOGO
function calcularPontuacao($linha, $coluna, $tabela, $jogador, $play){
	$som = false;
    $pontuacao = array(11 => 1,
        21 => 2, 22 => 2, 23 => 2, 24 => 2,
        31 => 3, 32 => 3, 33 => 3, 34 => 3, 35 => 3, 36 => 3,
        41 => 4, 42 => 4, 43 => 4, 44 => 4, 45 => 4, 46 => 4, 47 => 4,  48 => 4,
        51 => 5, 52 => 5, 53 => 5, 54 => 5, 55 => 5,
        99 => 0);

    $ponto = $pontuacao[ $tabela[$linha][$coluna]['codigo'] ];
    if($ponto > 0){
    	if($play == 1){
            $_SESSION["placar1"] = $_SESSION["placar1"] + $ponto;
            $_SESSION["contador1"]--;
        } else {
            $_SESSION["placar2"] = $_SESSION["placar2"] + $ponto;
            $_SESSION["contador2"]--;
        }
    	$som = true;
    }
    return $som;
}

function validarFinalDoJogo($play) {
	if ($_SESSION["contador".$play] <= 0) {
		header('location: vitoria.php?play=' . $play);
	}	
}

// operação responsavel por gerar um boolean randomico
function gerarBooleano(){
	return rand(0,1) == 1;
}

//Esse array tera as informações de direção e verticalidade para guiar o rotacionamento das imagens
function gerarConteudoCelula($codigo,  $isVertical, $isDirecao){
	return array ('codigo' => $codigo,	'vertical' => $isVertical, 'direcao' => $isDirecao );
}

function montarMapaDinamico(){
	$embarcacoes = array(
			1 => array('vertical' => 0, 'direcao' => 1, 'embarcacao' => array(11)),
			2 => array('vertical' => gerarBooleano(), 'direcao' => gerarBooleano(), 'embarcacao' => array(21,22)),
			3 => array('vertical' => gerarBooleano(), 'direcao' => gerarBooleano(), 'embarcacao' => array(31,32,33)),
			4 => array('vertical' => gerarBooleano(), 'direcao' => gerarBooleano(), 'embarcacao' => array(41,42,43,44)),
			5 => array('vertical' => gerarBooleano(), 'direcao' => gerarBooleano(), 'embarcacao' => array(51,52,53,54,55))
	);
	
	$mapa = array();
	for($l = 0; $l < 10; $l++){
		for($c = 0; $c < 10; $c++){
			$mapa[$l][$c] = gerarConteudoCelula(99, 0, 0);
		}
	}
	
	//ESSE BLOCO FOI INVERTIDO PARA MELHOR PERFORMANCE DIMINUINDO A CHANCE DE COLIXÕES ENTRE OS NAVIOS
	foreach (array_reverse($embarcacoes) as $embarcacao){
	
		$tamanho = count($embarcacao['embarcacao']);
		
		$posicao = sortearPosicaoInicialBarco($mapa, $embarcacao['vertical'], $embarcacao['direcao'], $tamanho);
		
		foreach ( $embarcacao['embarcacao'] as $emb ) {
			$linha = ($embarcacao['vertical'] == 1) ? (($embarcacao['direcao'] == 0) ? $posicao[0]-- : $posicao[0]++) : $posicao[0];
			$coluna = ($embarcacao['vertical'] == 1) ? $posicao[1] : (($embarcacao['direcao'] == 0) ? $posicao[1]-- : $posicao[1]++);
		
			$mapa [$linha][$coluna] = gerarConteudoCelula ($emb, $embarcacao['vertical'], $embarcacao['direcao']);
		}
		
	}
	return $mapa;
}

function sortearPosicaoInicialBarco($mapa, $isVertical, $isDirecao, $tamanho){
	$sorteado = null;
	$isValid = false;
	$count = 0;
	while (!$isValid) {
		++$count;
		$filtred = array();
		for($l = 0; $l < 10; $l ++) {
			for($c = 0; $c < 10; $c ++) {
				if ($mapa[$l][$c]['codigo'] == 99						
						&& checarDirecaoTamanhoVertical($c, $isDirecao, $tamanho, $isVertical) 
			 			&& checarDirecaoTamanhoHorizontal($l, $isDirecao, $tamanho, $isVertical)){
						$filtred[$l][$c] = 99;
				}
			}
		}
		$linha = array_rand($filtred); 
		$coluna = array_rand($filtred[$linha]);
		$sorteado = array($coluna, $linha);
		$isValid = validarIntercessao($sorteado, $mapa, $isVertical, $isDirecao, $tamanho);
		
		if($count >= 1000){
			break;
		}		
	}	
	return $sorteado;
}

function validarIntercessao($sorteado, $mapa, $isVertical, $isDirecao, $tamanho){
	if($isVertical == 1){
		if($isDirecao == 1) {
			for ($i = $sorteado[0]; $i <= ($sorteado[0] + $tamanho); $i++) {
				if($mapa[$i][$sorteado[1]]['codigo'] != 99){
					return false;
				}
			}			
		} else {			
			for ($i = $sorteado[0]; $i >= ($sorteado[0] - $tamanho); $i--) {
				if($mapa[$i][$sorteado[1]]['codigo'] != 99){
					return false;
				}
			}
		}		
	} else {
		if($isDirecao == 1) {			
			for ($i = $sorteado[1]; $i <= ($sorteado[1] + $tamanho); $i++) {
				if($mapa[$sorteado[0]][$i]['codigo'] != 99){
					return false;
				}
			}			
		} else {			
			for ($i = $sorteado[1]; $i >= ($sorteado[1] - $tamanho); $i--) {
				if($mapa[$sorteado[0]][$i]['codigo'] != 99){
					return false;
				}
			}
		}
	}
	return true;
}

function checarDirecaoTamanho($posicao, $direcao, $tamanho){
	if ($direcao && $posicao <= (10 - $tamanho)) {
		return true;
	}	
	if (!$direcao && $posicao >= ($tamanho-1)) {
		return true;
	}	
	return false;
}

function checarDirecaoTamanhoVertical($posicao, $direcao, $tamanho, $isVertical) {
	if ($isVertical) {
		return checarDirecaoTamanho($posicao, $direcao, $tamanho);
	} else {		
		return true;
	}
}

function checarDirecaoTamanhoHorizontal($posicao, $direcao, $tamanho, $isVertical){
	if (!$isVertical) {
		return checarDirecaoTamanho($posicao, $direcao, $tamanho);
	} else {		
		return true;
	}
}

function iniciarMascaraSetup(){
	$mascara = array();
	for($l = 0;$l < 10;$l++){
		for ($c = 0; $c < 10; $c++) {
			$mascara[$l][$c] = 0;
		}
	}
	return $mascara;
}

function resetarSessoesSetup(){
	unset($_SESSION["placar1"],
			$_SESSION["tabela1"],
			$_SESSION["mascara1"],
			$_SESSION["contador1"],
			$_SESSION["play"],
			$_SESSION["placar2"],
			$_SESSION["tabela2"],
			$_SESSION["mascara2"],
			$_SESSION["contador2"]);
}

function selecionarCelularPelaIA($oponente, $contador, $som){
	if($contador > 0){
		$filtred = array();
		for($l = 0; $l < 10; $l ++) {
			for($c = 0; $c < 10; $c ++) {
				if ($oponente[$l][$c]['codigo'] != 0) {
					$filtred[$l][$c] = 99;
				}
			}
		}
		$linha = array_rand($filtred);
		$coluna = array_rand($filtred[$linha]);
		header("location: core.php?linha=".$linha."&coluna=".$coluna."&som=".$som);
	}
}

function atualizarMascaraTabela($linha, $coluna, $play){
	$tabela = $_SESSION["tabela".$play];
	$mascara = $_SESSION["mascara".$play];
	$mascara[$linha][$coluna] = $tabela[$linha][$coluna];
	$_SESSION["mascara".$play] = $mascara;
	return $tabela;
}

function exibirMensagemEntreJogadores($contador, $jogador) {
	if ($contador != 0) {
		//TODO: desativado temporariamente
		return '';//'<script>window.alert("JOGADOR ( ' . $jogador . ' ) SUA VEZ");</script>';
	}
}

// *************************** CORE **************************************
if(isset($_SESSION["tabela1"]) && isset($_SESSION["tabela2"])){
    
    if($_SESSION["play"] == 1) {    	
        
    	$som = calcularPontuacao($_GET["linha"], $_GET["coluna"], 
    			atualizarMascaraTabela($_GET["linha"], $_GET["coluna"], $_SESSION["play"]), 
    			$_SESSION["jogador1"], $_SESSION["play"]);
        validarFinalDoJogo($_SESSION["play"]);    
        $_SESSION["play"] = 2;
        selecionarCelularPelaIA($_SESSION["tabela2"], $_SESSION["contador1"], $som);
        //echo exibirMensagemEntreJogadores($_SESSION["contador1"], $_SESSION["jogador2"]);        
        
    } else if($_SESSION["play"] == 2) {    	
    	
    	$som = calcularPontuacao($_GET["linha"], $_GET["coluna"], 
    			atualizarMascaraTabela($_GET["linha"], $_GET["coluna"], $_SESSION["play"]), $_SESSION["jogador2"], $_SESSION["play"]);        
        validarFinalDoJogo($_SESSION["play"]);        
        $_SESSION["play"] = 1;
        echo reproduzirSomAtaque($som);        
        //echo exibirMensagemEntreJogadores($_SESSION["contador2"], $_SESSION["jogador1"]);
    }
        
} else {
// **************************** SETUP ******************************************    
	resetarSessoesSetup();
    
    $_SESSION["play"] = 1; //essa variavel é responsavel por indicar quem esta jogando no inicio é o 1
    $_SESSION["placar1"] = $_SESSION["placar2"] = 0; //setup inicil dos placares
    $_SESSION["mascara1"] = $_SESSION["mascara2"] = iniciarMascaraSetup(); //setup inicial das mascaras, que será subistituido aos poucos pelos mapa 
    $_SESSION["contador1"] = $_SESSION["contador2"] = 15; //o contador dos navios restantes 
    $_SESSION["tabela1"] = montarMapaDinamico(); //essa variavel armazena o (mapa) posicionamento dos navios e dos mares que sera o guia da mascara
    $_SESSION["tabela2"] = montarMapaDinamico(); //essa variavel armazena o (mapa) posicionamento dos navios e dos mares que sera o guia da mascara
    //echo exibirMensagemEntreJogadores($_SESSION["contador2"], $_SESSION["jogador1"]);
    
}
?> 
<!-- *********************************************** HTML ********************************************* -->
<head>
	<link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />
</head>
<body <?php echo prepararBackGround($_SESSION["play"]); ?> >
	<table class="center">		
		<tr>
			<td align="center">
    			<?php echo montarTabelas(1, $_SESSION["jogador2"], 'CAMPO DE BATALHA DO ADVERSARIO: ', true); ?>       
			</td>
			<td width="10"></td>
			<td align="center">			
    			<?php echo montarTabelas(2, $_SESSION["jogador1"], 'CAMPO DE BATALHA DO JOGADOR: ', false); ?>
            </td>
		</tr>
		<tr>			
			<td>
				<font size="+1"> <b>JOGADOR: <?php echo $_SESSION["jogador1"]; ?></b>
    				<br /> <b>PONTUACAO ATUAL: <?php echo $_SESSION["placar1"]; ?> PONTOS</b><br />
					<b>NAVOIS RESTANTES DO OPONENTE: <?php echo $_SESSION["contador2"]; ?></b><br />
				</font>
			</td>
			<td>&nbsp;</td>
			<td>
				<font size="+1"> <b>JOGADOR: <?php echo $_SESSION["jogador2"]; ?></b>
    				<br /> <b>PONTUACAO ATUAL: <?php echo $_SESSION["placar2"]; ?> PONTOS</b><br />
					<b>NAVOIS RESTANTES DO OPONENTE: <?php echo $_SESSION["contador1"]; ?></b><br />
				</font>
			</td>			
		</tr>
	</table>
	<!--a href="index.php">Voltar</a-->
</body>
<?php echo reproduzirSomAtaque($_GET["som"]); ?>