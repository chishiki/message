<?php

final class MessagePDF {

	private $doc;
	private $fileObject;
	private $fileObjectID;

	public function __construct($loc, $input) {

		$doc = 'EXAMPLE PDF';
		$fileObject = 'Message';
		$fileObjectID = 0;

		$this->doc = $doc;
		$this->fileObject = $fileObject;
		$this->fileObjectID = $fileObjectID;

	}

	public function doc() {

		return $this->doc;

	}

	public function getFileObject() {

		return $this->fileObject;

	}

	public function getFileObjectID() {

		return $this->fileObjectID;

	}

}

?>