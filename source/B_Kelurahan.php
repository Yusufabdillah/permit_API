<?php
/**
 * Created by PhpStorm.
 * User: Yusuf Abdillah Putra
 * Date: 17/01/2019
 * Time: 14:26
 */

use Slim\Http\Response;
use Slim\Http\Request;

class B_Kelurahan extends Library {

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
            $Query = "CALL sp_mstkelurahan(
                        'getAll', 
                        NULL, 
                        NULL, 
                        NULL, 
                        NULL, 
                        NULL, 
                        NULL
                      );";
            $Fetch = $this->db->query($Query)->fetchAll(PDO::FETCH_OBJ);
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 500);
            }
        })->add(parent::middleware());
    }

    protected function getData() {
        $this->app->get($this->pattern.'/{VALUE_DATA}[/{KOLOM}]', function(Request $request, Response $response, $args) {
            $value_data = $args['VALUE_DATA'];
            if (empty($args['KOLOM'])) {
                $Query = "CALL sp_mstkelurahan(
                            'getData', 'idKelurahan', $value_data, NULL, NULL, NULL, NULL
                );";
                $Fetch = $this->db->query($Query)->fetch(PDO::FETCH_OBJ);
            } if (!empty($args['KOLOM'])) {
                $kolom = $args['KOLOM'];
                if ($kolom == 'idKecamatan') {
                    $Query = "CALL sp_mstkelurahan(
                            'getData', 'idKecamatan', NULL, $value_data, NULL, NULL, NULL
                    );";
                } else if ($kolom == 'namaKelurahan') {
                    $Query = "CALL sp_mstkelurahan(
                            'getData', 'namaKelurahan', NULL, NULL, '%$value_data%', NULL, NULL
                    );";
                }
                $Fetch = $this->db->query($Query)->fetchAll(PDO::FETCH_OBJ);
            }
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 500);
            }
        })->add(parent::middleware());
    }

    protected function post() {
        $this->app->post($this->pattern.'/', function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Query = "CALL sp_mstkelurahan(
                        'create', NULL, NULL, $dataParsed[idKecamatan], '$dataParsed[namaKelurahan]', '$dataParsed[createdBy]', NULL
                      );";
            $Exec = $this->db->prepare($Query)->execute();
            if ($Exec) {
                return $response->withJson(["status" => "success"], 200);
            } else {
                return $response->withJson(["status" => "failed"], 500);
            }
        });
    }

    protected function put() {
        $this->app->put($this->pattern.'/', function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Query = "CALL sp_mstkelurahan(
                        'update', NULL, $dataParsed[idKelurahan], $dataParsed[idKecamatan], '$dataParsed[namaKelurahan]', NULL, '$dataParsed[updatedBy]'
            );";
            $Exec = $this->db->prepare($Query)->execute();
            if ($Exec) {
                return $response->withJson(["status" => "success"], 200);
            } else {
                return $response->withJson(["status" => "failed"], 500);
            }
        });
    }

    protected function delete() {
        $this->app->delete($this->pattern.'/', function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Query = "CALL sp_mstkelurahan(
                    'delete', NULL, $dataParsed[idKelurahan], NULL, NULL, NULL, NULL
            );";
            $Exec = $this->db->prepare($Query)->execute();
            if ($Exec) {
                return $response->withJson(["status" => "success"], 200);
            } else {
                return $response->withJson(["status" => "failed"], 500);
            }
        });
    }

}