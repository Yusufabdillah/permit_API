<?php
/**
 * Created by PhpStorm.
 * User: Ozan
 * Date: 27/02/2019
 * Time: 11:53
 */

use Slim\Http\Response;
use Slim\Http\Request;

class B_menuAkses_frontend extends Library {

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
        self::deklarasi($this->deklarasi);
        self::$function();
        return $this->app->run();
    }

    private function deklarasi($deklarasi)
    {
        $deklarasi['tabel'] = 'tbl_utlmenuakses_frontend';
        $deklarasi['fk'] = 'idGrup';
    }

    protected function getAll_lv1() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $Query = "CALL sp_utlmenuakses_frontend('getAll_lv1', NULL, NULL, NULL);";
            $Fetch = $this->db->query($Query)->fetchAll(PDO::FETCH_OBJ);
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    protected function getAll_lv2() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $Query = "CALL sp_utlmenuakses_frontend('getAll_lv2', NULL, NULL, NULL);";
            $Fetch = $this->db->query($Query)->fetchAll(PDO::FETCH_OBJ);
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    protected function getData_lv1() {
        $this->app->get($this->pattern.'/{VALUE_DATA}[/{KOLOM}]', function(Request $request, Response $response, $args) {
            $value_data = $args['VALUE_DATA'];
            if (empty($args['KOLOM'])) {
                $Query = "CALL sp_utlmenuakses_frontend('getData_lv1', 'idGrup', $value_data, NULL);";
            } if (!empty($args['KOLOM'])) {
                $kolom = $args['KOLOM'];
                $Query = "CALL sp_utlmenuakses_frontend('getData_lv1', '$kolom', '%$value_data%', NULL);";
            }
            $Fetch = $this->db->query($Query)->fetchAll(PDO::FETCH_OBJ);
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    protected function getData_lv2() {
        $this->app->get($this->pattern.'/{VALUE_DATA}[/{KOLOM}]', function(Request $request, Response $response, $args) {
            $value_data = $args['VALUE_DATA'];
            if (empty($args['KOLOM'])) {
                $Query = "CALL sp_utlmenuakses_frontend('getData_lv2', 'idGrup', $value_data, NULL);";
            } if (!empty($args['KOLOM'])) {
                $kolom = $args['KOLOM'];
                $Query = "CALL sp_utlmenuakses_frontend('getData_lv2', '$kolom', '%$value_data%', NULL);";
            }
            $Fetch = $this->db->query($Query)->fetchAll(PDO::FETCH_OBJ);
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    protected function getMenu_lv1() {
        $this->app->get($this->pattern.'/{VALUE_DATA}[/{KOLOM}]', function(Request $request, Response $response, $args) {
            $value_data = $args['VALUE_DATA'];
            if (empty($args['KOLOM'])) {
                $Query = "CALL sp_utlmenuakses_frontend('getMenu_lv1', 'idGrup', $value_data, NULL);";
            } if (!empty($args['KOLOM'])) {
                $kolom = $args['KOLOM'];
                $Query = "CALL sp_utlmenuakses_frontend('getMenu_lv1', '$kolom', '%$value_data%', NULL);";
            }
            $Fetch = $this->db->query($Query)->fetchAll(PDO::FETCH_OBJ);
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    protected function getMenu_lv2() {
        $this->app->get($this->pattern.'/{VALUE_DATA}[/{KOLOM}]', function(Request $request, Response $response, $args) {
            $value_data = $args['VALUE_DATA'];
            if (empty($args['KOLOM'])) {
                $Query = "CALL sp_utlmenuakses_frontend('getMenu_lv2', 'idGrup', $value_data, NULL);";
            } if (!empty($args['KOLOM'])) {
                $kolom = $args['KOLOM'];
                $Query = "CALL sp_utlmenuakses_frontend('getMenu_lv2', '$kolom', '%$value_data%', NULL);";
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
            $Post = $this->qb
                ->table($this->tabel)
                ->insert($dataParsed);
            if ($Post) {
                return $response->withJson(["status" => "success"], 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        });
    }

    protected function delete() {
        $this->app->delete($this->pattern.'/', function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Query = "CALL sp_utlmenuakses_frontend('delete', NULL, $dataParsed[idGrup], NULL);";
            $Exec = $this->db->prepare($Query)->execute();
            if ($Exec) {
                return $response->withJson(["status" => "success"], 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        });
    }

}