<?php
/**
 * Created by PhpStorm.
 * User: Yusuf Abdillah Putra
 * Date: 17/01/2019
 * Time: 14:26
 */

use Slim\Http\Response;
use Slim\Http\Request;

class F_Suratterima extends Library {

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
        $deklarasi['view'] = 'vw_trnsuratterima';
        $deklarasi['tabel'] = 'tbl_trnsuratterima';
        $deklarasi['pk'] = 'idST';
    }

    protected function getAll() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $Fetch = $this->qb->table($this->view)->get();
            if (!empty($Fetch)) {
                return $response->withJson($Fetch, 200);
            } if (empty($Fetch)) {
                return $response->withJson(["status" => "empty"], 200);
            } else {
                return $response->withJson(["status" => "failed"], 500);
            }
        })->add(parent::middleware());
    }

    private function getByPerusahaan() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Fetch = $this->qb->table($this->view)
                ->where('idPerusahaan', $dataParsed['idPerusahaan'])
                ->orderBy('idST', 'desc')
                ->get();
            if (!empty($Fetch)) {
                return $response->withJson($Fetch, 200);
            } if (empty($Fetch)) {
                return $response->withJson(["status" => "empty"], 200);
            } else {
                return $response->withJson(["status" => "failed"], 500);
            }
        })->add(parent::middleware());
    }

    protected function getData() {
        $this->app->get($this->pattern.'/{VALUE_DATA}[/{KOLOM}]', function(Request $request, Response $response, $args) {
            $value_data = $args['VALUE_DATA'];
            if (empty($args['KOLOM'])) {
                $Fetch = $this->qb
                    ->table($this->view)
                    ->where($this->pk, $value_data)
                    ->first();
            } if (!empty($args['KOLOM'])) {
                $kolom = $args['KOLOM'];
                $Fetch = $this->qb
                    ->table($this->view)
                    ->where($kolom, $value_data)
                    ->orWhere($kolom, 'like', '%'.$value_data.'%')
                    ->first();
            }
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
                ->insertGetId($dataParsed);
            if ($Post) {
                return $response->withJson(["status" => "success", $this->pk => $Post], 200);
            } else {
                return $response->withJson(["status" => "failed"], 500);
            }
        });
    }

    protected function put() {
        $this->app->put($this->pattern.'/', function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Update = $this->qb
                ->table($this->tabel)
                ->where($this->pk, $dataParsed[$this->pk])
                ->update($dataParsed);
            if ($Update) {
                return $response->withJson(["status" => "success"], 200);
            } else {
                return $response->withJson(["status" => "failed"], 500);
            }
        });
    }

    protected function delete() {
        $this->app->delete($this->pattern.'/', function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Delete = $this->qb
                ->table($this->tabel)
                ->where($this->pk, $dataParsed[$this->pk])
                ->delete();
            if ($Delete) {
                return $response->withJson(["status" => "success"], 200);
            } else {
                return $response->withJson(["status" => "failed"], 500);
            }
        });
    }

}