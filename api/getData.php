<?php
	include 'header.php';

	try {
		$val['data'] = array();
		$date = date('Y-m-');

		$sql = "SELECT * FROM time WHERE date LIKE '$date%'";
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
				} else {
					$lama = '';
				}

				array_push($val['data'], [
					"date"   			=> (new DateTime($re['date']))->format('d F Y'),
					"start"      		=> $re['start'],
					"end"				=> ($re['end'] == '00:00:00') ? '' : $re['end'],
					"startIstirahat"	=> $re['start_istirahat'],
					"endIstirahat"		=> $re['end_istirahat'],
					"ket"				=> ($re['ket']) ? $re['ket'] : '-',
					"lama"				=> $lama,
					"key"				=> $re['created_at'],
					"istirahat"			=> $istirahat
				]);
			}
		}else{
			throw new Exception("Gagal mendapatkan data absensi");
		}
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