<?php
namespace bl\cms\cart\frontend\controllers;

use yii\web\Controller;

/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 */
class NovaPoshtaController extends Controller
{
    /**
     * This method is used for Nova Poshta widget.
     *
     * @param $modelName
     * @param $calledMethod
     * @param null $methodProperties
     * @return string
     */
    private function getResponse($modelName, $calledMethod, $methodProperties = null)
    {

        $data = [
            'apiKey' => $this->token,
            'modelName' => $modelName,
            'calledMethod' => $calledMethod,
            'language' => $this->language,
            'methodProperties' => $methodProperties
        ];

        $post = json_encode($data);

        $result = file_get_contents($this->url, null, stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded;\r\n",
                'content' => $post,
            ]
        ]));

        return $result;
    }

    public function actionGetAreasFromNp() {
        return $this->getResponse('Address', 'getAreas');
    }

    public function actionGetCitiesFromNp() {
        return $this->getResponse('AddressGeneral', 'getSettlements');
    }

    public function actionGetWarehousesFromNp($cityName) {

//        $cityName = (!empty($cityName)) ? $cityName : $this->defaultCityName;

        $methodProperties = [
            'CityName' => $cityName
        ];

        return $this->getResponse('AddressGeneral', 'getWarehouses', $methodProperties);
    }
}