<?php
	
	class Speck32 {
	
		private static $wordmask = 0xFFFF;
		private static $wordsize = 16;
		private static $textwords = 2;
		private static $keywords = 4;
		private static $rounds = 22;
		private static $alpha = 7;
		private static $beta = 2;
		
		private static function ror($a, $n) {
			return ($a >> $n) | (($a << (Speck32::$wordsize - $n)) & Speck32::$wordmask);
		}

		private static function rol($a, $n) {
			return (($a << $n) & Speck32::$wordmask) | ($a >> (Speck32::$wordsize - $n));
		}
		
		private static function add($a, $b) {
			return ($a + $b) & Speck32::$wordmask;
		}

		private static function sub($a, $b) {
			return ($a - $b) & Speck32::$wordmask;
		}
		
		private static function round(&$x, &$y, $k) {
			$x = Speck32::ror($x, Speck32::$alpha);
			$x = Speck32::add($x, $y);
			$x ^= $k;
			$y = Speck32::rol($y, Speck32::$beta);
			$y ^= $x;
		}
		
		private static function revRound(&$x, &$y, $k) {
			$y ^= $x;
			$y = Speck32::ror($y, Speck32::$beta);
			$x ^= $k;
			$x = Speck32::sub($x, $y);
			$x = Speck32::rol($x, Speck32::$alpha);
		}
	
		private static function keySchedule($k) {
			$ks = array();
			$ks[0] = $k[0];
			$l = array();
			$l[0] = $k[1];
			$l[1] = $k[2];
			$l[2] = $k[3];
			for ($i = 0; $i < Speck32::$rounds - 1; ++$i) {
				$a = $l[$i];
				$b = $ks[$i];
				Speck32::round($a, $b, $i);
				$l[$i + Speck32::$keywords - 1] = $a;
				$ks[$i + 1] = $b;
			}
			
			return $ks;			
		}
		
		public static function encrypt($pt, $k) {
			$ks = Speck32::keySchedule($k);
			
			$x = $pt[1];
			$y = $pt[0];
			
			for ($i = 0; $i < Speck32::$rounds; ++$i) {
//				print $i . ": " . dechex($x) . " " . dechex($y) . " " . dechex($ks[$i]) . "<br/>\n";
				Speck32::round($x, $y, $ks[$i]);
			}
			
			$ct = array();
			$ct[1] = $x;
			$ct[0] = $y;
			return $ct;
		}
		
		public static function decrypt($ct, $k) {
			$ks = Speck32::keySchedule($k);
			
			$x = $ct[1];
			$y = $ct[0];
			
			for ($i = Speck32::$rounds - 1; 0 <= $i; --$i) {
				Speck32::revRound($x, $y, $ks[$i]);
			}
			
			$pt = array();
			$pt[1] = $x;
			$pt[0] = $y;
			return $pt;
		}
		
		public static function test() {
			$k = array();
			$k[3] = 0x1918;
			$k[2] = 0x1110;
			$k[1] = 0x0908;
			$k[0] = 0x0100;
			
			$pt = array();
			$pt[1] = 0x6574;
			$pt[0] = 0x694c;
			
			$ct = array();
//		$ct[1] = 0xa868;
//		$ct[0] = 0x42f2;

			print "k:  0x" . dechex($k[3]) . " 0x" . dechex($k[2]) . " 0x" . dechex($k[1]) . " 0x" . dechex($k[0]) . "<br/>\n";
			print "pt: 0x" . dechex($pt[1]) . " 0x" . dechex($pt[0]) . "<br/>\n";

			$ct = array();
			$ct = Speck32::encrypt($pt, $k);
			print "ct: 0x" . dechex($ct[1]) . " 0x" . dechex($ct[0]) . "<br/>\n";
			
			$pt = array();
			$pt = Speck32::decrypt($ct, $k);
			print "pt: 0x" . dechex($pt[1]) . " 0x" . dechex($pt[0]) . "<br/>\n";
		}
		
	}

?>
