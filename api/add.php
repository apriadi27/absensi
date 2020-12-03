<?php
	include 'header.php';

	try {
		if (isset($_POST['start'])) {
			$start = $_POST['start'];
			$ket = $_POST['ket'];
		} else {
			throw new Exception("Data tidak lengkap");
		}
		$date = date('Y-m-d');

		$sql = "INSERT INTO time (date, start, ket) VALUES ('$date', '$start', '$ket')";
		$logText .= $sql." | ".date('H:i:s')."\n";
		if (!$query = mysqli_query($conn, $sql)) throw new Exception("Gagal absen");
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