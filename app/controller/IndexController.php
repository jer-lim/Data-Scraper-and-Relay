<?php

class IndexController extends Controller {

	public static $exampleVar = "Example attribute of object called in view";

	public static function showPage(){
		IndexView::make();
	}
}
