<?php
  

return array(
    'router' => array(
        'routes' => array(
             'getAll' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/v1/:adapterName/workflow/:workflowid',
                    'defaults' => array(
                        'controller' => 'WorkflowApi\Controller\Workflow',
                        'action'=> 'geAllbyWorkflowId',
                    )
                    
                ),
            ),
             'getAllWorkflows' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/v1/:adapterName/workflow',
                    'defaults' => array(
                        'controller' => 'WorkflowApi\Controller\Workflow',
                        'action'=> 'geAll',
                    )
                    
                ),
            ),
            'addNode' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/v1/:adapterName/node/add',
                    'defaults' => array(
                        'controller' => 'WorkflowApi\Controller\Workflow',
                        'action'=> 'addNode',
                    )
                    
                ),
            ),
             'deleteNode' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/v1/:adapterName/node/delete/:nodoId',
                    'defaults' => array(
                        'controller' => 'WorkflowApi\Controller\Workflow',
                        'action'=> 'deleteNode',
                    )
                    
                ),
            ),
             'deleteTransicion' => array(
                'type' => 'segment',
                'options' => array(
                    'route'    => '/v1/:adapterName/transition/delete/:transitionId',
                    'defaults' => array(
                        'controller' => 'WorkflowApi\Controller\Workflow',
                        'action'=> 'deleteTransition',
                    )
                    
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'WorkflowApi\Controller\Workflow' => 'WorkflowApi\Controller\WorkflowController',
        ),
    ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);
