<?php
namespace Abr4xas\Plans\Traits;

trait ResolveClass
{

    /**
     * Undocumented function
     *
     * @param string $name
     * @todo hacer que funcione con psalm #YOLO
     */
    public function resolveClass(string $name)
    {
        $service = config($name);

        if ($service) {
            return resolve($service);
        }

        throw new \Exception('This class does not exist...');
    }
}
