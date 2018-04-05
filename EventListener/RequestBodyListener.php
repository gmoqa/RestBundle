<?php

namespace MNC\Bundle\RestBundle\EventListener;

use MNC\Bundle\RestBundle\Exception\BodyFormatException;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class RequestBodyListener
 * @package MNC\Bundle\RestBundle\EventListener
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class RequestBodyListener
{
    /**
     * @param GetResponseEvent $event
     * @throws BodyFormatException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $content = $request->getContent();
        if ($request->isMethod('GET') OR !$content OR $request->headers->get('Content-Type') !== 'application/json') {
            return;
        }

        $data = json_decode($content, true);

        if (!$data) {
            $error = json_last_error_msg();
            throw BodyFormatException::malformedBody($error);
        }

        foreach ($data as $key => $value) {
            $request->request->set($key, $value);
        }
    }
}