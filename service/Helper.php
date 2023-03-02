<?php 
class Helper {
	static function formatVNDate($date) {
		$vnFormatDate = date("d/m/Y", strtotime($date));
		return $vnFormatDate;
	}
}

 ?>