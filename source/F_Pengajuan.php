<?php
/**
 * Created by PhpStorm.
 * User: Yusuf Abdillah Putra
 * Date: 17/01/2019
 * Time: 14:26
 */

use Slim\Http\Response;
use Slim\Http\Request;

class F_Pengajuan extends Library {

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
        $deklarasi['view'] = 'vw_trnpengajuan';
        $deklarasi['tabel'] = 'tbl_trnpengajuan';
        $deklarasi['pk'] = 'idPengajuan';
        $deklarasi['ses_koordinator'] = 3;
    }

    protected function getAll() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Fetch = $this->qb->table($this->view)
            ->whereRaw("idPerusahaan in (select idPerusahaan from tbl_utlperusahaanakses where idGrup=$dataParsed[idGrup])")
            ->get();
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 500);
            }
        })->add(parent::middleware());
    }

    private function getDraft() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Fetch = $this->qb->table($this->view)
                ->where('draft_status', true)
                ->where('draft_createdby', parent::decode_str($dataParsed['idUser']))
                ->where('submit_status', false)
                ->where('idEstafet', null)
                ->whereRaw('idPerusahaan in (select idPerusahaan from tbl_utlperusahaanakses where idGrup='.$dataParsed['idGrup'].')')
                ->orderBy('idPengajuan', 'desc')
                ->get();
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    private function getSubmit() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Fetch = $this->qb->table($this->view)
                ->where('draft_status', true)
                ->where('submit_status', true)
                ->where('approve_status', false)
                ->whereRaw('idPerusahaan in (select idPerusahaan from tbl_utlperusahaanakses where idGrup='.$dataParsed['idGrup'].')')
                ->orderBy('idPengajuan', 'desc')
                ->get();
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    private function getPending() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Fetch = $this->qb->table($this->view)
                ->where('draft_status', true)
                ->where('submit_status', true)
                ->where('approve_status', true)
                ->where('pending_status', true)
                ->whereRaw('idPerusahaan in (select idPerusahaan from tbl_utlperusahaanakses where idGrup='.$dataParsed['idGrup'].')')
                ->orderBy('idPengajuan', 'desc')
                ->get();
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    private function getPosting() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Fetch = $this->qb->table($this->view)
                ->where('draft_status', true)
                ->where('submit_status', true)
                ->where('approve_status', true)
                ->where('srtterima_status', true)
                ->where('dokumen_status', false)
                ->where('estafet_status', false)
                ->whereRaw('idPerusahaan in (select idPerusahaan from tbl_utlperusahaanakses where idGrup='.$dataParsed['idGrup'].')')
                ->orderBy('idPengajuan', 'desc')
                ->get();
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    private function getEstafet() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            /**
             * Jika koordinator maka data yang ditampilkan sesuai dengan sesi perusahaan yang aktif
             */
            if ($dataParsed['idAkses'] == $this->ses_koordinator) {
                $Fetch = $this->qb->table($this->view)
                    ->where('idEstafet', '!=', null)
                    ->where('estafet_status', true)
                    ->whereRaw('idPerusahaan in (select idPerusahaan from tbl_utlperusahaanakses where idGrup='.$dataParsed['idGrup'].')')
                    ->orderBy('idPengajuan', 'desc')
                    ->get();
            }
            /**
             * Jika permit officer maka data yang ditampilkan sesuai dengan sesi perusahaan PIC yang ditunjuk
             */
            else if ($dataParsed['idAkses'] !== $this->ses_koordinator) {
                $Fetch = $this->qb->table($this->view)
                    ->where('draft_createdby', parent::decode_str($dataParsed['idUser']))
                    ->whereRaw('idPerusahaan in (select idPerusahaan from tbl_utlperusahaanakses where idGrup='.$dataParsed['idGrup'].')')
                    ->where('draft_status', true)
                    ->where('submit_status', false)
                    ->where('idEstafet', "!=", null)
                    ->orderBy('idPengajuan', 'desc')
                    ->get();
            }
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    private function declineEstafet() {
        $this->app->put($this->pattern.'/', function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Query = "UPDATE tbl_trnpengajuan SET
                    idPerusahaan = $dataParsed[idPerusahaan], 
                    idEstafet = NULL,
                    estafet_status = 0,
                    ktrDecline_estafet = '$dataParsed[ktrDecline_estafet]'
                 WHERE idPengajuan = $dataParsed[idPengajuan];";
            $Exec = $this->db->prepare($Query)->execute();
            if ($Exec) {
                return $response->withJson(["status" => "success"], 200);
            } else {
                return $response->withJson(["status" => "failed"], 500);
            }
        });
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
                return $response->withJson(["status" => "success", 'idPengajuan' => $Post], 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
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
                return $response->withJson(["status" => "failed"], 200);
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