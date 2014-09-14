<?php

namespace Mimazoo\SoaBundle\Controller;

use Mimazoo\SoaBundle\Controller\Controller;

class HealthController extends Controller
{
    public function masterAction()
    {
    	$q = $this->getDoctrine()->GetConnection($this->getDoctrine()->getDefaultConnectionName());
     	$result = $q->query("SELECT 1=1");
     	return ($result->fetchAll()[0]["1=1"] == "1" ? "OK" : $this->view("NOT OK!", 500));
    	//return "$result OK";
    }

}
