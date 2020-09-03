<?php
function currency_format($dValue, $decimal_places, $decimal_separator, $grouping_separator)
{
	return number_format($dValue, $decimal_places, $decimal_separator, $grouping_separator);
}
function double_format($dValue)
{
	return number_format($dValue, 2, ',', '.');
}
function integer_format($dValue)
{
	return number_format($dValue, 0, ',', '.');
}
function gen_uuid() {
	 $uuid = array(
	  'time_low'  => 0,
	  'time_mid'  => 0,
	  'time_hi'  => 0,
	  'clock_seq_hi' => 0,
	  'clock_seq_low' => 0,
	  'node'   => array()
	 );

	 $uuid['time_low'] = mt_rand(0, 0xffff) + (mt_rand(0, 0xffff) << 16);
	 $uuid['time_mid'] = mt_rand(0, 0xffff);
	 $uuid['time_hi'] = (4 << 12) | (mt_rand(0, 0x1000));
	 $uuid['clock_seq_hi'] = (1 << 7) | (mt_rand(0, 128));
	 $uuid['clock_seq_low'] = mt_rand(0, 255);

	 for ($i = 0; $i < 6; $i++) {
	  $uuid['node'][$i] = mt_rand(0, 255);
	 }

	 $uuid = sprintf('%08x-%04x-%04x-%02x%02x-%02x%02x%02x%02x%02x%02x',
	  $uuid['time_low'],
	  $uuid['time_mid'],
	  $uuid['time_hi'],
	  $uuid['clock_seq_hi'],
	  $uuid['clock_seq_low'],
	  $uuid['node'][0],
	  $uuid['node'][1],
	  $uuid['node'][2],
	  $uuid['node'][3],
	  $uuid['node'][4],
	  $uuid['node'][5]
	 );

	 return $uuid;
}
function validurl($str) {
	  $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);             
	  $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);             
	  $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);             
	  $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);             
	  $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);             
	  $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);             
	  $str = preg_replace("/(đ)/", 'd', $str);             
	  $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);             
	  $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);             
	  $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);             
	  $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);             
	  $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);             
	  $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);             
	  $str = preg_replace("/(Đ)/", 'D', $str);        
	  $str = str_replace("/", "-", str_replace("&*#39;","",$str));		  
	  $str = str_replace(" ", "-", str_replace("&*#39;","",$str)); 
	  $str = str_replace("%", "", $str); 
	  $str = str_replace("(", "", $str); 
	  $str = str_replace(")", "", $str); 
			  
	  $str = strtolower($str);

	return $str;
}
function __($k) 
{
	global $langs;
	foreach($langs as $key => $item)
	{
		if($k == $key)
		{
			return $item;
		}				
	}
	return $k;
}
function findReceiptNo($db, $id) 
{
	$sql = "SELECT receipt_number FROM res_receipt_no WHERE id='".$id."'";
	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);	
	$i = 1;
	if($numrows>0)
	{
		$row = pg_fetch_array($result, 0);
		$s = $row["receipt_number"];
		$i = (int) $s;
		$i = $i + 1;
		$sql = "UPDATE res_receipt_no SET receipt_number=" .$i.", receipt_date=NOW(), write_date=NOW() WHERE id='".$id."'";
		$rs = pg_exec($db, $sql);
	}else
	{
		$sql = "INSERT INTO res_receipt_no(receipt_number, id, receipt_date) VALUES(1,'".$id."',NOW())";
		$rs = pg_exec($db, $sql);
	}
	return $i;
		
}
function findSaleAmount($db, $id)
{
	
	$sql = "SELECT SUM(d1.quantity * d1.unit_price) AS amount FROM sale_product_local d1 WHERE d1.status =0 AND d1.sale_id='".$id."'";
	
	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);
	$amount = 0;
	if($numrows>0)
	{
		$row = pg_fetch_array($result, 0);
		$amount = $row["amount"];
	}
	
	return $amount;
}
const METHOD = 'aes-256-ctr';

/**
 * Encrypts (but does not authenticate) a message
 * 
 * @param string $message - plaintext message
 * @param string $key - encryption key (raw binary expected)
 * @param boolean $encode - set to TRUE to return a base64-encoded 
 * @return string (raw binary)
 */
function encrypt($message, $encode = false)
{
	$nonceSize = openssl_cipher_iv_length(METHOD);
	$nonce = openssl_random_pseudo_bytes($nonceSize);
	$key = hex2bin('000102030405060708090a0b0c0d0e0f101112131415161718191a1b1c1d1e1f');

	$ciphertext = openssl_encrypt(
		$message,
		METHOD,
		$key,
		OPENSSL_RAW_DATA,
		$nonce
	);

	// Now let's pack the IV and the ciphertext together
	// Naively, we can just concatenate
	if ($encode) {
		return base64_encode($nonce.$ciphertext);
	}
	return $nonce.$ciphertext;
}

/**
 * Decrypts (but does not verify) a message
 * 
 * @param string $message - ciphertext message
 * @param string $key - encryption key (raw binary expected)
 * @param boolean $encoded - are we expecting an encoded string?
 * @return string
 */
