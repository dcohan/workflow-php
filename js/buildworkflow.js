$(document).ready(function() {
    var modal = document.getElementById('editNode');
    var span = document.getElementsByClassName("close")[0];
    
    span.onclick = function() {
        modal.style.display = "none";
    };
    
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
    
    var adapters = [
        { label:'AOL',value:'workflow_adapter_aoladapter'},
        { label:'ATV',value:'workflow_adapter_atvadapter'}
    ];
    $( "#adapters" ).autocomplete({
      source: adapters,
      select: function(value, data){ 
        if (data) {
            resetGraph();
            var adapterName = data.item.value;
            value = data.item.value;
            $( "#workflows" ).val('');
            $.get('services/v1/'+adapterName+'/workflow')
                    .success(function(data) {   
                        var workflows = [];
                        for(var i=0; i < data.length; i++) {
                            workflows.push( { label:data[i].nombre,value:data[i].id});
                        }

                        $( "#workflows" ).autocomplete({
                            source: workflows,
                            select: function(value, data){ 
                              if (data) {
                                  var workflowId = data.item.value;
                                  loadData(workflowId,adapterName );
                              }
                            }
                        });    
            });
        }
      }
    });
    
    

    
});

function showModalNode(nodeData, callback)
{
    $('#nodeLabel').val(nodeData.label);
    $('#nodeid').val(nodeData.id);
    $('#nodesave').click(function()
    {
        callback(nodeData);
    });
    var modal = document.getElementById('editNode');
    modal.style.display = "block";
}

function showModalTransition(transitionData, callback)
{
    $('#transitioncondition').val(transitionData.tooltip);
    $('#transitionid').val(transitionData.id);
    $('#transitionsave').click(function()
    {
        callback(transitionData);
    });
    var modal = document.getElementById('editTransition');
    modal.style.display = "block";
}

function saveNode()
{
    var workflowId = $('#workflows').val();
    if (!workflowId)
    {
        workflowId=getParameterByName('workflowId') ? getParameterByName('workflowId') : 1;
    }
    
    var adapterName = $('#adapters').val();
    if (!adapterName)
    {
        adapterName=getParameterByName('adapterName') ? getParameterByName('adapterName') : 'workflow_adapter_aoladapter';
    }
    
    
    $.ajax({
        async: false,
        type: 'POST',
        url: 'services/v1/'+adapterName+'/node/add',
        dataType: 'json',
        data: JSON.stringify({ id: $('#nodeid').val(), nombre:$('#nodeLabel').val() }),
        timeout: 4000,
        success: function(result) {
            var modal = document.getElementById('editNode');
            modal.style.display = "none";
            loadData();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            var modal = document.getElementById('editNode');
            modal.style.display = "none";   
        }
    });
}

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

function formatNodes(nodes)
{
    var nodeset = [];
    for(var i=0; i < nodes.length; i++)
    {
        nodo = nodes[i];
        //fijo el primero
        if (i==0) {
            nodeset.push({'id':nodo.id, 'label':nodo.nombre, 'borderWidth':3,'fixed':true,'x':10,'y':10,'color':'#00ff00','shape':'circle','mass':10});
        } else {
            if (i==nodes.length-1) {
                nodeset.push({'id':nodo.id, 'label':nodo.nombre, 'borderWidth':3,'fixed':true,'color':'#ff0066','shape':'box','mass':10});
            } else {
                nodeset.push({'id':nodo.id, 'label':nodo.nombre,'mass':3});
            }
        }
    }
    return nodeset;
}

function formatTransitions(transitions)
{
    var edgeset=[];
    for(var i=0; i < transitions.length; i++)
    {
        transicion = transitions[i];
        var title='<strong>Condicion: </strong>'+transicion.condicion+' </br><strong>Handler :</strong> '+transicion.handler;
        edgeset.push({'id':transicion.id,'from': transicion.origen_id, 'to': transicion.destino_id, 'arrows':'to','title':title,'label':'mostrar formula'});
    };
    return edgeset;
}

function deleteTransitions(edges)
{
    for(var i=0; i < transitions.length; i++)
    {
        $.ajax({
            url: 'services/v1/'+adapterName+'/transition/delete/'+transitions[i],
            type: 'DELETE'
        });
    }
}

function deleteNodes(nodes)
{
    for(var i=0; i < nodes.length; i++)
    {
        $.ajax({
            url: 'services/v1/'+adapterName+'/node/delete/'+nodes[i],
            type: 'DELETE'
        });
    }
}

function loadData(workflowId, adapterName)
{
    if(!workflowId)
    {
        workflowId = $('#workflows').val();
    }
    if (!workflowId)
    {
        workflowId=getParameterByName('workflowId') ? getParameterByName('workflowId') : 1;
    }
    
    if (!adapterName)
    {
        adapterName = $('#adapters').val();
    }
    if (!adapterName)
    {
        adapterName=getParameterByName('adapterName') ? getParameterByName('adapterName') : 'workflow_adapter_aoladapter';
    }
    
    $.get('services/v1/'+adapterName+'/workflow/'+workflowId)
        .success(function(data) {
    
            var nodes = new vis.DataSet(formatNodes(data.nodos));
            var edges = new vis.DataSet(formatTransitions(data.transiciones));

            // create a network
            var container = document.getElementById('mynetwork');
            var data = {
              nodes: nodes,
              edges: edges
            };
            var options = {
              autoResize: true,
              height: '600px',
              width: '1000px',
               manipulation: {
                enabled: true,
                initiallyActive: true,
                addNode: function(data, callback) {
                    showModalNode(data, callback);
                },
                addEdge: function(data, callback)
                {
                    showModalTransition(data, callback);
                },
                editNode: function(data, callback) {
                   showModalNode(data, callback);
                },
                editEdge: true,
                deleteNode: function(data, callback) {
                    deleteTransitions(data.edges);
                    deleteNodes(data.nodes);
                    callback(data);
                },
                deleteEdge: function(data, callback) {
                    deleteTransitions(data.edges);
                    callback(data);
                },
                controlNodeStyle:{
                  // all node options are valid.
                }
              }

            };
            var network = new vis.Network(container, data, options);
            network.fit();
        });
}

function resetGraph()
{
    var container = document.getElementById('mynetwork');
    var data = {
        nodes: [],
        edges: []
      };

    var options = {
              autoResize: true,
              height: '600px',
              width: '1000px',
               manipulation: {
                enabled: false,
                initiallyActive: false,
                addNode: false,
                addEdge: false,
                editNode: function(data, callback)
                {
                    
                },
                editEdge: false,
                deleteNode: false,
                deleteEdge: false,
                controlNodeStyle:{
                  // all node options are valid.
                }
              }

            };
     var network = new vis.Network(container, data, options);
     
 }
