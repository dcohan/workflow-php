<?php
namespace WorkflowApi\Controller;

use Zend;
use Zend\Http\Request;
use WorkflowApi\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\Http\Client;
use Zend\Stdlib\Parameters;
use WorkflowApi;

class WorkflowController extends AbstractRestfulJsonController {

    private $workflowId;
    private $adapter;

    /**
    * @SWG\Get(
    *     path="/workflow/services/v1/{adapterName}/workflow/{workflowid}",
    *     summary="Fetch all Workflow rules",
    *     operationId="workflow/get",
    *     consumes={"application/json"},
    *     produces={"application/json"},
    *     tags={"workflow"},
    *     @SWG\Parameter(
    *         description="ID of the workflow",
    *         format="int64",
    *         default=1,
    *         in="path",
    *         name="workflowid",
    *         required=true,
    *         type="integer"
    *     ),
    *     @SWG\Parameter(
    *         description="adapterName",
    *         format="string",
    *         default="workflow_adapter_atvadapter",
    *         in="path",
    *         name="adapterName",
    *         required=false,
    *         type="string"
    *     ), 
    *     @SWG\Response(
    *         response=200,
    *         description="successful operation",
    *     ),
    *     @SWG\Response(
    *         response="500",
    *         description="Error processing the response",
    *     )
    * )
    */
    public function geAllbyWorkflowIdAction()
    {
        $this->setEngine();
        $nodos = $this->adapter->fetchAllNodesByWorkflowId($this->workflowId);
        $transiciones = $this->adapter->fetchAllTransitionsByWorkflowId($this->workflowId);
        return WorkflowApi\WorkflowGetAllResponse::success($nodos, $transiciones);
    }
    
    /**
    * @SWG\Get(
    *     path="/workflow/services/v1/{adapterName}/workflow",
    *     summary="Fetch all Workflows",
    *     operationId="workflow/get",
    *     consumes={"application/json"},
    *     produces={"application/json"},
    *     tags={"workflow"},
    *     @SWG\Parameter(
    *         description="adapterName",
    *         format="string",
    *         default="workflow_adapter_atvadapter",
    *         in="path",
    *         name="adapterName",
    *         required=false,
    *         type="string"
    *     ), 
    *     @SWG\Response(
    *         response=200,
    *         description="successful operation",
    *     ),
    *     @SWG\Response(
    *         response="500",
    *         description="Error processing the response",
    *     )
    * )
    */
    public function geAllAction()
    {
        $this->setEngine();
        return WorkflowApi\Response::success($this->adapter->fetchAllWorkflows());
    }
    
    /**
    * @SWG\Post(
    *     path="/workflow/services/v1/{adapterName}/{workflowid}/node/add",
    *     summary="Insert a new node",
    *     operationId="workflow/get",
    *     consumes={"application/json"},
    *     produces={"application/json"},
    *     tags={"workflow"},
    *     @SWG\Parameter(
    *         description="ID of the workflow",
    *         format="int64",
    *         default=1,
    *         in="path",
    *         name="workflowid",
    *         required=true,
    *         type="integer"
    *     ),
    *     @SWG\Parameter(
    *         description="adapterName",
    *         format="string",
    *         default="workflow_adapter_atvadapter",
    *         in="path",
    *         name="adapterName",
    *         required=false,
    *         type="string"
    *     ), 
    *     @SWG\Parameter(
    *         name="body",
    *         in="body",
    *         description="addNodoRequest",
    *         required=true,
    *         @SWG\Schema(ref="#/definitions/addNodoRequest"),
    *     ),
    *     @SWG\Response(
    *         response=200,
    *         description="successful operation",
    *     ),
    *     @SWG\Response(
    *         response="500",
    *         description="Error processing the response",
    *     )
    * )
    */
    public function AddNodeAction()
    {
        $this->setEngine();
        $rawRequest = json_decode($this->getRequest()->getContent());
        $response = array();
        if(is_null($rawRequest->id) || is_string($rawRequest->id)  )
        {
            $id = $this->adapter->addNode($this->workflowId, $rawRequest->nombre);
        }
        else
        {
            $id =$rawRequest->id ;
            $this->adapter->updateNode($rawRequest->id , $rawRequest->nombre);
        }
        array_push($response, $id);
        return WorkflowApi\Response::success($response);
    }
    
    /**
    * @SWG\Delete(
    *     path="/workflow/services/v1/{adapterName}/node/delete/{nodoid}",
    *     summary="deletes a node",
    *     operationId="workflow/node/delete",
    *     consumes={"application/json"},
    *     produces={"application/json"},
    *     tags={"workflow"},
    *     @SWG\Parameter(
    *         description="adapterName",
    *         format="string",
    *         default="workflow_adapter_atvadapter",
    *         in="path",
    *         name="adapterName",
    *         required=false,
    *         type="string"
    *     ), 
    *     @SWG\Parameter(
    *         description="nodoId",
    *         format="string",
    *         default="1",
    *         in="path",
    *         name="nodoId",
    *         required=false,
    *         type="string"
    *     ),   
    *     @SWG\Response(
    *         response=200,
    *         description="successful operation",
    *     ),
    *     @SWG\Response(
    *         response="500",
    *         description="Error processing the response",
    *     )
    * )
    */
    public function deleteNodeAction()
    {
        $this->setEngine();
        $this->adapter->deleteNode($this->params('nodoId'));
        return WorkflowApi\Response::success();
    }
    
    /**
    * @SWG\Delete(
    *     path="/workflow/services/v1/{adapterName}/transition/delete/{transitionId}",
    *     summary="deletes a transition",
    *     operationId="workflow/node/delete",
    *     consumes={"application/json"},
    *     produces={"application/json"},
    *     tags={"workflow"},
    *     @SWG\Parameter(
    *         description="adapterName",
    *         format="string",
    *         default="workflow_adapter_atvadapter",
    *         in="path",
    *         name="adapterName",
    *         required=false,
    *         type="string"
    *     ), 
    *     @SWG\Parameter(
    *         description="transitionId",
    *         format="string",
    *         default="1",
    *         in="path",
    *         name="transitionId",
    *         required=false,
    *         type="string"
    *     ),   
    *     @SWG\Response(
    *         response=200,
    *         description="successful operation",
    *     ),
    *     @SWG\Response(
    *         response="500",
    *         description="Error processing the response",
    *     )
    * )
    */
    public function deleteTransitionAction()
    {
        $this->setEngine();
        $this->adapter->deleteTransition($this->params('transitionId'));
        return WorkflowApi\Response::success();
    }
    
    private function setEngine()
    {
        $this->workflowId = is_null($this->params('workflowid')) ? 1 : $this->params('workflowid');
        $adapterName = $this->params('adapterName') == '{adapterName}' ? null : $this->params('adapterName');
        $this->adapter = \workflow_adapter_factoryadapter::getInstance($adapterName);
    }
}