function decrypt($message, $encoded = false)
{
	
	if ($encoded) {
		$message = base64_decode($message, true);
		if ($message === false) {
			return "";
		}
	}
	$key = hex2bin('000102030405060708090a0b0c0d0e0f101112131415161718191a1b1c1d1e1f');
	$nonceSize = openssl_cipher_iv_length(METHOD);
	$nonce = mb_substr($message, 0, $nonceSize, '8bit');
	$ciphertext = mb_substr($message, $nonceSize, null, '8bit');

	$plaintext = openssl_decrypt(
		$ciphertext,
		METHOD,
		$key,
		OPENSSL_RAW_DATA,
		$nonce
	);

	return $plaintext;
}
function paging($sql, $p, $ps, $sort)
{
	$arr = [];
	$index = strpos($sql, "FROM", 5); 
	if($index == true)
	{
		$arr[1] = "SELECT COUNT(*) ".substr($sql, $index);
		
	}
	if($sort != "")
	{
		$sql = $sql." ORDER BY ".$sort;
	}
	$p = $p * $ps;
	$sql = $sql." OFFSET ".$p." LIMIT ".$ps;
	$arr[0] = $sql;
	
	return $arr;
}
function format_date($date)
{
	if($date != "")
	{
		$firstIndex = stripos($date, " ");
		if($firstIndex != -1)
		{
			$date = substr($date, 0, $firstIndex);
			$arr = explode("-", $date);
			if(count($arr)>2)
			{
				$date = $arr[1]."/". + $arr[2]."/". + $arr[0];
			}
		}
	}
	return $date;
}
function format_datetime($date)
{
	$d = format_date($date);
	$firstIndex = stripos($date, " ");
	if($firstIndex != -1)
	{
		$date = substr($d, 0, $firstIndex );
	}else{
		$date = $d;
	}
	return $date;
}
function format_dateminute($date)
{
	$d = format_date($date);
	$firstIndex = stripos($date, " ");
	if($firstIndex != -1)
	{
		$s = substr($date, $firstIndex  + 1);
		$arr = explode(":", $s);
		if(count($arr)>2)
		{
			$date = $d." ".$arr[0].":".$arr[1];
		}else
		{
		}
		
	}else{
		$date = $d;
	}
	return $date;
}
function respTable($db, $sql) 
{
	$result = pg_exec($db, $sql);
	$numrows = pg_numrows($result);
	$numfields = pg_num_fields($result);
	$s = "";
	for($i =0; $i <$numfields; $i++)
	{
		if($i>0)
		{
			$s = $s."\t";
		}
		echo pg_field_name ( $result , $i );
	}
	for($j=0; $j<$numrows; $j++)
	{
		$s = $s."\n";
		$row = pg_fetch_array($result, $j);
		
		for($i =0; $i <$numfields; $i++)
		{
			if($i>0)
			{
				$s = $s."\t";
			}
			$s = $s."".$row[$i];
		}
	}
	return $s;
		
}
function selectDistinct($values, $indexes)
{
	$arr = array();
	$len = count($indexes);
	$m = 0;
	for($i = 0; $i<count($values); $i++)
	{
		$exist = false;
		for($n =0; $n<count($arr); $n++)
		{
			for($j =0; $j<$len; $j++)
			{
				if($arr[$n][$j] == $values[$i][$indexes[$j]])
				{
					$exist = true;					
				}else
				{
					$exist = false;	
					break;
				}
			}
			if($exist == true)
            {
                break;
            }
		}
		if($exist == false)
        {
			$item = array();
            for($j=0; $j<$len; $j++)
            {
                $item[$j]= $values[$i][$indexes[$j]];
                
            }
			
            $arr[$m] = $item;
			$m = $m + 1;
        }
	}
	return $arr;
	
}
function rand_color() 
{
    return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
}
class ws
{
	private $params;
	private $head;
	private $instance;
		
	public function __construct($params)
	{
		foreach($params as $key => $value)
			$this->params[$key] = $value;

		$local = "http://".$this->params['host'];
		if(isset($_SERVER['REMOTE_ADDR']))
			$local = "http://".$_SERVER['REMOTE_ADDR'];

		$this->head =	"GET / HTTP/1.1\r\n" .
						"Upgrade: websocket\r\n" .
						"Connection: Upgrade\r\n" .
						"Host: ".$this->params['host']."\r\n" .
						"Origin: ".$local."\r\n" .
						"Sec-WebSocket-Key: TyPfhFqWTjuw8eDAxdY8xg==\r\n" .
						"Sec-WebSocket-Version: 13\r\n";		
	}

	public function send($method)
	{
		$this->head .= "Content-Length: ".strlen($method)."\r\n\r\n";
			$this->connect();		
			fwrite($this->instance, $this->hybi10Encode($method));
		
				
	}

	public function close()
	{
		if($this->instance)
		{
			fclose($this->instance);
			$this->instance = NULL;
		}
	}
	
	private function connect()
	{
		$sock = fsockopen($this->params['host'], $this->params['port'], $errno, $errstr, 2);
		
		if ($sock) {
			fwrite($sock, $this->head);
			$headers = fread($sock, 2000);

			$this->instance = $sock;
			
		}
				
	}
	
