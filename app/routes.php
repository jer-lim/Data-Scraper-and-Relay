<?php

Route::get("/", "IndexController::showPage");
Route::get("/rpc/{name}", "RpcController::handleRequest");
