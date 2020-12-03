<?php
	include 'header.php';

	try {
		if (isset($_POST['key'])) {
			$key = $_POST['key'];
		} else {
			throw new Exception("Data tidak lengkap");
		}

		$sql = "DELETE FROM time WHERE created_at = '$key'";
		$logText .= $sql." | ".date('H:i:s')."\n";
		if (!$query = mysqli_query($conn, $sql)) throw new Exception("Gagal menghapus absen");
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