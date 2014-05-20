<?php
class ConfoneHomeController extends ViewController {

	protected function control() {
		$this->render( array(
			'view' => 'page/home.php'
		));
	}
}
?>