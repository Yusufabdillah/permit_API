<?php
/**
 * Created by PhpStorm.
 * User: Yusuf Abdillah Putra
 * Date: 17/01/2019
 * Time: 14:26
 */

use Slim\Http\Response;
use Slim\Http\Request;

class F_Dokumen extends Library {

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
        $deklarasi['view'] = 'vw_mstdokumen';
        $deklarasi['tabel'] = 'tbl_mstdokumen';
        $deklarasi['pk'] = 'idDokumen';
    }

    protected function getAll() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Fetch = $this->qb->table($this->view)
            ->whereRaw('idPerusahaan in (select idPerusahaan from tbl_utlperusahaanakses where idGrup='.$dataParsed['idGrup'].')')
            ->get();
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
	
	 private function getDokumenPerpanjangan() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $casenumberDokumen = !empty($dataParsed['casenumberDokumen']) ? "AND casenumberDokumen LIKE '%".$dataParsed['casenumberDokumen']."%'" : "";
            $judulDokumen = !empty($dataParsed['judulDokumen']) ? "AND namaJudul LIKE '%".$dataParsed['judulDokumen']."%'" : "";
            $idPerusahaan= !empty($dataParsed['idPerusahaan']) ? "AND idPerusahaan = ".$dataParsed['idPerusahaan'] : "";
            $Query = "SELECT * FROM ".$this->view." WHERE ".$this->pk." IS NOT NULL ".$casenumberDokumen." ".$judulDokumen." ".$idPerusahaan;
            $Fetch = $this->db->query($Query)->fetchAll(PDO::FETCH_OBJ);
            if (empty($Fetch)) {
                return $response->withJson(['status' => 'empty'], 200);
            } else if (!empty($Fetch)) {
                return $response->withJson(['status' => 'success', 'data' => $Fetch], 200);
            } else {
                return $response->withJson(["status" => "failed"], 500);
            }
        });
    }

    protected function ajaxSearchDokumen(){
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            // if (empty($dataParsed['casenumber'])) {
            //     $Fetch = $this->qb
            //          ->table('vw_mstdokumen')
            //          ->whereNotNull('idDokumen')
            //          ->get();
            // } else if (!empty($dataParsed['casenumber'])) {
            //     $Fetch = $this->qb
            //          ->table('vw_mstdokumen')
            //          ->whereNotNull('idDokumen')
            //          ->where('casenumberDokumen', 'like', "%".$dataParsed['casenumber']."%")
            //          ->get();
            // }
            if (!empty($dataParsed['casenumber'])) {
                $querycasenumber="and casenumberDokumen like '%$dataParsed[casenumber]%'";
            }else{
                $querycasenumber="";
            }
            if(!empty($dataParsed['perusahaan'])){
                $queryperusahaan="and idPerusahaan = $dataParsed[perusahaan]";
            }else{
                $queryperusahaan="";
            }
             if(!empty($dataParsed['bulan'])){
                $querybulan="and MONTH(tgl_habis_berlakuDokumen) = $dataParsed[bulan]";
            }else{
                $querybulan="";
            }
             if(!empty($dataParsed['tahun'])){
                $querytahun="and YEAR(tgl_habis_berlakuDokumen) = $dataParsed[tahun]";
            }else{
                $querytahun="";
            }


            if(!empty($dataParsed['params'])){
                // $this->db->like('JudulTask',$params['search']['keywords']);
                $queryjudul="and namaJudul like '%$dataParsed[params]%'";
            }else{
                $queryjudul="";
            }
            // if (empty($dataParsed['casenumber'])) {
            $Query = "Select * from vw_mstdokumen where idDokumen is not null $querycasenumber $queryperusahaan $queryjudul $querybulan $querytahun";
            // }
            $Fetch = $this->db->query($Query)->fetchAll(PDO::FETCH_OBJ);
            if (empty($Fetch)) {
                return $response->withJson(['status' => 'empty'], 200);
            } elseif (!empty($Fetch)) {
                return $response->withJson(['status' => 'success', 'data' => $Fetch], 200);
            }
            else {
                return $response->withJson(["status" => "failed"], 500);
            }
        });
    }

     private function getAllExpired() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            // $Fetch = $this->qb
            //     ->table($this->view)
            //     ->where('tglAwal','<>',0)
            //     ->where('tglAwal','=>','2019-02-28')
            //     ->get();
            $tanggal=date('Y-m-d');
            $Query = "Select * from vw_mstdokumen where (NOW() BETWEEN DATE_ADD(tgl_habis_berlakuDokumen,INTERVAL-1 * (900) day) and tgl_habis_berlakuDokumen) and tgl_habis_berlakuDokumen is not null and tgl_habis_berlakuDokumen != '0000-00-00'";
            $Fetch = $this->db->query($Query)->fetchAll(PDO::FETCH_OBJ);
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 500);
            }
        });
    }

    private function getDataBySiteExpired() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $tanggal=date('Y-m-d');
            $Query = "Select * from vw_mstdokumen where NOW() BETWEEN (tgl_habis_berlakuDokumen - INTERVAL(900) day) AND tgl_habis_berlakuDokumen and idPerusahaan in (select idPerusahaan from tbl_utlperusahaanakses where idGrup=$dataParsed[idGrup])  and tgl_habis_berlakuDokumen is not null";
            $Fetch = $this->db->query($Query)->fetchAll(PDO::FETCH_OBJ);
            if (!empty($Fetch)) {
                return $response->withJson(['status' => 'success', 'data' => $Fetch], 200);
            } else if (empty($Fetch)) {
                return $response->withJson(["status" => "empty"], 200);
            } else {
                return $response->withJson(["status" => "failed"], 500);
            }
        });
    }

    protected function getDataExpired() {
        // $tanggal=date('Y-m-d');
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