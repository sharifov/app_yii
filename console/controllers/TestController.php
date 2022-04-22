<?php

namespace console\controllers;
use console\base\Controller;
use ms\zp1c\services\actions\ActionsOrderInsert;
use ms\zp1c\services\actions\types\Position;


/**
 * Class TestController
 */
class TestController extends Controller
{
    public function actionOneC()
    {
        $request = new ActionsOrderInsert();
        $request->ActionId = 'polisorb';
        $request->OrderID = 'polisorb-99999';
        $request->IsProcessed = true;
        $request->Items = [];
        $request->Items[] = new Position([
            'ID' => '00000003594',
            'Quantity' => 1,
            'Nominal' => 500,
            'Price' => 0,
        ]);
        $request->Items[] = new Position([
            'ID' => '00000003594',
            'Quantity' => 2,
            'Nominal' => 200,
            'Price' => 0,
        ]);

        try {
            $response = $request->send();
        } catch (\SoapFault $e) {
            echo($request->getLastRequest());
            echo "\n\n";
            echo($request->getLastResponse());
            echo "\n\n";
            throw $e;
        }

        echo($request->getLastRequest());
        echo "\n\n";
        echo($request->getLastResponse());
        echo "\n\n";

        var_dump($response);
    }
}