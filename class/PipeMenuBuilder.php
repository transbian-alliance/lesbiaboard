<?php
//PipeMenuBuilder class -- It builds menus.

class PipeMenu {
	private $className = "pipemenu";
	private $entries = array();

	public function setClass($class) {
		$this->className = $class;
	}

	public function getClass() {
		return $this->className;
	}
	
	public function add($entry) {
		$this->entries[] = $entry;
	}

	public function build() {
		$html = "<ul class=\"" . $this->className . "\">";

		foreach ($this->entries as $entry) {
			$html .= $entry->build();
		}

		$html .= "</ul>";
		return $html;
	}
}

interface PipeMenuEntry {
	public function build();
}

class PipeMenuLinkEntry implements PipeMenuEntry {
	private $label;
	private $action;
	private $id;
	private $args;

	public function __construct($label, $action, $id = 0, $args = "") {
		$this->label = $label;
		$this->action = $action;
		$this->id = $id;
		$this->args = $args;
	}

	public function build() {
		return "<li><a href=\"" . actionLink($this->action, $this->id, $this->args) . "\">" . $this->label . "</a></li>";
	}
}

class PipeMenuTextEntry implements PipeMenuEntry {
	private $text;

	public function __construct($text) {
		$this->text = $text;
	}

	public function build() {
		return "<li>" . htmlspecialchars($this->text) . "</li>";
	}
}

class PipeMenuHtmlEntry implements PipeMenuEntry {
	private $html;

	public function __construct($html) {
		$this->html = $html;
	}

	public function build() {
		return "<li>" . $this->html . "</li>";
	}
}
