<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itaja�								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja�			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
	*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
	*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
	*																		 *
	*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
	*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
	*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
$desvio_diretorio = "";
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Acesso indevido" );
		$this->processoAp = "244";
	}
}
class indice extends clsListagem
{
	function Gerar()
	{
		
		$this->titulo = "Acessos Indevidos";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );	
		$this->addCabecalhos( array( "Nome", "IP", "IP interno", "Data" ) );		
		$where = "";		
		$db = new clsBanco();
		$db2 = new clsBanco();
		$total = $db->UnicoCampo( "SELECT count(0) FROM intranet_segur_permissao_negada {$where}" );
		$total_temp = $total;
		$limite= 20;
		$iniciolimit = (!empty($_GET['iniciolimit'])) ? $_GET['iniciolimit'] : "0";
		$clasula_limit = $iniciolimit * $limite;
		$limit = " LIMIT {$clasula_limit}, $limite";	
		
		$db->Consulta( "SELECT cod_intranet_segur_permissao_negada, ref_ref_cod_pessoa_fj, ip_interno, ip_externo, data_hora FROM intranet_segur_permissao_negada ORDER BY data_hora DESC $limit" );
		$objPessoa = new clsPessoaFisica();
		while ( $db->ProximoRegistro() )
		{
			list ( $cod, $cod_pessoa, $ip_interno, $ip_externo, $data_hora ) = $db->Tupla();
			if( ! is_null( $cod_pessoa ) )
			{
				//$nm_pessoa = $db2->CampoUnico( "SELECT nm_pessoa FROM pessoa_fj WHERE cod_pessoa_fj = $cod_pessoa" );
				list ( $nm_pessoa ) = $objPessoa->queryRapida($cod_pessoa, "nome");
			}
			else 
			{
				$nm_pessoa = "Convidado";
			}
			$ip_externo = ($ip_externo == '200.215.80.163') ? "Prefeitura" : "Externo - ".$ip_externo;
			$this->addLinhas( array( "<a href=\"acesso_indevido_det.php?cod_permissao=$cod\"><img src='imagens/noticia.jpg' border=0>$nm_pessoa</a>", "<a href=\"acesso_indevido_det.php?cod_permissao=$cod\">$ip_externo</a>", "<a href=\"acesso_indevido_det.php?cod_permissao=$cod\">$ip_interno</a>", "<a href=\"acesso_indevido_det.php?cod_permissao=$cod\">" . date( "d/m/Y H:i", strtotime( substr($data_hora,0,19) ) ) . "</a>" ) );
		}		
		$this->paginador( "acesso_indevido_lst.php?&nm_pessoa={$_GET['nm_pessoa']}",$total_temp,$limite,@$_GET['pos_atual']);
		$this->largura = "100%";
	}
}
$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm( $miolo );
$pagina->MakeAll();
?>