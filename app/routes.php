<?php

Route::get("/", "IndexController::showPage");
Route::post("/rpc/{name}", "RpcController::handleRequest");
