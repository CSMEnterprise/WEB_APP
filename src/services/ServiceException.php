<?php

namespace App\Services;

use App\Entity\EAccountBusiness;
use App\Entity\EAdmin;
use App\Entity\EAnnuncio;
use App\Entity\ECarrello;
use App\Entity\ECategoria;
use App\Entity\EElementoCarrello;
use App\Entity\EFeedback;
use App\Entity\EImmagine;
use App\Entity\EIndirizzo;
use App\Entity\EModera;
use App\Entity\EPagamento;
use App\Entity\EPreferito;
use App\Entity\ESegnalazione;
use Exception;
use PDO;
use PDOException;
use Throwable;
use finfo;

class ServiceException extends Exception
{
}
