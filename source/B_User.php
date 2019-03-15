<?php
/**
 * Created by PhpStorm.
 * User: Yusuf Abdillah Putra
 * Date: 17/01/2019
 * Time: 14:26
 */

use Slim\Http\Response;
use Slim\Http\Request;

class B_User extends Library {

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
            $Query = "CALL sp_mstuser('getAll', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
            $Fetch = $this->db->query($Query)->fetchAll(PDO::FETCH_OBJ);
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }
	
	private function getKoordinator() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $Fetch = $this->qb->table('vw_userkoordinator')->get();
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 500);
            }
        })->add(parent::middleware());
    }

    private function getKoordinatorPerusahaan() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Fetch = $this->qb->table('vw_userkoordinator')
                ->where('idPerusahaan', $dataParsed['idPerusahaan'])
                ->get();
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 500);
            }
        });
    }

    private function getOSS() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $Fetch = $this->qb->table('vw_useross')->get();
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
                $Query = "CALL sp_mstuser('getData', 'idUser', '$value_data', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
                $Fetch = $this->db->query($Query)->fetch(PDO::FETCH_OBJ);
            } if (!empty($args['KOLOM'])) {
                $kolom = $args['KOLOM'];
                if ($kolom == 'idPerusahaan') {
                    $Query = "CALL sp_mstuser('getData', 'idPerusahaan', NULL, $value_data, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
                } if ($kolom !== 'idPerusahaan') {
                    $Query = "CALL sp_mstuser('getData', 'namaUser', NULL, NULL, NULL, NULL, NULL, NULL, '%$value_data%', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
                }
                $Fetch = $this->db->query($Query)->fetchAll(PDO::FETCH_OBJ);
            }
            if (!empty($Fetch)) {
                return $response->withJson($Fetch, 200);
            } if (empty($Fetch)) {
                return $response->withJson(["status" => "empty"], 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    protected function post() {
        $this->app->post($this->pattern.'/', function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Query = "CALL sp_mstuser(
                        'create', 
                        NULL,
                        '$dataParsed[idUser]', 
                        $dataParsed[idPerusahaan], 
                        $dataParsed[idDivisi], 
                        $dataParsed[idDepartemen], 
                        $dataParsed[idJabatan], 
                        $dataParsed[idGrup], 
                        '$dataParsed[namaUser]', 
                        '$dataParsed[passUser]', 
                        '$dataParsed[telpUser]', 
                        $dataParsed[statusUser], 
                        $dataParsed[statusAPI], 
                        $dataParsed[statusPIC], 
                        $dataParsed[statusOSS], 
                        '$dataParsed[createdBy]', 
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

    protected function put() {
        $this->app->put($this->pattern.'/', function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Query = "CALL sp_mstuser(
                        'update', 
                        NULL,
                        '$dataParsed[idUser]', 
                        $dataParsed[idPerusahaan], 
                        $dataParsed[idDivisi], 
                        $dataParsed[idDepartemen], 
                        $dataParsed[idJabatan], 
                        $dataParsed[idGrup], 
                        '$dataParsed[namaUser]', 
                        '$dataParsed[passUser]', 
                        '$dataParsed[telpUser]', 
                        $dataParsed[statusUser], 
                        $dataParsed[statusAPI], 
                        $dataParsed[statusPIC],
                        $dataParsed[statusOSS], 
                        NULL, 
                        '$dataParsed[updatedBy]'
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
            $Query = "CALL sp_mstuser('delete', NULL, '$dataParsed[idUser]', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
            $Exec = $this->db->prepare($Query)->execute();
            if ($Exec) {
                return $response->withJson(["status" => "success"], 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        });
    }

    public function login() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Query = "CALL sp_mstuser('login', NULL, '$dataParsed[idUser]', NULL, NULL, NULL, NULL, NULL, NULL, '$dataParsed[passUser]', NULL, NULL, NULL, NULL, NULL, NULL, NULL);";
            $Fetch = $this->db->query($Query)->fetch(PDO::FETCH_OBJ);
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        });
    }

}