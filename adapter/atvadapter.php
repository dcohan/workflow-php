
<?php

use Zend\Db\Adapter\Adapter;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author David
 */
class workflow_adapter_atvadapter implements \workflow_adapter_iadapter {
    
    private $connection;
    
    public function __construct()
    {
        $_CONFIG = core_framework_config::getInstance()->getConfig()->database->atvdb; 
		if (is_null($_CONFIG))
		{
			$_CONFIG = core_framework_config::getConfig()->database->atvdb;
		}
         
        $this->connection  = new Adapter(array(
                    'driver'    => $_CONFIG->adapter,
                    'hostname'  => $_CONFIG->params->host,
                    'username'  => $_CONFIG->params->username,
                    'password'  => $_CONFIG->params->password,
                    'dbname'    => $_CONFIG->params->dbname,
                    'profiler'  => ($_CONFIG->params->profile === 'true'),
                    'persistent'=> $_CONFIG->params->persistent,
                ));    
    }
    
    private function convertArrayToClass($instancias)
    {
        $instanciasClassArray = array();
        foreach($instancias as $instancia)
        {
            $instanciasClass = new stdClass();
            foreach ($instancia as $key => $value)
            {
                $instanciasClass->$key = $value;
            }
            array_push($instanciasClassArray, $instanciasClass);
        }
        return $instanciasClassArray;
    }
    
    public function fetchAllInstances($limit=100)
    {
        //Primero me traigo todas las instancias pendientes
        $instancias = $this->connection->query('SELECT * FROM WKF_instancia WHERE estado='.workflow_core_nodoestadoenum::ABIERTO, Adapter::QUERY_MODE_EXECUTE)->toArray();
        $instancias = $this->convertArrayToClass($instancias);
        foreach($instancias as $instancia)
        {
            $clientes = $this->connection->query('SELECT * FROM SVJ_Cotizacion WHERE id='.$instancia->cliente_id, Adapter::QUERY_MODE_EXECUTE)->toArray();    
            $instancia->cliente = $this->convertArrayToClass($clientes)[0];
        }
        
        return $instancias;
    }
    
    public function fetchAllTransitions($nodoId)
    {
        $transiciones = $this->connection->query('SELECT * FROM WKF_transicion WHERE origen_id='.$nodoId, Adapter::QUERY_MODE_EXECUTE)->toArray();
        return $this->convertArrayToClass($transiciones);
    }
    
    public function registerInstance($clienteId,$workflowId)
    {
        throw new Exception('Este metodo NO debe implementarse por el momento!');
    }
    
    public function fetchContext($clienteId)
    {       
        //solo la ultima cotizacion si es que esta activa
        $cotizaciones = $this->connection->query('SELECT TOP 1 * FROM SVJ_Cotizacion WHERE id='.$clienteId, Adapter::QUERY_MODE_EXECUTE)->toArray();    
        $cotizacion = $this->convertArrayToClass($cotizaciones)[0];
        
        //obtenemos el pais de residencia
        $residencia = $this->connection->query('select distinct top(1) nombre from LOC_PaisLocale where id='.$cotizacion->idpaisresidencia, Adapter::QUERY_MODE_EXECUTE)->toArray();            
        $cotizacion->residencia = $residencia[0]['nombre'];
        
        //Obtenemos las regiones de origen y destino
        $destino = $this->connection->query('select distinct top(1) nombre from LOC_RegionDestinoLocale where id='.$cotizacion->idpaisdestino, Adapter::QUERY_MODE_EXECUTE)->toArray();            
        $cotizacion->destino = $destino[0]['nombre'];
        
        return array(
          'cotizacion' => $cotizacion
        );
    }
    
    public function updateInstance($instance, $transicion)
    {
        //actualizar workflow instance a otro nodo
        if (is_null($transicion->destino_id))
        {
            echo nl2br("Cerrando instancia, pues no tiene otro nodo destino activable\n");
            $instance->estado=workflow_core_nodoestadoenum::CERRADO;
        } 
        else 
        {
            $nodos = $this->connection->query('SELECT * FROM WKF_nodo WHERE id='.$transicion->destino_id, Adapter::QUERY_MODE_EXECUTE)->toArray();
            $nodos= $this->convertArrayToClass($nodos);
            if (count($nodos)>0)
            {
                $siguienteNodo= $nodos[0];
            }
            echo nl2br("Transicionando instancia a nodo: ".$siguienteNodo->nombre."\n");
        }
        $this->connection->query('UPDATE WKF_instancia set nodo_id='.$transicion->destino_id.' WHERE id='.$instance->id, Adapter::QUERY_MODE_EXECUTE);
    }
    
    public function closeInstance($instance)
    {
        $this->connection->query('UPDATE WKF_instancia set estado='.workflow_core_nodoestadoenum::CERRADO.' WHERE id='.$instance->id, Adapter::QUERY_MODE_EXECUTE);
    }
    
    public function fetchAllNodesByWorkflowId($workflowId)
    {
        $nodos = $this->connection->query('SELECT * FROM WKF_nodo WHERE workflow_id='.$workflowId, Adapter::QUERY_MODE_EXECUTE)->toArray();
        return $this->convertArrayToClass($nodos);
    }
    public function fetchAllTransitionsByWorkflowId($workflowId)
    {
        $sql = 'SELECT t.* from WKF_transicion t join WKF_nodo origen on origen.id=t.origen_id join WKF_nodo destino on destino.id=t.destino_id and origen.workflow_id='.$workflowId.' and destino.workflow_id='.$workflowId.' ORDER BY origen_id';
        $transiciones = $this->connection->query($sql, Adapter::QUERY_MODE_EXECUTE)->toArray();
        return $this->convertArrayToClass($transiciones);
    }
    
    public function addNode($workflowId, $nombre)
    {
        $this->connection->query("INSERT INTO WKF_nodo VALUES ('".$nombre."',0,".$workflowId.")", Adapter::QUERY_MODE_EXECUTE);
    }
    
    public function addTransition($workflowId, $nodoOrigen, $nodoDestino, $handler, $condicion)
    {
        $this->connection->query("INSERT INTO WKF_transicion VALUES (".$nodoOrigen.",".$nodoDestino.",'".$condicion."','".$handler."')", Adapter::QUERY_MODE_EXECUTE);
    }
    
    public function updateNode($idNodo, $nombre)
    {
        $this->connection->query("UPDATE WKF_nodo set nombre='".$nombre."' WHERE id=".$idNodo, Adapter::QUERY_MODE_EXECUTE);
    }
    public function updateTransition($idTransicion, $nodoOrigen, $nodoDestino, $handler, $condicion)
    {
        $this->connection->query("UPDATE WKF_transicion set origen_id=".$nodoOrigen."',destino_id='".$nodoDestino."', handler='".$handler."', condicion='".$condicion."'  WHERE id=".$idTransicion, Adapter::QUERY_MODE_EXECUTE);
    }
    public function fetchAllWorkflows()
    {
        $workflows = $this->connection->query('SELECT * FROM WKF_workflow', Adapter::QUERY_MODE_EXECUTE)->toArray();
        return $this->convertArrayToClass($workflows);        
    }
    
    public function deleteNode($nodoId)
    {
        $this->connection->query('DELETE FROM WKF_nodo WHERE id='.$nodoId, Adapter::QUERY_MODE_EXECUTE);
    }
    
    public function deleteTransition($transitionId)
    {
        $this->connection->query('DELETE FROM WKF_transicion WHERE id='.$transitionId, Adapter::QUERY_MODE_EXECUTE);
    }
}
