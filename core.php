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



//************************************** CORE **********************************************
function calcularPontuacao($linha, $coluna, $tabela, $jogador, $play){
    $pontuacao = array(11 => 1,
        21 => 2, 22 => 2, 23 => 2, 24 => 2,
        31 => 3, 32 => 3, 33 => 3, 34 => 3, 35 => 3, 36 => 3,
        41 => 4, 42 => 4, 43 => 4, 44 => 4, 45 => 4, 46 => 4, 47 => 4,  48 => 4,
        51 => 5, 52 => 5, 53 => 5, 54 => 5, 55 => 5,
        99 => 0);

    $ponto = $pontuacao[ $tabela[$linha][$coluna] ];
    if($ponto > 0){
        if($play == 1){
            $_SESSION["placar1"] = $_SESSION["placar1"] + $ponto;
            $_SESSION["contador1"]--;
        } else {
            $_SESSION["placar2"] = $_SESSION["placar2"] + $ponto;
            $_SESSION["contador2"]--;
        }
        $html = '';
        //$html .= '<script>window.alert("JOGADOR ('.$jogador.') MARCOU UM PONTO");</script>';
        $html .= '<body><EMBED SRC="sounds/Bomb2.wav" hidden="true" VOLUME="50"></EMBED></body>';
    	return $html;
    }
}

function validarFinalDoJogo($contador, $play) {
	if ($contador == 0) {
		header('location: vitoria.php?play=' . $play);
	}
}

function exibirMensagemEntreJogadores($contador, $jogador) {
	if ($contador != 0) {
		//TODO: desativado temporariamente
		return '';//'<script>window.alert("JOGADOR ( ' . $jogador . ' ) SUA VEZ");</script>';
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

//bloco responsavel por gerar o mapa aleatorio
//  NAVIO        (11) = 1 PONTO   (Opicional)
//  DESTROYER    (21;22) =  2 PONTOS
//  SUBMARINO    (31;32;33) = 3 PONTOS
//  ENCOURAÇADO  (41;42;43;44) =  4 PONTOS
//  PORTA-AVIÕES (51;52;53;54;55) =  5 PONTOS
// BLOCO DE CONTROLE DA PROGRAMAÇÃO DO JOGO
// *************************** CORE **************************************
if(isset($_SESSION["tabela1"]) && isset($_SESSION["tabela2"])){
    
    //BLOCO QUE INICIA AS VARIAVEIS A SER USADA SEJA PLAY 1 OU PLAY 2
    $tabela = array();
    $linha = $_GET["linha"];
    $coluna = $_GET["coluna"];
    $mascara = array();
    $jogador = 0;
    
    //TODO: SIMPLIFICAR ESSE IF QUE FAZ AS MESMAS COISAS COM DIFERENÇA QUE USAM CONTROLADORES DIFERENTES UMA POSSIBILIDADE É TER ARRAYS PARA CONTROLAR PLAYER 1 E 2 COM POSICIONAMENTO
    //VARIAVEIS RESPONSAVEIS PELATROCA DEDADOS ENTRE OS ARRAYS E SESSIONS
    if($_SESSION["play"] == 1) {
    	
        $tabela = $_SESSION["tabela1"];
        $mascara = $_SESSION["mascara1"];
        $jogador = $_SESSION["jogador1"];        
        $mascara[$linha][$coluna] = $tabela[$linha][$coluna];
        $_SESSION["mascara1"] = $mascara;
        $_SESSION["play"] = 2;        
        
        echo calcularPontuacao($linha, $coluna, $tabela, $jogador, $_SESSION["play"]);
        validarFinalDoJogo($_SESSION["contador1"], $_SESSION["play"]);    
        echo exibirMensagemEntreJogadores($_SESSION["contador1"], $_SESSION["jogador2"]);
    		
    } else {	
    	
        $tabela = $_SESSION["tabela2"];
        $mascara = $_SESSION["mascara2"];
        $jogador = $_SESSION["jogador2"];
        $mascara[$linha][$coluna] = $tabela[$linha][$coluna];
        $_SESSION["mascara2"] = $mascara;
        $_SESSION["play"] = 1;	
        
        echo calcularPontuacao($linha, $coluna, $tabela, $jogador, $_SESSION["play"]);
        validarFinalDoJogo($_SESSION["contador2"], $_SESSION["play"]);
        echo exibirMensagemEntreJogadores($_SESSION["contador2"], $_SESSION["jogador1"]);

    }
        
} else {
// **************************** SETUP ******************************************    
    unset($_SESSION["placar1"],
    	  $_SESSION["tabela1"],
   		  $_SESSION["mascara1"],
	   	  $_SESSION["contador1"],
   		  $_SESSION["play"],
   		  $_SESSION["placar2"],
   		  $_SESSION["tabela2"],
   		  $_SESSION["mascara2"],
   		  $_SESSION["contador2"]);
    
    // Esse bloco é do iframe desativado temporariamente
    unset($_SESSION["jogador1"], $_SESSION["jogador2"]);
    $_SESSION["jogador1"] = $_POST["jogador1"];
    $_SESSION["jogador2"] = $_POST["jogador2"];
    // Esse bloco é do iframe desativado temporariamente
    
    //*********************************************************************************************************************************
    $mascara = array();
    for($l = 0;$l < 10;$l++){
        for ($c = 0; $c < 10; $c++) {
        	$mascara[$l][$c] = 0;
        }    	
    }   

    $temp =  montarMapaDinamico();
    
    $_SESSION["play"] = 1;
    $_SESSION["placar1"] = $_SESSION["placar2"] = 0;
    $_SESSION["mascara1"] = $_SESSION["mascara2"] = $temp;//$mascara;
    $_SESSION["contador1"] = $_SESSION["contador2"] = 25;
    $_SESSION["tabela1"] = $temp; //montarMapaDinamico();
    $_SESSION["tabela2"] = $temp; //montarMapaDinamico();
    
    echo exibirMensagemEntreJogadores($_SESSION["contador2"], $_SESSION["jogador1"]);
    
}
?> 
<!-- *********************************************** HTML ********************************************* -->
<body <?php echo prepararBackGround($_SESSION["play"]); ?> >
	<table border="0" align="center">		
		<tr>
			<td align="center">			
    			<?php 
    			    $joutro = ($_SESSION["play"] == 1) ? $_SESSION["jogador2"] : $_SESSION["jogador1"];
                    echo montarTabelas(2, $joutro, 'MEU CAMPO DE BATALHA DE ', false); 
    			?>
            </td>
			<td width="100"></td>
			<td align="center">
    			<?php
    			    $jogador = ($_SESSION["play"] == 1) ? $_SESSION["jogador1"] : $_SESSION["jogador2"];
    			    echo montarTabelas(1, $jogador, 'CAMPO DE BATALHA DO ADVERSARIO DE ', true); ?>       
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<font size="+2"> <b>JOGADOR: <?php echo $jogador; ?></b>
    				<br /> <b>PONTUACAO ATUAL: <?php echo ($_SESSION["play"] == 1) ? $_SESSION["placar1"] : $_SESSION["placar2"]; ?> PONTOS</b><br />
					<b>NAVOIS RESTANTES DO OPONENTE: <?php echo ($_SESSION["play"] == 1) ? $_SESSION["contador2"] : $_SESSION["contador1"]; ?></b><br />
				</font>
			</td>
			<!--<td width="294" >&nbsp;</td> -->
		</tr>
	</table>
	<a href="index.php">Voltar</a>
</body>