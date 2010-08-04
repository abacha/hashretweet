<?

class Rest {

	var $url;
	function __construct($url) {
		$this->url = $url;
	}

	static function at($url) {
		return new Rest($url);
	}

	function get() {
		return file_get_contents($this->url);
	}

	function getJson() {
		return json_decode($this->get());
	}
}

class RestMock extends Rest {
	function __construct() {}
}

?>
