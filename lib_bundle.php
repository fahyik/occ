<?php
/*********************************************************************************
 * Bundle Class to avoid code repetitions                                        *
 * Should make life a lot easier when accessing information connected to the RPi *
 * No more repetition of exactly the same code                                   *
 *********************************************************************************/
 
 //To Do:
 // - sauberes Überschreiben per URL
 // destruktor mit close(db)
 
class rpiBundleClass
{
	private $ip;
	private $room;
	private $name;
	private $lux;
	private $token;
	//private $db;
	private $dss_ip = "10.200.186.172";
	private $email_adress = "yongf@student.ethz.ch";
	
	function __construct()
	{
		//Connect to database
		//$this->db = mysqli_connect('localhost','root','root','hpzcontrol');
		
		// Get ip of current RPi
		if (empty($_GET['rpi_ip']) && empty($_GET['room']))
		{
			$this->ip = $_SERVER['REMOTE_ADDR'];
		}
		else if (empty($_GET['room']))
		{
			$this->ip = $_GET['rpi_ip'];
		}
		else if(!empty($_GET['room']))
		{
			$this->get_room();
			$db = mysqli_connect('localhost','root','root','hpzcontrol');
			$sql = 'SELECT `ip` FROM structure WHERE room="'.$this->room.'"';
			$result = mysqli_query($db,$sql);
			$obj = mysqli_fetch_object($result);
			$this->ip = $obj->ip;
			mysqli_close($db);
		}
	}
	
	public function get_room()
	{	
		// Get room according to ip
		if (empty($_GET['room']))
		{	
			$db = mysqli_connect('localhost','root','root','hpzcontrol');
			$sql = 'SELECT `room` FROM structure WHERE ip="'.$this->ip.'"';
			$result = mysqli_query($db,$sql);
			$obj = mysqli_fetch_object($result);
			$this->room = $obj->room;
			mysqli_close($db);
		}
		else
		{
			$this->room = $_GET['room'];
		}
		
		return $this->room;
	}	
	
	public function get_name()                                       // get name of rpi according to ip
	{
		if (empty($_GET['rpi_name']))
		{
			$ip = $this->ip;
			$db = mysqli_connect('localhost','root','root','hpzcontrol');
			$sql = 'SELECT `name` FROM structure WHERE ip="'.$ip.'"';
			$result = mysqli_query($db,$sql);
			$obj = mysqli_fetch_object($result);
			$this->name = $obj->name;
			mysqli_close($db);
		}
		else
		{
			$this->name = $_GET['rpi_name'];
		}
		return $this->name;
	}
	
	public function get_token()
	{		                        	                        	 // request session token
		$data = file_get_contents('https://10.200.186.172:8080/json/system/loginApplication?loginToken=17e329deefdc8ed1b0a35416081b1424d18783c69e22a3b2a9805f1d5a8a708d');
		$obj = json_decode($data,true); 	                         // extract session token
		$this->token =  $obj['result']['token'];
		return $this->token;
	}
	
	public function get_lux()
	{
		if (empty($_GET['lux']))
		{
			$room = $this->get_room();                               // search appropiate lux value from table
			$db = mysqli_connect('localhost','root','root','hpzcontrol');
			$sql = 'SELECT `'.$this->room.'` FROM log_lux ORDER BY id DESC LIMIT 1,1';
			$result = mysqli_query($db,$sql);
			$obj = mysqli_fetch_object($result);
			$this->lux = $obj->$room;
			mysqli_close($db);
		}
		else
		{
			$this->lux = $_GET['lux'];
		}
		return $this->lux;
	}
	
	public function get_ip()
	{
		return $this->ip;
	}
	
	//public function get_db()
	//{
	//	return $this->db;
	//}
	
	public function echo_debug()
	{
		echo "rpi_ip: ";   echo $this->get_ip();    echo "<br>";
		echo "rpi_name: "; echo $this->get_name();  echo "<br>";
		echo "rpi_room: "; echo $this->get_room();  echo "<br>";
		echo "rpi_lux: ";  echo $this->get_lux();   echo "<br>";
		echo "token: ";    echo $this->get_token(); echo "<br>";
	}
}
?>