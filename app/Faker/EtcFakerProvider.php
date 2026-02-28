<?php

namespace App\Faker;

use Faker\Provider\Base;

class EtcFakerProvider extends Base
{
    /**
     * Create a new class instance.
     */
    public function num_cif(){
        return $this->generator->regexify('^[A-Z]{1}\d{8}$');
    }
    public function email_invitado(string $nombre){
        return $nombre . "@etcApps.com";
    }
    public function dni(): string
    {
        return $this->generator->regexify('^\d{8}[A-Z]{1}$');
    }
}
