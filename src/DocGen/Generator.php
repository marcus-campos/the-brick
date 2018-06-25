<?php

namespace Sympla\Search\DocGen;

use Sympla\Search\DocGen\DocParser;

class Generator
{

    private $allRoutes;
    private $docArray;

    public function __construct()
    {
    }

    public function run()
    {
        $this->handle();
    }

    private function handle()
    {
        $this->setRoutes();
        $this->generate();
        $this->createJsonFile();
    }

    private function createJsonFile()
    {
        $storage = app('Storage');
        $storage::disk('local')
            ->put('the-brick/doc.json', json_encode($this->docArray, JSON_PRETTY_PRINT));
    }

    /**
     * Doc generate
     * @return void
     */
    private function generate()
    {
        foreach ($this->allRoutes as $route) {
            $docArray = [];
            $docParser = null;
            $filterDocParser = null;

            if ($route->getAction()['uses'] != '' && is_string($route->getAction()['uses'])) {
                $uses = explode('@', $route->getAction()['uses']);
                $class = $uses[0];

                if (class_exists($class)) {
                    $class = $this->getReflectionClass($class);
                    if ($class->hasMethod($uses[1])) {
                        $docParser = new DocParser($class->getMethod($uses[1])->getDocComment());
                        $parsedDoc = $docParser->get();
                    }
                }

                if (!empty($docParser) && $docParser->getTagValue('negotiate')) {

                    $class = $docParser->getTagValue('negotiate');
                    $class = $this->getReflectionClass(
                        (config('the-brick-search.models.namespace_prefix') ?? 'App\\').$class
                    );

                    $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
                    $filters = [];

                    foreach ($methods as $method) {
                        if (strpos($method->name, 'scope') !== false) {
                            $filterDocParser = new DocParser($method->getDocComment());

                            $filters[] =  [
                                'name' => lcfirst(str_replace('scope', '', $method->name)),
                                'summary' => $filterDocParser->getShortDesc(),
                                'description' => $filterDocParser->getLongDesc(),
                                'params' => $filterDocParser->getTagsIfCointansString('param'),
                                'return' => $filterDocParser->getTagValue('return')
                            ];
                        }
                    }

                    $this->docArray[] = [
                        'route' => $route->getPath(),
                        'methods' => $route->getMethods(),
                        'uses' => $route->getAction()['uses'],
                        'summary' => $docParser->getShortDesc(),
                        'description' => $docParser->getLongDesc(),
                        'params' => $docParser->getTagsIfCointansString('param'),
                        'filters' => $filters
                    ];
                }
            }
        }
    }

    private function setRoutes()
    {
        $route = app('route');
        $this->allRoutes = $route::getRoutes();
        return $this;
    }

    private function getReflectionClass($class)
    {
        return new \ReflectionClass($class);
    }
}
