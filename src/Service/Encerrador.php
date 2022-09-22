<?php

namespace Treino\Leilao\Service;

use Treino\Leilao\Dao\Leilao as LeilaoDao;

class Encerrador
{
    private $dao;
    private $enviadorDeEmail;

    public function __construct(LeilaoDao $dao, EnviadorEmail $enviadorDeEmail)
    {
        $this->dao = $dao;
        $this->enviadorDeEmail = $enviadorDeEmail;
    }

    public function encerra()
    {
        $leiloes = $this->dao->recuperarNaoFinalizados();

        foreach ($leiloes as $leilao) {
            if ($leilao->temMaisDeUmaSemana()) {
                try {
                    $leilao->finaliza();
                    $this->dao->atualiza($leilao);
                    $this->enviadorDeEmail->notificaTerminoLeilao($leilao);
                } catch (\DomainException $e) {
                    error_log($e->getMessage());
                }
            }
        }
    }
}
