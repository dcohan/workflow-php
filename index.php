<!DOCTYPE html>
<html>
    <head>
        <title>Workflow editor</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
    </head>
    <body >
        <div id="editNode" class="modal">
            <!-- Modal content -->
            <div class="modal-content">
                <div>
                    <span class="close">x</span>
                </div>  
                <div>
                    <input type="text" id="nodeLabel" ></input>
                    <input type="text" id="nodeid" style="display:none" ></input>
                    <input type="button" id="nodesave" onclick="saveNode();" value="Guardar"></input>
                </div>
            </div>

        </div>
        <div id="editTransition" class="modal">
            <!-- Modal content -->
            <div class="modal-content">
                <div>
                    <span class="close">x</span>
                </div>  
                <div>
                    <input type="text" id="transitioncondition" ></input>
                    <input type="text" id="transitionhandler" ></input>
                    <input type="text" id="transitionid" style="display:none" ></input>
                    <input type="button" id="transitionsave" onclick="saveTransition();" value="Guardar"></input>
                </div>
            </div>

        </div>
        <div class="ui-widget">
            <label for="adapters">Adapters: </label>
            <input id="adapters">
            <label for="workflows">Workflows: </label>
            <input id="workflows">
        </div>
        
        <p>--------------------------------------------------</p>
        <div id="mynetwork"></div>
		
        
        
        
        <p>--------------------------------------------------</p>

        <link rel="stylesheet" type="text/css" href="css\vis.css" />
	<link rel="stylesheet" type="text/css" href="css\buildworkflow.css" />
	<script src="js\jquery.min.js"></script>
        <script src="js\jquery-ui.min.js"></script>
	<script src="js\vis.js"></script>
	<script src="js\buildworkflow.js"></script>
    </body>
</html>


