<?php
	
	class IdCrypt {
	
		private static $mask = 0xffffffff;
	
		public static function encrypt($salt, $key, $id) {
			$saltId = ($id + $salt) & IdCrypt::$mask;
			$words = array( ($saltId & 0xffff), ($saltId >> 16) );
			$wordsEnc = Speck32::encrypt($words, $key);
			$encId = ($wordsEnc[1] << 16) | $wordsEnc[0];
			$encBase32 = Base32::encode($encId);
			return $encBase32;
		}
		
		public static function decrypt($salt, $key, $encBase32) {
			$encId = Base32::decode($encBase32);
			$wordsEnc = array ( ($encId & 0xffff), ($encId >> 16) );
			$words = Speck32::decrypt($wordsEnc, $key);
			$saltId = ($words[1] << 16) | $words[0];
			$id = ($saltId - $salt) & IdCrypt::$mask;
			return $id;
		}
		
		public static function test() {
			$salt = 0x11223344;
			$key = array(0x0100, 0x0908, 0x1110, 0x1918);
		
			print "IdCrypt::encrypt(121): " . IdCrypt::encrypt($salt, $key, 121) . "<br/>\n";
			print "IdCrypt::decrypt(MLEFZIA): " . IdCrypt::decrypt($salt, $key, "MLEFZIA") . "<br/>\n";
		}
		
	}
	
?>
