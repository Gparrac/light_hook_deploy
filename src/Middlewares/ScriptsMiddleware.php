<?php

namespace Lhd\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Lhd\Utils\ResponseHttp;
use Psr\Log\LoggerInterface;

class KeyMiddleware
{
    // this attirube must be import from other file with all keys information🏗️
    private $projects =  include '../../Deployments/deployment_projects.php' ;
    private $logger;

    public function __invoke(Request $request, RequestHandlerInterface $handler): Response
    {
            $params = $request->getParsedBody();
            $uri = $request->getUri();
            $project = $this->projects[$params['project']];
            $lifeCycles = [];
            // Validate scripts location
            if($uri == '/deploy'){
                $lifeCycles = ['predeploy', 'deploy'];
            }elseif ($uri == '/rollback'){
                $lifeCycles = ['rollback'];
            }
            foreach ($lifeCycles as  $value) {                
                foreach ($project['directory']['lyfecycle'][$value] as $stage) {
                    if(!file_exists($project['directory'] . '/' . $stage)){
                        return ResponseHttp::generateJson((object)["message" => "Inconsistence in files from  " . $stage . " status."], 500);
                    }
                }
            }
            $response = $handler->handle($request);

        return $response;
    }
}