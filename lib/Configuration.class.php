<?

class Configuration {

	public $USERNAME;
	public $PASSWORD;
	public $USERID;
	public $WHITELIST;
	
	function __construct($config = null) {
		$this->USERNAME = $config['username'];
		$this->PASSWORD = $config['password'];
		$this->USERID = $config['userid'];
		$this->WHITELIST = $config['whitelist'];
	}		
}	
?>
