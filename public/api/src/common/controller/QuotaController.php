<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 4/6/2017
 * Time: 11:57 AM
 */

namespace common\controller;

use core\model\DegreeTable;
use core\model\Quota;
use core\model\QuotaTable;
use core\utility\Constant;
use core\utility\Paging;

require_once 'src/core/utility/Constant.php';
require_once 'src/core/model/Quota.php';
require_once 'src/core/model/QuotaTable.php';
require_once 'src/core/model/DegreeTable.php';
require_once 'src/core/utility/Paging.php';


class QuotaController
{
    /**
     * API
     * getQuota()
     *
     * HOW-TO-DO: des
     */
    public function getQuota(){
        $option = Paging::normalizeOption($_GET);
        $result = QuotaTable::get($option);

        http_response_code(200);
        echo json_encode($result);
    }

    /**
     * API
     * createQuotaVersion()
     *
     * HOW-TO-DO: des
     */
    public function createQuotaVersion(){
        switch ($_SESSION['role']) {
            case 0:
                break;
            default:
                http_response_code(403);
                echo json_encode(array(
                    'message' => Constant::notPermissionText
                ));
                return;
        }

        $json = file_get_contents('php://input');
        $quotas = json_decode($json);

        if (property_exists($quotas, 'version')) {
            $version = $quotas->version;
            $checkVer = QuotaTable::checkVersionExisted($version);
            if($checkVer) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames['version']) . Constant::isExistedText
                ));
                return;
            }
        } else {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['version']) . Constant::isRequiredText
            ));
            return;
        }
        $data = $quotas->data;
        if (!is_array($data) || count($data) == 0) {
            http_response_code(400);
            echo json_encode(array(
                'message' => Constant::emptyList
            ));
            return;
        }

        $errorCount = 0;

        $factors = array();
        //Only update factor by version
        $factors['mainFactorStudent'] = (property_exists($quotas, 'mainFactorStudent'))
            ? number_format($quotas->mainFactorStudent, 2, '.', '') : 1;
        $factors['coFactorStudent'] = (property_exists($quotas, 'coFactorStudent'))
            ? number_format($quotas->coFactorStudent, 2, '.', '') : 1;
        $factors['mainFactorResearcher'] = (property_exists($quotas, 'mainFactorResearcher'))
            ? number_format($quotas->mainFactorResearcher, 2, '.', '') : 1;
        $factors['coFactorResearcher'] = (property_exists($quotas, 'coFactorResearcher'))
            ? number_format($quotas->coFactorResearcher, 2, '.', '') : 1;
        $factors['mainFactorGraduated'] = (property_exists($quotas, 'mainFactorGraduated'))
            ? number_format($quotas->mainFactorGraduated, 2, '.', '') : 1;
        $factors['coFactorGraduated'] = (property_exists($quotas, 'coFactorGraduated'))
            ? number_format($quotas->coFactorGraduated, 2, '.', '') : 1;

        foreach ($data as $obj) {
            $quota = array();
            $quota['version'] = $version;
            if (property_exists($obj, 'degreeId')) $quota['degreeId'] = $obj->degreeId;
            if (property_exists($obj, 'maxStudent')) $quota['maxStudent'] = $obj->maxStudent;
            if (property_exists($obj, 'maxGraduated')) $quota['maxGraduated'] = $obj->maxGraduated;
            if (property_exists($obj, 'maxResearcher')) $quota['maxResearcher'] = $obj->maxResearcher;
            $quota['isActive'] = 0;
            $quota['mainFactorStudent'] = $factors['mainFactorStudent'];
            $quota['coFactorStudent'] = $factors['coFactorStudent'];
            $quota['mainFactorResearcher'] = $factors['mainFactorResearcher'];
            $quota['coFactorResearcher'] = $factors['coFactorResearcher'];
            $quota['mainFactorGraduated'] = $factors['mainFactorGraduated'];
            $quota['coFactorGraduated'] = $factors['coFactorGraduated'];

            $quotaObj = new Quota($quota);

            if(!isset($quota['degreeId']) || $quota['degreeId'] == null) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames['degreeId']) . Constant::isRequiredText
                ));
                $errorCount += 1;
                break;
            } elseif(!isset($quota['maxStudent']) || $quota['maxStudent'] === null) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames['maxStudent']) . Constant::isRequiredText
                ));
                $errorCount += 1;
                break;
            } elseif(!isset($quota['maxGraduated']) || $quota['maxGraduated'] === null) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames['maxGraduated']) . Constant::isRequiredText
                ));
                $errorCount += 1;
                break;
            } elseif(!isset($quota['maxResearcher']) || $quota['maxResearcher'] === null) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames['maxResearcher']) . Constant::isRequiredText
                ));
                $errorCount += 1;
                break;
            } elseif(!isset($quota['isActive']) || $quota['isActive'] === null) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::columnNames['isActive']) . Constant::isRequiredText
                ));
                $errorCount += 1;
                break;
            } else {
                foreach ($quota as $key => $value) {
                    $action = 'check' . ucfirst($key);
                    if (!$quotaObj->$action()) {
                        http_response_code(400);
                        echo json_encode(array(
                            'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                        ));

                        $errorCount += 1;
                        break;
                    }
                    
                    //Check maximum sum of factors
                    if($key == 'mainFactorStudent' || $key == 'coFactorStudent') {
                        $sumFactors = $quota['mainFactorStudent'] + $quota['coFactorStudent'];
                        if($sumFactors != 1) {
                            http_response_code(400);
                            echo json_encode(array(
                                'message' => "Tổng hệ số hướng dẫn cho đề tài khóa luận phải bằng 1"
                            ));
                            $errorCount += 1;
                            break;
                        }
                    }

                    else if($key == 'mainFactorGraduated' || $key == 'coFactorGraduated') {
                        $sumFactors = $quota['mainFactorGraduated'] + $quota['coFactorGraduated'];
                        if($sumFactors != 1) {
                            http_response_code(400);
                            echo json_encode(array(
                                'message' => "Tổng hệ số hướng dẫn cho đề tài luận văn phải bằng 1"
                            ));
                            $errorCount += 1;
                            break;
                        }
                    }

                    else if($key == 'mainFactorResearcher' || $key == 'coFactorResearcher') {
                        $sumFactors = $quota['mainFactorResearcher'] + $quota['coFactorResearcher'];
                        if($sumFactors != 1) {
                            http_response_code(400);
                            echo json_encode(array(
                                'message' => "Tổng hệ số hướng dẫn cho đề tài luận án phải bằng 1"
                            ));
                            $errorCount += 1;
                            break;
                        }
                    }
                }

                if($errorCount > 0) { break; }
            }

            $degree = DegreeTable::getById($quota['degreeId']);
            if(!$degree){
                http_response_code(400);
                echo json_encode(array(
                    'message' => ucfirst(Constant::objectNames['degree']) . Constant::notExistedText
                ));

                $errorCount += 1;
                break;
            }

            //Create new quota for each degreeId
            $result = QuotaTable::addQuota($quotaObj);
            if(!$result) {
                http_response_code(400);
                echo json_encode(array(
                    'message' => "Tạo " . Constant::objectNames['quotas'] . " không thành công"
                ));

                $errorCount += 1;
                break;
            }
        }

        //Delete version if error occurs
        if($errorCount > 0) {
            //deactive
            //QuotaTable::activeQuotaByVersion($version, 0);
            QuotaTable::deleteByVersion($version);
        } else {
            http_response_code(200);
            echo json_encode(array(
                'message' => Constant::success
            ));
        }
    }

    /**
     * API
     * updateQuotaVersion()
     *
     * HOW-TO-DO: des
     * @param $param
     */
    public function updateQuotaVersion($param){
        $version = $param['version'];

        $oldQuota = QuotaTable::getByVersion($version);
        if(!$oldQuota){
            http_response_code(404);
            echo json_encode(array(
                'message' => Constant::notFoundText . Constant::objectNames['quotas']
            ));
            return;
        }

        switch ($_SESSION['role']) {
            case 0:
                break;
            default:
                http_response_code(403);
                echo json_encode(array(
                    'message' => Constant::notPermissionText
                ));
                return;
        }

        $json = file_get_contents('php://input');
        $quotas = json_decode($json);
        $factors = array();

        //Only update factor by version
        if(property_exists($quotas, 'mainFactorStudent')){
            $factors['mainFactorStudent'] = number_format($quotas->mainFactorStudent, 2, '.', '');
        }
        if(property_exists($quotas, 'coFactorStudent')){
            $factors['coFactorStudent'] = number_format($quotas->coFactorStudent, 2, '.', '');
        }
        if(property_exists($quotas, 'mainFactorResearcher')){
            $factors['mainFactorResearcher'] = number_format($quotas->mainFactorResearcher, 2, '.', '');
        }
        if(property_exists($quotas, 'coFactorResearcher')){
            $factors['coFactorResearcher'] = number_format($quotas->coFactorResearcher, 2, '.', '');
        }
        if(property_exists($quotas, 'mainFactorGraduated')){
            $factors['mainFactorGraduated'] = number_format($quotas->mainFactorGraduated, 2, '.', '');
        }
        if((property_exists($quotas, 'coFactorGraduated'))){
            $factors['coFactorGraduated'] = number_format($quotas->coFactorGraduated, 2, '.', '');
        }

        $data = $quotas->data;
        $errorCount = 0;
        if($data && is_array($data) && count($data) != 0) {
            foreach ($data as $obj) {
                $quota = array();
                if (property_exists($obj, 'id')) {
                    $quota['id'] = $obj->id;
                } else {
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => ucfirst(Constant::columnNames['id']) . Constant::isRequiredText
                    ));
                    $errorCount += 1;
                    break;
                }

                if (property_exists($obj, 'maxStudent')) $quota['maxStudent'] = $obj->maxStudent;
                if (property_exists($obj, 'maxGraduated')) $quota['maxGraduated'] = $obj->maxGraduated;
                if (property_exists($obj, 'maxResearcher')) $quota['maxResearcher'] = $obj->maxResearcher;
                foreach($factors as $key => $value){
                    $quota[$key] = $value;
                }

                $quotaObj = new Quota($quota);

                if(isset($quota['maxStudent']) && $quota['maxStudent'] === null) {
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => ucfirst(Constant::columnNames['maxStudent']) . Constant::notEmptyText
                    ));
                    $errorCount += 1;
                    break;
                } elseif(isset($quota['maxGraduated']) && $quota['maxGraduated'] === null) {
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => ucfirst(Constant::columnNames['maxGraduated']) . Constant::notEmptyText
                    ));
                    $errorCount += 1;
                    break;
                } elseif(isset($quota['maxResearcher']) && $quota['maxResearcher'] === null) {
                    http_response_code(400);
                    echo json_encode(array(
                        'message' => ucfirst(Constant::columnNames['maxResearcher']) . Constant::notEmptyText
                    ));
                    $errorCount += 1;
                    break;
                } else {
                    foreach ($quota as $key => $value) {
                        $action = 'check' . ucfirst($key);
                        if (!$quotaObj->$action()) {
                            http_response_code(400);
                            echo json_encode(array(
                                'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                            ));

                            $errorCount += 1;
                            break;
                        }

                        //Check maximum sum of factors
                        if($key == 'mainFactorStudent' || $key == 'coFactorStudent') {
                            $sumFactors = $quota['mainFactorStudent'] + $quota['coFactorStudent'];

                            if($sumFactors != 1) {
                                http_response_code(400);
                                echo json_encode(array(
                                    'message' => "Tổng hệ số hướng dẫn cho đề tài khóa luận phải bằng 1"
                                ));
                                $errorCount += 1;
                                break;
                            }
                        }

                        else if($key == 'mainFactorGraduated' || $key == 'coFactorGraduated') {
                            $sumFactors = $quota['mainFactorGraduated'] + $quota['coFactorGraduated'];
                            if($sumFactors != 1) {
                                http_response_code(400);
                                echo json_encode(array(
                                    'message' => "Tổng hệ số hướng dẫn cho đề tài luận văn phải bằng 1"
                                ));
                                $errorCount += 1;
                                break;
                            }
                        }

                        else if($key == 'mainFactorResearcher' || $key == 'coFactorResearcher') {
                            $sumFactors = $quota['mainFactorResearcher'] + $quota['coFactorResearcher'];
                            if($sumFactors != 1) {
                                http_response_code(400);
                                echo json_encode(array(
                                    'message' => "Tổng hệ số hướng dẫn cho đề tài luận án phải bằng 1"
                                ));
                                $errorCount += 1;
                                break;
                            }
                        }
                    }
                    if($errorCount > 0) { break; }
                }

                //Update a quota by id
                QuotaTable::updateQuotaById($version, $quota);
            }
        } else {
            if(count($factors) > 0){
                echo "hel";
                $quotaObj = new Quota($factors);
                foreach ($factors as $key => $value) {
                    $action = 'check' . ucfirst($key);
                    if (!$quotaObj->$action()) {
                        http_response_code(400);
                        echo json_encode(array(
                            'id' => $factors['id'],
                            'message' => ucfirst(Constant::columnNames[$key]) . Constant::invalidText
                        ));

                        $errorCount += 1;
                        break;
                    }
                }

                if($errorCount == 0) {
                    QuotaTable::updateQuotaByVersion($version, $factors);
                }
            }
        }

        //Delete version if error occurs
        if($errorCount > 0) {
            return;
        } else {
            http_response_code(200);
            echo json_encode(array(
                'message' => Constant::success
            ));
        }
    }


    /**
     * API
     * activeQuotaVersion()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $version
     */
    public function activeQuotaVersion($param)
    {
        $version = $param['version'];

        switch ($_SESSION['role']) {
            case 0:
                break;
            default:
                http_response_code(403);
                echo json_encode(array(
                    'message' => Constant::notPermissionText
                ));
                return;
        }

        $json = file_get_contents('php://input');
        $obj = json_decode($json);

        if (property_exists($obj, 'isActive')) {
            $isActive = $obj->isActive;
        } else {
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['isActive']) . Constant::isRequiredText
            ));
            return;
        }

        if(is_null($obj->isActive) || !is_int($obj->isActive)){
            http_response_code(400);
            echo json_encode(array(
                'message' => ucfirst(Constant::columnNames['isActive']) . Constant::invalidText
            ));
            return;
        }

        if($isActive == 1){
            //Deactive other quota version
            QuotaTable::deActiveOtherQuotaVersion($version);
        }

        //Active or deactive current quota version
        QuotaTable::activeQuotaByVersion($version, $isActive);

        http_response_code(200);
        echo json_encode(array(
            'message' => Constant::success
        ));
    }

    /**
     * API
     * deleteQuotasByVersion()
     *
     * HOW-TO-DO: des
     * @param $param
     * @internal param $version
     */
    public function deleteQuotasByVersion($param)
    {
        $version = $param['version'];

        switch ($_SESSION['role']) {
            case 0:
                break;
            default:
                http_response_code(403);
                echo json_encode(array(
                    'message' => Constant::notPermissionText
                ));
                return;
        }

        $result = QuotaTable::deleteByVersion($version);

        if ($result['rowCount']) {
            http_response_code(204);
        } else {
            http_response_code(400);
            echo json_encode(array(
                'message' => "Không thể xóa hoặc phiên bản định mức đang có hiệu lực"
            ));
        }
    }
}