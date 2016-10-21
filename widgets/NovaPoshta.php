<?php
namespace bl\cms\cart\widgets;
use bl\cms\cart\widgets\assets\NovaPoshtaAsset;
use yii\base\Exception;
use yii\base\Widget;


/**
 * @author Albert Gainutdinov <xalbert.einsteinx@gmail.com>
 *
 * Requirements:
 */
class NovaPoshta extends Widget
{
    public $token;
    public $url = 'https://api.novaposhta.ua/v2.0/json/';


    public $language = 'ru';
    public $defaultCityName = 'Киев';

    public $formModel;
    public $formAttribute;

    public function init()
    {
        NovaPoshtaAsset::register($this->getView());
    }

    public function run($params = null)
    {

        $areas = json_decode($this->getAreas());
        $cities = json_decode($this->getCities());
        $warehouses = json_decode($this->getWarehouses());

        return $this->render('nova-poshta', [
            'language' => $this->language,
            'model' => $this->formModel,
            'attribute' => $this->formAttribute,
            'areas' => $areas->data,
            'cities' => $cities->data,
            'warehouses' => $warehouses->data,
        ]);
    }

    public function getWarehouses($cityName = null) {

        $cityName = (!empty($cityName)) ? $cityName : $this->defaultCityName;

        $methodProperties = [
            'CityName' => $cityName
        ];

        return $this->getResponse('AddressGeneral', 'getWarehouses', $methodProperties);
    }

    public function getAreas() {

        return $this->getResponse('Address', 'getAreas');
    }
    public function getCities() {

        return $this->getResponse('AddressGeneral', 'getSettlements');
    }

    private function getResponse($modelName, $calledMethod, $methodProperties = null) {

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
}