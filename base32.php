<?php
	
	class Base32 {
	
		private static $alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ234567";
		private static $chars = null;
		private static $values = null;
		
		public static function init() {
			Base32::$chars = str_split(Base32::$alphabet);
			Base32::$values = array_fill(0, 256, 0);
			$i = 0;
			foreach(Base32::$chars as $c) {
				Base32::$values[ord($c)] = $i;
				++$i;
			}
		}
	
		public static function encode($data) {
			$str = "";
			for ($i = 0; $i < 7; ++$i) {
				$bits = $data & 0x1f;
				$data = $data >> 5;
				$str = $str . Base32::$chars[$bits];
			}
			
			return $str;		
		}
		
		public static function decode($str) {
			$data = 0;
			$baseChars = str_split($str);
			for ($i = min(6, count($baseChars)); 0 <= $i ; --$i) {
				$bits = Base32::$values[ord($baseChars[$i])];
				$data <<= 5;
				$data |= $bits;
			}
			return $data;
		}
		
		public static function test() {
			print "Base32::encode(121): " . Base32::encode(121) . "<br/>\n";
			print "Base32::decode(ZDAAAAA): " . Base32::decode("ZDAAAAA") . "<br/>\n";
			print "Base32::decode(ZDAAA): " . Base32::decode("ZDAAA") . "<br/>\n";
			print "Base32::decode(ZDAAAAAZZZ): " . Base32::decode("ZDAAAAAZZZ") . "<br/>\n";
		}
		
	}
	
	Base32::init();

?>
