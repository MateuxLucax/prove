<?php
interface IGetValoresEmVetorPDO {
	# TERÁ QUE SER IMPLEMENTADO EM TODAS AS CLASSES QUE SE RELACIONAREM COM O BD
	# OS VALORES DA CLASSE DEVEM SER ARMAZENADOS NO ARRAY NA MESMA ORDEM EM QUE OS CAMPO ESTÃO NO BANCO DE DADOS
	# O PRIMEIRO VALOR (POSIÇÃO 0 DO ARRAY) DEVE SER SEMPRE A CHAVE PRIMÁRIA
	# ENTÃO, NO BD, O PRIMEIRO CAMPO TAMBÉM DEVE SEMPRE SER A CHAVE PRIMÁRIA
	public function getValoresEmVetorPDO();
}
?>