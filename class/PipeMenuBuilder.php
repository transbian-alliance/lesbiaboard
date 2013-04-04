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

	public function addStart($entry) {
		array_unshift($this->entries, $entry);
	}

	public function pop() {
		return array_pop($this->entries);
	}

	public function shift() {
		return array_shift($this->entries);
	}

	public function build() {
		if(count($this->entries) == 0)
			return "";

		$html = "<ul class=\"" . $this->className . "\">";

		foreach ($this->entries as $entry) {
			$html .= "<li>".$entry->build()."</li>";
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
		return "<a href=\"" . htmlspecialchars($this->getLink()) . "\">" . $this->label . "</a>";
	}
	public function getLink() {
		return actionLink($this->action, $this->id, $this->args);
	}
}

class PipeMenuTextEntry implements PipeMenuEntry {
	private $text;

	public function __construct($text) {
		$this->text = $text;
	}

	public function build() {
		return htmlspecialchars($this->text);
	}
	public function getLink() {
		return "";
	}
}

class PipeMenuHtmlEntry implements PipeMenuEntry {
	private $html;

	public function __construct($html) {
		$this->html = $html;
	}

	public function build() {
		return $this->html;
	}
	public function getLink() {
		preg_match('/href="([^"]*)"/', $this->html, $match);
		return html_entity_decode($match[1]);
	}
}
