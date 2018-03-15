<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author David
 */
interface workflow_adapter_iadapter {
    public function fetchAllInstances($limit=100);
    public function fetchAllTransitions($nodoId);
    public function registerInstance($clienteId,$workflowId);
    public function fetchContext($clienteId);
    public function updateInstance($instance, $transicion);
    public function closeInstance($instance);
    public function fetchAllNodesByWorkflowId($workflowId);
    public function fetchAllTransitionsByWorkflowId($workflowId);
    public function addNode($workflowId, $nombre);
    public function addTransition($workflowId, $nodoOrigen, $nodoDestino, $handler, $condicion);
    public function updateNode($idNodo, $nombre);
    public function updateTransition($idNodo, $nodoOrigen, $nodoDestino, $handler, $condicion);
    public function fetchAllWorkflows();
    public function deleteNode($nodoId);
    public function deleteTransition($transitionId);
}
