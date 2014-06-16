<?php

namespace Ecentria\Libraries\CoreRestBundle\Controller;

class Hateoas
{
    /**
     * Build an array of links in a standardized format
     *
     * @param array $links The links to be formatted
     * @return array
     */
    public function buildLinksArray(array $links)
    {
        $linkArray = [];

        foreach($links AS $name => $url)
        {
            $link = [$name => ['href' => $url]];

            array_push($linkArray, $link);
        }

        return $linkArray;
    }

    /**
     * Retrieve requested objects and return them in an array
     *
     * @param string    $baseNamespace  The object making the request
     * @param string    $from           The object making the request
     * @param int       $fromId         ID of the object making the request
     * @param string    $get            The object being requested
     * @param null|int  $getId          The ID of the object being requested (optional)
     * @return object
     */
    public function getEmbeddedObject($baseNamespace, $from, $fromId, $get, $getId = null)
    {
        $use = $baseNamespace . 'Controllers\\' .  $from . 'Controller';

        if (!class_exists($use))
        {
            throw new \Exception('Requested embedded object does not exist: ' . $use);
        }

        $controller = new $use;

        return $controller->get$get($getId);
    }
}