 <?php
    function selisih($jam_masuk, $jam_keluar)
    {
        list($h, $m, $s) = explode(":", $jam_masuk);
        $dtAwal = mktime($h, $m, $s, 1, 1, 1);
        list($h2, $m2, $s2) = explode(":", $jam_keluar);
        $dtAkhir = mktime($h2, $m2, $s2, 1, 1, 1);
        $dtSelisih = $dtAkhir - $dtAwal;
        $totalmenit = $dtSelisih / 60;
        $jam = floor($totalmenit / 60);
        $menit = floor($totalmenit % 60);
        $detik = $dtSelisih % 60;
        return $jam . ":" . str_pad($menit, 2, '0', STR_PAD_LEFT) . ":" . str_pad($detik, 2, '0', STR_PAD_LEFT);
    }
    ?>
