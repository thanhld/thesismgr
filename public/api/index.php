<?php
    error_reporting(E_ERROR);   //only report error
    require_once 'src/core/controller/FrontController.php';
    core\controller\FrontController::proc();