	private function hybi10Decode($data)
	{
		$bytes = $data;
		$dataLength = '';
		$mask = '';
		$coded_data = '';
		$decodedData = '';
		$secondByte = sprintf('%08b', ord($bytes[1]));
		$masked = ($secondByte[0]=='1') ? true : false;
		$dataLength = ($masked===true) ? ord($bytes[1]) & 127 : ord($bytes[1]);

		if ($masked===true)
		{
			if ($dataLength===126)
			{
				$mask = substr($bytes, 4, 4);
				$coded_data = substr($bytes, 8);
			}
			elseif ($dataLength===127)
			{
				$mask = substr($bytes, 10, 4);
				$coded_data = substr($bytes, 14);
			}
			else
			{
				$mask = substr($bytes, 2, 4);
				$coded_data = substr($bytes, 6);
			}
			for ($i = 0; $i<strlen($coded_data); $i++)
				$decodedData .= $coded_data[$i] ^ $mask[$i % 4];
		}
		else
		{
			if ($dataLength===126)
				$decodedData = substr($bytes, 4);
			elseif ($dataLength===127)
				$decodedData = substr($bytes, 10);
			else
				$decodedData = substr($bytes, 2);
		}

		return $decodedData;
	}

	private function hybi10Encode($payload, $type = 'text', $masked = true)
	{
		$frameHead = array();
		$frame = '';
		$payloadLength = strlen($payload);

		switch ($type)
		{
			case 'text' :
				// first byte indicates FIN, Text-Frame (10000001):
				$frameHead[0] = 129;
				break;

			case 'close' :
				// first byte indicates FIN, Close Frame(10001000):
				$frameHead[0] = 136;
				break;

			case 'ping' :
				// first byte indicates FIN, Ping frame (10001001):
				$frameHead[0] = 137;
				break;

			case 'pong' :
				// first byte indicates FIN, Pong frame (10001010):
				$frameHead[0] = 138;
				break;
		}

		// set mask and payload length (using 1, 3 or 9 bytes)
		if ($payloadLength>65535)
		{
			$payloadLengthBin = str_split(sprintf('%064b', $payloadLength), 8);
			$frameHead[1] = ($masked===true) ? 255 : 127;
			for ($i = 0; $i<8; $i++)
				$frameHead[$i + 2] = bindec($payloadLengthBin[$i]);

			// most significant bit MUST be 0 (close connection if frame too big)
			if ($frameHead[2]>127)
			{
				$this->close(1004);
				return false;
			}
		}
		elseif ($payloadLength>125)
		{
			$payloadLengthBin = str_split(sprintf('%016b', $payloadLength), 8);
			$frameHead[1] = ($masked===true) ? 254 : 126;
			$frameHead[2] = bindec($payloadLengthBin[0]);
			$frameHead[3] = bindec($payloadLengthBin[1]);
		}
		else
			$frameHead[1] = ($masked===true) ? $payloadLength + 128 : $payloadLength;

		// convert frame-head to string:
		foreach (array_keys($frameHead) as $i)
			$frameHead[$i] = chr($frameHead[$i]);

		if ($masked===true)
		{
			// generate a random mask:
			$mask = array();
			for ($i = 0; $i<4; $i++)
				$mask[$i] = chr(rand(0, 255));

			$frameHead = array_merge($frameHead, $mask);
		}
		$frame = implode('', $frameHead);
		// append payload to frame:
		for ($i = 0; $i<$payloadLength; $i++)
			$frame .= ($masked===true) ? $payload[$i] ^ $mask[$i % 4] : $payload[$i];

		return $frame;
	}
}
function sendWS($host, $port, $message)
{

	$ws = new ws(array
	(
		'host' => $host,
		'port' => $port,
		'path' => ''
	));
	$result = $ws->send($message);
	$ws->close();

	return $result;
	
}
function base64Decode($s)
{
	return base64_decode($s);
}
function getMime($ex)
{
	$ex = strtolower($ex);
	
	if($ex == "bmp") return "image/bmp";
	if($ex == "fif") return "image/fif";
	if($ex == "gif") return "image/gif";
	if($ex == "jpe") return "image/jpeg";
	if($ex == "jpeg") return "image/jpeg";
	if($ex == "jpg") return "image/jpeg";
	if($ex == "png") return "image/png";
	if($ex == "css") return "text/css";
	if($ex == "js") return "text/javascript";
	if($ex == "htm") return "text/html";
	if($ex == "html") return "text/html";
	if($ex == "ico") return "image/x-icon";
	if($ex == "svg") return "image/svg+xml";
	if($ex == "ttf") return "application/x-font-ttf";
	if($ex == "otf") return "application/x-font-opentype";
	if($ex == "woff") return "application/font-woff";
	if($ex == "woff2") return "application/font-woff2";
	if($ex == "eot") return "application/vnd.ms-fontobject";
	if($ex == "xls") return "application/vnd.ms-excel";
	if($ex == "xlsx") return "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
	return "application/octet-stream";
	return "";
}
