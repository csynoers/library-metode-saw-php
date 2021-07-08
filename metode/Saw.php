<?php

    /**
     * Implementasi metode SAW
     */
    class Saw
    {       
        private static $_instance = null;

        // variable awal saw
        protected static $kriteria = [];
        protected static $alternatif = [];

        protected static $init = [];
        protected static $matrik = null; # generate dalam bentuk matrik dengan HTML Table
        protected static $table = null; # generate dalam bentuk tabel dengan HTML Table
        protected static $proses = null; # generate proses normalisasi dengan HTML Table
        protected static $normalisasi = [];
        protected static $hasil = [];

    
        private function __construct () {
            
        }
    
        public static function getInstance ()
        {
            if (self::$_instance === null) {
                self::$_instance = new self;
            }
    
            return self::$_instance;
        }

        public function setKriteria(array $kriteria)
        {
            self::$kriteria = $kriteria;
            return $this;
        }

        public function setAlternatif(array $alternatif)
        {
            self::$alternatif = $alternatif;
            return $this;
        }

        public function showData(string $data)
        {
            $function = "showData{$data}";
            return self::$function();
        }

        protected function showDataKriteria()
        {
            return self::$kriteria;
        }
        protected function showDataAlternatif()
        {
            return self::$alternatif;
        }

        /**
         * menampilkan data awal dari kriteria dan alternatif dalam bentuk tabel
         * 
        */
        protected function showDataTable()
        {
            // create header table
            $contentHeader = [];
            foreach (self::$kriteria as $key => $value) {
                $contentHeader[] = "<td>{$value['code']}: {$value['title']}</td>";
            }
            $contentHeader = implode('',$contentHeader);

            // create content table
            $contentBody = [];
            foreach (self::$alternatif as $key => $value) {
                $contentKriteriaAlternatif = [];
                foreach ($value['kriteria'] as $keyKriteriaAlternatif => $valueKriteriaAlternatif) {
                    $contentKriteriaAlternatif[] = "<td>$valueKriteriaAlternatif</td>";
                }
                $contentKriteriaAlternatif = implode('',$contentKriteriaAlternatif);

                $contentBody[] = "<tr><td>{$value['code']}: {$value['title']}</td>{$contentKriteriaAlternatif}</tr>";
            }
            $contentBody = implode('',$contentBody);

            return "
                <style>
                    #tableData, #tableData th, #tableData td {
                        border: 1px solid black;
                        border-collapse: collapse;
                    }
                    #tableData {
                        width: 100%;
                        text-align: center;
                    }
                </style>
                <h5>Tabel Informasi Alternatif pada setiap kriteria :</h5>
                <table id='tableData'>
                    <tr>
                        <th rowspan='2'>Alternatif</th>
                        <th colspan='4'>Kriteria</th>
                    </tr>
                    <tr>{$contentHeader}</tr>
                    {$contentBody}
                </table>
            ";
        }

        protected function showDataMatrikKeputusan ()
        {
            $contentBody = [];
            foreach (self::$alternatif as $key => $value) {
                $contentKriteriaAlternatif = [];
                foreach ($value['kriteria'] as $keyKriteriaAlternatif => $valueKriteriaAlternatif) {
                    $contentKriteriaAlternatif[] = "<td>$valueKriteriaAlternatif</td>";
                }
                $contentKriteriaAlternatif = implode('',$contentKriteriaAlternatif);

                $contentBody[] = "<tr>{$contentKriteriaAlternatif}</tr>";
            }
            $contentBody = implode('',$contentBody);

            return "
                <style>
                    #tableDataMatrikKeputusan {
                        border: 1px solid black;
                        border-collapse: collapse;
                        width: 100%;
                        text-align: center;
                    }
                    table#tableDataMatrikKeputusan::before {
                        content: '';
                        width: 90vw;
                        background-color: #fff;
                        height: 1px;
                        display: block;
                        position: absolute;
                        z-index: 2;
                        margin-top: -1px;
                        margin-left: 3vw;
                    }
                    table#tableDataMatrikKeputusan::after {
                        content: '';
                        width: 90vw;
                        background-color: #fff;
                        height: 1px;
                        display: block;
                        position: absolute;
                        z-index: 2;
                        margin-bottom: -1px;
                        margin-left: 3vw;
                    }
                </style>
                <h5>Matrik Keputusan :</h5>
                <table id='tableDataMatrikKeputusan'>
                    ".$contentBody."
                </table>
            ";
        }

        protected function showDataNormalisasi()
        {
            $normalisasi = [];
    
            for ($i=0; $i < count(self::$kriteria) ; $i++) {             
                for ($j=0; $j < count(self::$alternatif) ; $j++) { 
                    $barisKolom = ('r').($j+1).($i+1);
                    $barisKolomValue = self::$alternatif[$j]['kriteria'][$i];
                    $normalisasiData = self::normalisasiMinMax();
                    
                    if ( self::$kriteria[$i]['type'] == 'cost' ) { // jika cost gunakan fungsi minimal
                        
                        $normalisasi['html'][] = "
                            <table class='table-data-normalisasi'>
                                <tr>
                                    <td rowspan='2' width='10%'>{$barisKolom}</td>
                                    <td rowspan='2' width='2%'>=</td>
                                    <td style='border-bottom: 1px solid;' width='40%'>min{".implode(',',$normalisasiData[$i])."}</td>
                                    <td rowspan='2' width='2%'>=</td>
                                    <td style='border-bottom: 1px solid;' width='10%'>".min($normalisasiData[$i])."</td>
                                    <td rowspan='2' width='2%'>=</td>
                                    <td rowspan='2' width='40%'>".(min($normalisasiData[$i])/$barisKolomValue)."</td>
                                </tr>
                                <tr>
                                    <td>{$barisKolomValue}</td>
                                    <td>{$barisKolomValue}</td>
                                </tr>
                            </table>
                        ";
                    } else if ( self::$kriteria[$i]['type'] == 'benefit' ) { // jika benefit gunakan fungsi maksimal
                        $normalisasi['html'][] = "
                            <table class='table-data-normalisasi'>
                                <tr>
                                    <td rowspan='2' width='10%'>{$barisKolom}</td>
                                    <td rowspan='2' width='2%'>=</td>
                                    <td style='border-bottom: 1px solid;' width='40%'>{$barisKolomValue}</td>
                                    <td rowspan='2' width='2%'>=</td>
                                    <td style='border-bottom: 1px solid;' width='10%'>{$barisKolomValue}</td>
                                    <td rowspan='2' width='2%'>=</td>
                                    <td rowspan='2' width='40%'>".($barisKolomValue/max($normalisasiData[$i]))."</td>
                                </tr>
                                <tr>
                                    <td>max{".implode(',',$normalisasiData[$i])."}</td>
                                    <td>".max($normalisasiData[$i])."</td>
                                </tr>
                            </table>
                        ";
                    }
                }
            }           
    
                    
    
            // ambil posisi 
            $normalisasi = implode('',$normalisasi['html']);
            
            return "
                <style>
                    .table-data-normalisasi {
                        border: 1px solid black;
                        width: 100%;
                        padding: 15px;
                        margin-bottom: 15px;
                    }
                </style>
                <h5>Normalisasi data dari matrik keputusan :</h5>
                {$normalisasi}
            ";
        }
        protected function showDataNormalisasiHasil()
        {
            $normalisasi = [];
    
            for ($i=0; $i < count(self::$kriteria) ; $i++) {             
                for ($j=0; $j < count(self::$alternatif) ; $j++) { 
                    $barisKolom = ('r').($j+1).($i+1);
                    $barisKolomValue = self::$alternatif[$j]['kriteria'][$i];
                    $normalisasiData = self::normalisasiMinMax();
                    
                    if ( self::$kriteria[$i]['type'] == 'cost' ) { // jika cost gunakan fungsi minimal
                        $normalisasi[$barisKolom] = (min($normalisasiData[$i])/$barisKolomValue);
                    } else if ( self::$kriteria[$i]['type'] == 'benefit' ) { // jika benefit gunakan fungsi maksimal
                        $normalisasi[$barisKolom] = ($barisKolomValue/max($normalisasiData[$i]));
                    }
                }
            }           
            
            return $normalisasi;
        }

        protected function showDataNormalisasiArray()
        {
            $normalisasi = [];
    
            for ($i=0; $i < count(self::$kriteria) ; $i++) {             
                for ($j=0; $j < count(self::$alternatif) ; $j++) { 
                    $barisKolom = ('r').($j).($i);
                    $barisKolomValue = self::$alternatif[$j]['kriteria'][$i];
                    $normalisasiData = self::normalisasiMinMax();
                    
                    if ( self::$kriteria[$i]['type'] == 'cost' ) { // jika cost gunakan fungsi minimal
                        $normalisasi[$j][$i] = (min($normalisasiData[$i])/$barisKolomValue);
                    } else if ( self::$kriteria[$i]['type'] == 'benefit' ) { // jika benefit gunakan fungsi maksimal
                        $normalisasi[$j][$i] = ($barisKolomValue/max($normalisasiData[$i]));
                    }
                }
            }           
            
            return $normalisasi;
        }
        protected function showDataNormalisasiMatrik()
        {
            $contentBody = [];
            foreach (self::showDataNormalisasiArray() as $key => $value) {
                $hasilNormalisasi = [];
                foreach ($value as $keySub => $valueSub) {
                    $hasilNormalisasi[] = "<td>$valueSub</td>";
                }
                $hasilNormalisasi = implode('',$hasilNormalisasi);

                $contentBody[] = "<tr>{$hasilNormalisasi}</tr>";
            }
            $contentBody = implode('',$contentBody);

            return "
                <style>
                    #tableNormalisasiMatrik {
                        border: 1px solid black;
                        border-collapse: collapse;
                        width: 100%;
                        text-align: center;
                    }
                    table#tableNormalisasiMatrik::before {
                        content: '';
                        width: 90vw;
                        background-color: #fff;
                        height: 1px;
                        display: block;
                        position: absolute;
                        z-index: 2;
                        margin-top: -1px;
                        margin-left: 3vw;
                    }
                    table#tableNormalisasiMatrik::after {
                        content: '';
                        width: 90vw;
                        background-color: #fff;
                        height: 1px;
                        display: block;
                        position: absolute;
                        z-index: 2;
                        margin-bottom: -1px;
                        margin-left: 3vw;
                    }
                </style>
                <h5>Matrik Ternormalisasi :</h5>
                <table id='tableNormalisasiMatrik'>
                    ".$contentBody."
                </table>
            ";
        }

        protected function normalisasiMinMax()
        {
            $alternatifByKriteria = [];
            for ($i=0; $i < count(self::$kriteria) ; $i++) { 
                $alternatifByKriteria[$i] = [];
                
                for ($j=0; $j < count(self::$alternatif) ; $j++) { 
                    array_push($alternatifByKriteria[$i],self::$alternatif[$j]['kriteria'][$i]);
                }
            }
    
            return $alternatifByKriteria;
        }

        protected function bobotDikaliMatrikNormalisasiProses()
        {
            $prosesPerhitungan = [];

            foreach (self::showDataNormalisasiArray() as $key => $value) {
                $prosesPerhitungan[$key] = null;
                $hasilPerhitungan = 0;
                foreach ($value as $keySub => $valueSub) {
                    $prosesPerhitungan[$key][] = "(".self::$kriteria[$keySub]['weight']."*{$valueSub})";

                    $hasilPerhitungan += (self::$kriteria[$keySub]['weight']*$valueSub);
                }
                $prosesPerhitungan[$key] = implode('+',$prosesPerhitungan[$key]);

                $prosesPerhitungan[$key] = "<tr>
                    <td>V".($key+1)."</td>
                    <td>=</td>
                    <td>{$prosesPerhitungan[$key]}</td>
                    <td>=</td>
                    <td>{$hasilPerhitungan}</td>
                </tr>";
            }
            $prosesPerhitungan = implode('',$prosesPerhitungan);

            return "
                <style>
                    #tableProsesBobotDikaliMatrikNormalisasi {
                        width: 100%;
                    }
                    #tableProsesBobotDikaliMatrikNormalisasi, #tableProsesBobotDikaliMatrikNormalisasi tr {
                        border: 1px solid black;
                        border-collapse: collapse;
                    }
                    #tableProsesBobotDikaliMatrikNormalisasi tr td {
                        padding: 15px;
                    }
                </style>
                <h5>Hasil Perhitungan :</h5>
                <table id='tableProsesBobotDikaliMatrikNormalisasi'>{$prosesPerhitungan}</table>
            ";
        }

        protected function bobotDikaliMatrikNormalisasiHasil()
        {
            $prosesPerhitungan = [];

            foreach (self::showDataNormalisasiArray() as $key => $value) {
                $prosesPerhitungan[$key] = null;
                $hasilPerhitungan = 0;
                foreach ($value as $keySub => $valueSub) {
                    $hasilPerhitungan += (self::$kriteria[$keySub]['weight']*$valueSub);
                }
                $prosesPerhitungan[$key] .= ' = '. $hasilPerhitungan;
            }

            return $prosesPerhitungan;
        }

        

        /**
         * Data
        */
        public function data()
        {
            self::$init = [
                'kriteria' => self::$kriteria,
                'alternatif' => self::$alternatif,
            ];

            self::$table = self::showDataTable();

            return $this;
        }

        /**
         * Keputusan
        */
        public function keputusan()
        {
            self::$matrik = self::showDataMatrikKeputusan();

            return $this;
        }

        /**
         * Normalisasi
        */
        public function normalisasi()
        {
            self::$proses = self::showDataNormalisasi();
            self::$hasil  = self::showDataNormalisasiHasil();
            self::$matrik = self::showDataNormalisasiMatrik();

            return $this;
        }

        /**
         * Perhitungan bobot preferensi W dikalikan  dengan matrik ternormalisasi R
         */
        public function bobotDikaliMatrikNormalisasi()
        {
            self::$proses = self::bobotDikaliMatrikNormalisasiProses();
            self::$hasil  = self::bobotDikaliMatrikNormalisasiHasil();

            // showDataNormalisasiArray()
            return $this;
        }

        /**
         * menampilkan data dalam bentuk table
         */
        public function init()
        {
            return self::$init;
        }

        /**
         * menampilkan data dalam bentuk table
         */
        public function table()
        {
            return self::$table;
        }

        /**
         * menampilkan data dalam bentuk matrik
         */
        public function matrik()
        {
            return self::$matrik;
        }

        /**
         * menampilkan data dalam bentuk proses
         */
        public function proses()
        {
            return self::$proses;
        }

        /**
         * menampilkan data dalam bentuk hasil
         */
        public function hasil()
        {
            return self::$hasil;
        }
    }

    
    // Example Usage:
    $saw = Saw::getInstance ();

    // masukan data kriteria dan alternatif
    $saw->setKriteria($kriteria)->setAlternatif($alternatif);
        
    echo '<pre>';
    print_r($saw->data()->init());
    echo '</pre>';
    print_r($saw->data()->table());
    print_r($saw->keputusan()->matrik());
    print_r($saw->normalisasi()->proses());
    print_r($saw->normalisasi()->matrik());
    print_r($saw->bobotDikaliMatrikNormalisasi()->proses());
    
