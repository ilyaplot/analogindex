<?php

class TypeRule extends CBaseUrlRule
{

    public $urlSuffix;

    public function createUrl($manager, $route, $params, $ampersand)
    {
        // Если не наш url, идем лесом
        if ($route !== 'site/type')
            return false;
        // Сразу обозначаем параметр языка
        $params['language'] = !empty($params['language']) ? $params['language'] : Language::getCurrentZone();
        // Если тип не задан, идем лесом
        if (empty($params['type']))
            return false;
        // Строим базовый url
        $url = "//analogindex.{$params['language']}/type/{$params['type']}";
        // Удаляем параметры, которые уже были применены
        unset($params['language'], $params['type']);

        // Сортируем параметры по ключу для исключения умножения ссылок
        ksort($params);

        // Перебираем переданные параметры и добавляем к url
        foreach ($params as $key => $value)
            $url.="/{$key}/{$value}";

        return $url . $manager->urlSuffix;
    }

    public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
    {
        // url подходит под правило type
        if (preg_match("~type/(?P<type>[\d\w\-_]+)/(?P<url>[/\-\.\w\d]+)~", $pathInfo, $matches)) {
            $_GET['type'] = $matches['type'];

            $url = explode("/", $matches['url']);
            foreach ($this->getAssocParams($url) as $key => $value) {
                $_GET[$key] = $value;
            }
            return "site/type";
        }

        // Простой url без дополнительных параметров
        if (preg_match("~type/(?P<type>[\d\w\-_]+)~", $pathInfo, $matches)) {
            $_GET['type'] = $matches['type'];
            return "site/type";
        }

        return false;  // не применяем данное правило
    }

    /**
     * Получаем ассоциативный массив параметров
     * @param type $params
     */
    protected function getAssocParams($params)
    {
        $assoc = array();
        for ($i = 0; $i < count($params); $i+=2) {
            if (!isset($params[$i + 1]))
                continue;
            $assoc[$params[$i]] = $params[$i + 1];
        }
        return $assoc;
    }

}
