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


    public function getEmbeddedObject()
    {

    }
}