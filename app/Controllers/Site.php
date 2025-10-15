<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Site extends BaseController
{
    public function forbidden()
    {
        return $this->view('templates/forbidden', [], false);
    }

    public function show404()
    {
        return $this->view('templates/404', [], false);
    }

    public function test() {
        $email = service('email');
        $email->setTo('jeremy.sabbah@gmail.com');
        $email->setSubject('Test Email');
        $email->setMessage('Ceci est un test.');
        if ($email->send()) {
            echo "E-mail envoyé avec succès !";
        } else {
            echo $email->printDebugger(['headers']);
        }
    }

}
