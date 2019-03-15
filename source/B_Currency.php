<?php
/**
 * Created by PhpStorm.
 * User: Yusuf Abdillah Putra
 * Date: 17/01/2019
 * Time: 14:26
 */

use Slim\Http\Response;
use Slim\Http\Request;

class B_Currency extends Library {

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
        $deklarasi['tabel'] = 'tbl_mstcurrency';
        $deklarasi['pk'] = 'idCurrency';
    }

    protected function getAll() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $Fetch = $this->qb->table($this->tabel)
                ->get();
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    private function getIDR() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $Fetch = $this->qb->table($this->tabel)
                ->where($this->pk, 'IDR')
                ->get();
            $Data = json_decode($Fetch[0]->dataCurrency);
            if ($Fetch) {
                return $response->withJson($Data, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    private function getAUD() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $Fetch = $this->qb->table($this->tabel)
                ->where($this->pk, 'AUD')
                ->get();
            $Data = json_decode($Fetch[0]->dataCurrency);
            if ($Fetch) {
                return $response->withJson($Data, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    private function getEUR() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $Fetch = $this->qb->table($this->tabel)
                ->where($this->pk, 'EUR')
                ->get();
            $Data = json_decode($Fetch[0]->dataCurrency);
            if ($Fetch) {
                return $response->withJson($Data, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    private function getUSD() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $Fetch = $this->qb->table($this->tabel)
                ->where($this->pk, 'USD')
                ->get();
            $Data = json_decode($Fetch[0]->dataCurrency);
            if ($Fetch) {
                return $response->withJson($Data, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    private function getSGD() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $Fetch = $this->qb->table($this->tabel)
                ->where($this->pk, 'SGD')
                ->get();
            $Data = json_decode($Fetch[0]->dataCurrency);
            if ($Fetch) {
                return $response->withJson($Data, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    /**
     * todo : Data currency ngambil API darimana ? to -> Fauzan Syabil
     */

    protected function put() {
        $this->app->put($this->pattern.'/', function(Request $request, Response $response) {
           return null;
        });
    }

}