<?php

namespace common\controller;

use core\model\Step;
use core\model\StepTable;
use core\utility\Constant;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/Step.php';
require_once 'src/core/model/StepTable.php';

/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 2/27/2017
 * Time: 10:54 PM
 */
class StepController
{
    /**
     * API
     * getStep()
     *
     * HOW-TO-DO: des
     */
    public function getStep(){
        $result = StepTable::get();

        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * getStepById()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $id
     */
    public function getStepById($param)
    {
        $id = $param['id'];

        $result = StepTable::getById($id);

        if ($result !== null) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['step']
            ));
        }
    }
}
