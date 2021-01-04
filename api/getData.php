<?php
	include 'header.php';

	try {
		$val['data'] = array();
		$totalJam = $totalMenit = 0;
		$year = array();

		$bulan = (isset($_POST['bulan'])) ? $_POST['bulan'] : null;
		$tahun = (isset($_POST['tahun'])) ? $_POST['tahun'] : null;

		if ($bulan) {
			$bulanValue = [
				'Januari' => '-01-',
				'Februari' => '-02-',
				'Maret' => '-03-',
				'April' => '-04-',
				'Mei' => '-05-',
				'Juni' => '-06-',
				'Juli' => '-07-',
				'Agustus' => '-08-',
				'September' => '-09-',
				'Oktober' => '-10-',
				'November' => '-11-',
				'Desember' => '-12-'
			];
			foreach ($bulanValue as $key => $value) {
				if ($key == $bulan) $bulan = $value;
			}
		}

		$date = ($bulan && $tahun) ? $tahun . $bulan : date('Y-m-');
		$val['thisYear'] = ($tahun) ? $tahun : date('Y');
		$val['thisMonth'] = ($bulan) ? substr($bulan, 1, 2) : date('m');

		$sql = "SELECT * FROM time WHERE date LIKE '$date%' ORDER BY date DESC";
		if($query = mysqli_query($conn, $sql)){
			while ($re = mysqli_fetch_assoc($query)) {
				$istirahatJam = ((new DateTime($re['start_istirahat']))->diff(new DateTime($re['end_istirahat'])))->h;
				$istirahatMenit = ((new DateTime($re['start_istirahat']))->diff(new DateTime($re['end_istirahat'])))->i;
				$istirahat = ($re['start_istirahat'] != '00:00:00' && $re['end_istirahat'] != '00:00:00') ? ($istirahatJam > 0) ? $istirahatJam . ' jam ' . $istirahatMenit . ' menit' : $istirahatMenit . ' menit' : '';

				if (isset($re['end']) && $re['end'] != '00:00:00') {
					$lamaJam = ($istirahatJam > 0) ? (((new DateTime($re['start']))->diff(new DateTime($re['end'])))->h) - $istirahatJam : ((new DateTime($re['start']))->diff(new DateTime($re['end'])))->h;
					
					$lamaMenit = ((new DateTime($re['start']))->diff(new DateTime($re['end'])))->i;
					if ($istirahatMenit > 0) {
						if ($istirahatMenit > $lamaMenit) {
							if ($lamaJam > 0) {
								$lamaJam--;
								$lamaMenit = ($lamaMenit + 60) - $istirahatMenit;
							} else {
								throw new Exception("Time Error");
							}
						} else {
							$lamaMenit = $lamaMenit - $istirahatMenit;
						}
					} else {
						((new DateTime($re['start']))->diff(new DateTime($re['end'])))->i;
					}

					$lama = ($lamaJam > 0) ? $lamaJam . ' jam ' . $lamaMenit . ' menit' : $lamaMenit . ' menit';

					$totalJam = $totalJam + $lamaJam;
					$totalMenit = $totalMenit + $lamaMenit;
				} else {
					$lama = '';
				}

				array_push($val['data'], [
					"date"   			=> (new DateTime($re['date']))->format('d F Y'),
					"start"      		=> substr($re['start'], 0, 5),
					"end"				=> ($re['end'] == '00:00:00') ? '' : substr($re['end'], 0, 5),
					"startIstirahat"	=> substr($re['start_istirahat'], 0, 5),
					"endIstirahat"		=> substr($re['end_istirahat'], 0, 5),
					"ket"				=> ($re['ket']) ? $re['ket'] : '-',
					"lama"				=> $lama,
					"key"				=> $re['created_at'],
					"istirahat"			=> $istirahat
				]);
			}
		}else{
			throw new Exception("Gagal mendapatkan data absensi");
		}

		$sql = "SELECT YEAR(created_at) FROM time GROUP BY YEAR(created_at) ORDER BY YEAR(created_at) ASC";
		$stmt = $conn->prepare($sql);
		if ($stmt->execute()) {
			$stmt->store_result();
			$stmt->bind_result($resYear);
			while ($stmt->fetch()) {
				array_push($year, $resYear);
			}
		}else{
			throw new Exception("Gagal mendapatkan data tahun");
		}
    	$stmt->close();

		$menitToJam = floor($totalMenit / 60);
		$val['totalJam'] = $totalJam = $totalJam + $menitToJam;
		$val['totalMenit'] = $totalMenit = $totalMenit - ($menitToJam * 60);
		$val['tahun'] = $year;
	} catch (\Exception $e) {
		$val['error'] = true;
		$val['msg'] = $e->getMessage();
	}

	($val['error']) ? mysqli_query($conn, 'ROLLBACK') : mysqli_query($conn, 'COMMIT');

	echo json_encode($val);
	mysqli_close($conn);

	fwrite(fopen($log, 'a'), $logText);
	fclose(fopen($log, 'a'));
?>