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
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");

class clsIndex extends clsBase
{
	
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Acesso indevido" );
		$this->processoAp = "244";
	}
}

class indice extends clsDetalhe
{
	function Gerar()
	{
				
		@session_start();
		$this->cod_usuario = $_SESSION['id_pessoa'];
		session_write_close();
		$this->titulo = "Detalhe do V�nculo";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$cod_acesso = @$_GET['cod_permissao'];
		$db = new clsBanco();
		$db->Consulta( "SELECT ref_ref_cod_pessoa_fj, ip_externo, ip_interno, data_hora, pagina, variaveis FROM intranet_segur_permissao_negada WHERE  cod_intranet_segur_permissao_negada = '$cod_acesso' " );
		$objPessoa = new clsPessoaFisica();
		if( $db->ProximoRegistro() )
		{
			list( $ref_pessoa, $ip_i, $ip_e, $data_hora, $pagina, $variaveis ) = $db->Tupla();
			if( ! is_null( $ref_pessoa ) )
			{
				//$nome = $db->CampoUnico( "SELECT nm_pessoa FROM pessoa_fj WHERE cod_pessoa_fj = $ref_pessoa" );
				list( $nome, $cpf, $insc_mun ) = $objPessoa->queryRapida( $ref_pessoa, "nome", "cpf", "insc_mun" );
			}
			else 
			{
				$nome = "Convidado";
			}
			$variaveis = str_replace( "\n", "<br>", $variaveis );
			
			$this->addDetalhe( array("Nome", $nome ) );
			$this->addDetalhe( array("IP externo", $ip_e ) );
			$this->addDetalhe( array("IP interno", $ip_i ) );
			$this->addDetalhe( array("Pagina", $pagina ) );
			$this->addDetalhe( array("Extra", $variaveis ) );
			$this->addDetalhe( array("Data", date( "d/m/Y H:i", strtotime( substr($data_hora,0,19) ) ) ) );
		}	
		$this->url_cancelar = "acesso_indevido_lst.php";
		$this->largura = "100%";
	}
}


$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>