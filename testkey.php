<?php
	
	class TestKey {
	
		private static $salt = null;
		private static $speck32Key = null;
		
		public static function init() {

			$ini = "./config/testkey_config.ini";
			$parse = parse_ini_file($ini, true);

			$salt = hexdec($parse["salt"]);
			$key0 = hexdec($parse["key_0"]);
			$key1 = hexdec($parse["key_1"]);
			$key2 = hexdec($parse["key_2"]);
			$key3 = hexdec($parse["key_3"]);
			
			TestKey::$salt = $salt;
			TestKey::$speck32Key = array($key0, $key1, $key2, $key3);
			
		}
	
		public static function key($id) {
			return IdCrypt::encrypt(TestKey::$salt, TestKey::$speck32Key, $id);
		}
		
		public static function id($key) {
			return IdCrypt::decrypt(TestKey::$salt, TestKey::$speck32Key, $key);
		}
		
		public static function test() {
			print "TestKey::salt: 0x". dechex(TestKey::$salt) . "<br/>\n";
			print "TestKey::key[3]: 0x" . dechex(TestKey::$speck32Key[3]) . "<br/>\n";
			print "TestKey::key[2]: 0x" . dechex(TestKey::$speck32Key[2]) . "<br/>\n";
			print "TestKey::key[1]: 0x" . dechex(TestKey::$speck32Key[1]) . "<br/>\n";
			print "TestKey::key[0]: 0x" . dechex(TestKey::$speck32Key[0]) . "<br/>\n";
			print "TestKey::key(121): " . TestKey::key(121) . "<br/>\n";
			print "TestKey::id(MLEFZIA): " . TestKey::id("MLEFZIA") . "<br/>\n";
			print "TestKey::key(-1): " . TestKey::key(-1) . "<br/>\n";
			print "TestKey::id(VFBOLRC): " . TestKey::id("VFBOLRC") . "<br/>\n";
		}
		
	}
	
	TestKey::init();
	
?>
