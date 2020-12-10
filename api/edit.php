<?php
	include 'header.php';

	try {
		$start = mysqli_real_escape_string($conn, $_POST['start']);
		$end = mysqli_real_escape_string($conn, $_POST['end']);
		$ket = mysqli_real_escape_string($conn, $_POST['ket']);
		$istirahatStart = mysqli_real_escape_string($conn, $_POST['istirahatStart']);
		$istirahatEnd = mysqli_real_escape_string($conn, $_POST['istirahatEnd']);
		$key = mysqli_real_escape_string($conn, $_POST['key']);
		
		$sql = "UPDATE time SET start = '$start', end = '$end', start_istirahat = '$istirahatStart', end_istirahat = '$istirahatEnd', ket = '$ket' WHERE created_at = '$key'";
		$logText .= $sql." | ".date('H:i:s')."\n";
		if (!$query = mysqli_query($conn, $sql)) throw new Exception("Gagal mengubah absen");
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