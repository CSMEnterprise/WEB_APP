<?php

namespace App\Controllers;

class LegalController extends BaseController
{
    public function privacy(): void
    {
        $this->view('legale/privacy.tpl', [], 'Privacy Policy');
    }

    public function termini(): void
    {
        $this->view('legale/termini.tpl', [], 'Termini di servizio');
    }

    public function cookie(): void
    {
        $this->view('legale/cookie.tpl', [], 'Cookie');
    }
}
