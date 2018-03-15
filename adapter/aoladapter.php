
<?php

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
class workflow_adapter_aoladapter implements \workflow_adapter_iadapter {
    public function fetchAllInstances($limit=100)
    {
        return R::find('workflowinstancia', 'estado=? LIMIT '.$limit,
                            array(workflow_core_nodoestadoenum::ABIERTO));
    }
    
    public function fetchAllTransitions($nodoId)
    {
        return cachedDatabase::findAll('transicion','origen_id=?',array($nodoId));
    }
    
    public function registerInstance($clienteId,$workflowId)
    {
        //nos traemos el unico nodo que no es usado como destino, si hay mas de un nodo en la misma condicion
        //es un ERROR del workflow y se aborta esta ejecucion
            $startNodes = cachedDatabase::getAll('select distinct n.id from workflow w 
                                    inner join nodo n on n.workflow_id = w.id
                                    inner join transicion t on t.origen_id = n.id
                                    where w.id = ? and n.id not in (select distinct destino_id from transicion)', array($workflowId));
        
        if (count($startNodes)>1)
        {
            throw new Exception('El workflow de referencia NO puede tener mas de un nodo de inicio!');
        }
        
        if (count($startNodes)==0)
        {
            throw new Exception('El workflow de referencia DEBE tener un nodo de inicio!');
        }
        
        $instancia = R::dispense('workflowinstancia');
        $instancia->cliente_id = $clienteId;
        $instancia->estado = workflow_core_nodoestadoenum::ABIERTO;
        $instancia->nodo_id = $startNodes[0]['id'];
        R::store($instancia);
    }
    
    public function fetchContext($clienteId)
    {
        //Obtenemos el cliente
        $cliente = cachedDatabase::load('cliente',$clienteId);
        
        //solo la ultima cotizacion si es que esta activa
        $cotizacion = R::getAll('SELECT c.* FROM cotizacion c WHERE c.cliente_id = ? AND DATEDIFF(NOW(),c.fechaalta)<7 ORDER BY c.fechaalta DESC LIMIT 1;', array($clienteId));
        if (count($cotizacion)>0) 
        {
            $cotizacion = R::load('cotizacion', $cotizacion[0]['id']);
            
            //obtenemos los datos segun el tipo de cotizacion
            $cotibiz = core_biz_cotizacion::getFactory($cotizacion->tipodecotizacion_id);
            $cotizacionExtendida = $cotibiz::getByCotizacionIdPadre($cotizacion->id);
        }
        else
        {
            $cotizacion = array();
            $cotizacionExtendida = array();
        }

        
        
        return array(
          'cliente' => $cliente,
          'cotizacion' => $cotizacion,
          'cotizacionExtendida' => $cotizacionExtendida
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
            $siguienteNodo = cachedDatabase::load('nodo',$transicion->destino_id);
            echo nl2br("Transicionando instancia a nodo: ".$siguienteNodo->nombre."\n");
            $instance->nodo = $siguienteNodo;
        }
        R::store($instance);
    }
    
    public function closeInstance($instance)
    {
         $instance->estado=workflow_core_nodoestadoenum::CERRADO;
         R::store($instance);
    }
    public function fetchAllNodesByWorkflowId($workflowId)
    {
        return R::exportAll(cachedDatabase::findAll('nodo','workflow_id=1',array($workflowId)));
    }
    public function fetchAllTransitionsByWorkflowId($workflowId)
    {
        return cachedDatabase::getAll('select t.* from transicion t join nodo origen on origen.id=t.origen_id join nodo destino on destino.id=t.destino_id and origen.workflow_id=? and destino.workflow_id=? ORDER BY origen_id', array($workflowId,$workflowId));
    }
    public function addNode($workflowId, $nombre)
    {
        $nodo = R::dispense('nodo');
        $nodo->nombre= $nombre;
        $nodo->workflow_id=$workflowId;
        R::store($nodo);
        return $nodo->id;
    }
    
    public function addTransition($workflowId, $nodoOrigen, $nodoDestino, $handler, $condicion)
    {
        $transicion = R::dispense('transicion');
        $transicion->origen_id= $nodoOrigen;
        $transicion->destino_id= $nodoDestino;
        $transicion->handler= $handler;
        $transicion->condicion= $condicion;
        R::store($transicion);
        return $transicion->id;
    }
    
    public function updateNode($idNodo, $nombre)
    {
        $nodo = cachedDatabase::load('nodo', $idNodo);
        $nodo->nombre= $nombre;
        cachedDatabase::store($nodo);
    }
    public function updateTransition($idTransicion, $nodoOrigen, $nodoDestino, $handler, $condicion)
    {
        $transicion = cachedDatabase::load('nodo', $idNodo);
         $transicion->origen_id= $nodoOrigen;
        $transicion->destino_id= $nodoDestino;
        $transicion->handler= $handler;
        $transicion->condicion= $condicion;
        cachedDatabase::store($transicion);
    }
    public function fetchAllWorkflows()
    {
        $workflows = R::find('workflow');
        return R::exportAll($workflows, FALSE);
    }
    
    public function deleteNode($nodoId)
    {
        R::trash(R::load('nodo', $nodoId));
    }
    
    public function deleteTransition($transitionId)
    {
        R::trash(R::load('transicion', $transitionId));
    }
}
