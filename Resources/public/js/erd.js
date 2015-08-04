$(function() { 
	$.get("/visualizer/data", function(data) {
		console.log(data);
		//var entities = data.data.entities;
		var entities = data.entities;
		
		var graph = new joint.dia.Graph;
		
		var paper = new joint.dia.Paper({
		    el: $('#paper'),
		    width: 1500,
		    height: 1000,
		    gridSize: 1,
		    model: graph
		});
		
		var uml = joint.shapes.uml;

		
		var element = function(elm, x, y, label) {
		    var cell = new elm({ position: { x: x, y: y }, attrs: { text: { text: label }}});
		    graph.addCell(cell);
		    return cell;
		};
		
		var link = function(elm1, elm2) {
		    var myLink = new erd.Line({ source: { id: elm1.id }, target: { id: elm2.id }});
		    graph.addCell(myLink);
		    return myLink;
		};
		
		var entitiesById = {};
		
		var persistPositions = function ()
		{
			var data = {};
			
			$.each(classes, function(uuid, entity) {
				data[entity.uuid] = {					
					x: entity.x,
					y: entity.y
				};
			});
			
			$.ajax({
				type: "POST",
				url: '/visualizer/save',
				data: {
					entities: JSON.stringify(data)
				}
			});
		}
		
		var persistPositionTimer = null;
		

		var classes = {
		};
		
		$.each(entities,  function(){
			  var entity = this.entity;
			  var type = eval('erd.' + entity.type);
			  
			  var attributes = [];
			  var height = 50 + 12 * entity.fields.length;
			  
			  $.each(entity.fields, function() {
				  attributes.push(this.name + ': ' + this.type);
			  });
			  
			  var entityClass = new uml.Class({
			        position: { x:this.x, y: this.y },
			        size: { width: 160, height: height },
			        name: entity.shortName,
			        attributes: attributes,
			        methods: []
			    });
			  
			  entityClass.uuid = entity.uuid;
			  entityClass.x = this.x;
			  entityClass.y = this.y;
			  
			  classes[entity.uuid] = entityClass;
			  
			  entityClass.on('change:position', function() 
			  {
				  var entity = classes[this.uuid];
				  
				  if (this.changed) {
					  entity.x = this.changed.position.x;
					  entity.y = this.changed.position.y;
					  
					  if (persistPositionTimer !== null) {
						  clearTimeout(persistPositionTimer);
					  }
					  persistPositionTimer = window.setTimeout(persistPositions, 1000);
				  }
			  });
		});

		_.each(classes, function(c) { graph.addCell(c); });
		
		var relations = [];
		
		joint.shapes.uml.OneToOne = joint.dia.Link.extend({
		    defaults: {
		        type: 'uml.OneToMany',
		        attrs: { '.marker-target': { d: 'M 20 10 L 20 20 L 0 10 L 20 0 z', fill: 'white' }}
		    }
		});
		
		joint.shapes.uml.ManyToOne = joint.dia.Link.extend({
		    defaults: {
		        type: 'uml.OneToMany',
		        attrs: { '.marker-target': { d: 'M 20 10 L 20 20 L 0 10 L 20 0 z', fill: 'white'}}
		    }
		});
		
		joint.shapes.uml.ManyToOneComposition = joint.dia.Link.extend({
		    defaults: {
		        type: 'uml.ManyToOneComposition',
		        attrs: { '.marker-target': { d: 'M 20 20 L 20 0 L 0 0 L 0 20 z', fill: 'white'}}
		    }
		});
		
		joint.shapes.uml.OneToMany = joint.dia.Link.extend({
		    defaults: {
		        type: 'uml.OneToMany',
		        attrs: { '.marker-target': { d: 'M 20 10 L 20 20 L 0 10 L 20 0 z', fill: 'black'}}
		    }
		});
		
		$.each(entities,  function(){
			var me = this.entity;
			$.each(me.targetEntities, function(index, targetEntity) {
				switch (targetEntity.associationType) {
                    case 'MANY_TO_ONE':
                    	if (targetEntity.isNullable) {
                    		relations.push(new uml.ManyToOne({ source: { id: classes[me.uuid].id }, target: { id: classes[targetEntity.uuid].id }}));
                    	} else {
                    		relations.push(new uml.ManyToOneComposition({ source: { id: classes[me.uuid].id }, target: { id: classes[targetEntity.uuid].id }}));
                    	}
                        break;
                    case 'ONE_TO_ONE':
                    	relations.push(new uml.OneToOne({ source: { id: classes[me.uuid].id }, target: { id: classes[targetEntity.uuid].id }}));
                        break;
                    case 'ONE_TO_MANY':
                    	relations.push(new uml.OneToMany({ source: { id: classes[me.uuid].id }, target: { id: classes[targetEntity.uuid].id }}));
                        break;
                    default:
                    	console.log(targetEntity.associationType);
                    	break;
				}
			});
		});
		
		_.each(relations, function(r) { graph.addCell(r); });
	});
});