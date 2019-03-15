<?php
/**
 * Created by PhpStorm.
 * User: Yusuf Abdillah Putra
 * Date: 17/01/2019
 * Time: 14:26
 */

use Slim\Http\Response;
use Slim\Http\Request;

class B_Log extends Library {

    /**
     * @param $function
     * Tujuan : Digunakan untuk memanggil fungsi yang ada di kelas ini
     *          Konsep pemanggilannya diatur sesuai inputan url yang
     *          dimasukkan pengguna.
     * Eksekusi : permit_API/index.php
     *            $Run = new $__CLASS_API__($__FUNCTION_API__)
     *            $__CLASS_API__ : REQUEST_URI[2]
     *            $__FUNCTION_API__ : REQUEST_URI[3]
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     *
     * Cara memakai middleware(Pengecekan API Key) cukup tambahkan saja ->add(parent::middleware());
     */
    public function __construct($function)
    {
        parent::__construct();
        self::$function();
        return $this->app->run();
    }

    protected function getAll() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $Query = "CALL sp_mstutllogonline('getAll', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
            $Fetch = $this->db->query($Query)->fetchAll(PDO::FETCH_OBJ);
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    protected function getData() {
        $this->app->get($this->pattern.'/{VALUE_DATA}[/{KOLOM}]', function(Request $request, Response $response, $args) {
            $value_data = $args['VALUE_DATA'];
            if (empty($args['KOLOM'])) {
                $Query = "CALL sp_mstutllogonline(
                        'getData', 
                        'idLog', 
                        $value_data, 
                        NULL, 
                        NULL, 
                        NULL, 
                        NULL, 
                        NULL, 
                        NULL, 
                        NULL, 
                        NULL, 
                        NULL, 
                        NULL
                 );";
            } if (!empty($args['KOLOM'])) {
                $kolom = $args['KOLOM'];
                $Query = "CALL sp_mstutllogonline(
                        'getData', 
                        'idUser', 
                        NULL, 
                        '$value_data', 
                        NULL, 
                        NULL, 
                        NULL, 
                        NULL, 
                        NULL, 
                        NULL, 
                        NULL, 
                        NULL, 
                        NULL
                );";
            }
            $Fetch = $this->db->query($Query)->fetchAll(PDO::FETCH_OBJ);
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    protected function post() {
        $this->app->post($this->pattern.'/', function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Query = "CALL sp_mstutllogonline(
                        'create', 
                        NULL,
                        NULL, 
                         '$dataParsed[idUser]',
                         '$dataParsed[tanggalLog]',
                         '$dataParsed[signinLog]',
                         NULL,
                         '$dataParsed[hostnameLog]',
                         '$dataParsed[ipaddressLog]',
                         '$dataParsed[deviceLog]',
                         '$dataParsed[browserLog]',
                         '$dataParsed[platformLog]',
                         '$dataParsed[useragentLog]'
            );";
            $Exec = $this->db->prepare($Query)->execute();
            $stmt = $this->db->query("SELECT LAST_INSERT_ID() as 'idLog'")->fetch(PDO::FETCH_OBJ);
            $idLog = $stmt->idLog;
            if ($Exec) {
                return $response->withJson(["status" => "success", "idLog" => $idLog], 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        });
    }

    protected function put() {
        $this->app->put($this->pattern.'/', function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Query = "CALL sp_mstutllogonline(
                        'update', 
                        NULL,
                        $dataParsed[idLog], 
                         NULL,
                         NULL,
                         NULL,
                         '$dataParsed[signoutLog]',
                         NULL,
                         NULL,
                         NULL,
                         NULL,
                         NULL,
                         NULL
            );";
            $Exec = $this->db->prepare($Query)->execute();
            if ($Exec) {
                return $response->withJson(["status" => "success"], 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        });
    }

    protected function delete() {
        $this->app->delete($this->pattern.'/', function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Query = "CALL sp_mstutllogonline('delete', NULL, '$dataParsed[idLog]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
            $Exec = $this->db->prepare($Query)->execute();
            if ($Exec) {
                return $response->withJson(["status" => "success"], 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        });
    }

}