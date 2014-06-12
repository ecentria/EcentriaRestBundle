<?php

namespace Ecentria\Libraries\CoreRestBundle\Controller;

class Hateoas
{
    public function buildLinksArray($links)
    {
        $linkArray = [];

        foreach($links AS $name => $url)
        {
            $link = [$name => ['href' => $url]];

            array_push($linkArray, $link);
        }

        return $linkArray;
    }

    public function buildEmbeddedObjects($requestedObjects)
    {
        
    }
}