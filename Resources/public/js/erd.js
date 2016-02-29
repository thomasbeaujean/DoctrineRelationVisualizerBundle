/* global joint, SVGElement, managerName, baseUrl, erd */

/**
 * Fix due to chrome version 48
 * https://github.com/cpettitt/dagre-d3/issues/202
 */
SVGElement.prototype.getTransformToElement = SVGElement.prototype.getTransformToElement || function(elem) {
    return elem.getScreenCTM().inverse().multiply(this.getScreenCTM());
};

$(function() {
    $.get(baseUrl + "data/" + managerName, function(data) {
        console.log(data);

        var displayColumns = data.displayColumns;

        var entities = data.entities;

        var graph = new joint.dia.Graph;

        var paper = new joint.dia.Paper({
            el: $('#paper'),
            width: 4000,
            height: 3000,
            gridSize: 1,
            model: graph
        });

        //display all links
        paper.on('blank:pointerdblclick',
            function(cellView, evt, x, y) {
                //add all links
                _.each(relations, function(relation) {
                        graph.addCell(relation);
                });
            }
        );

        //display only links of the entity
        paper.on('cell:pointerdblclick',
            function(cellView, evt, x, y) {
                var entityUuid = cellView.model.id;

                //remove all links
                _.each(relations, function(relation) {
                    relation.remove();
                });

                //add only the link of the source
                _.each(relations, function(relation) {
                    if(relation.attributes.source.id === entityUuid) {
                        graph.addCell(relation);
                    }
                    if(relation.attributes.target.id === entityUuid) {
                        graph.addCell(relation);
                    }
                });
            }
        );

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
                url: baseUrl + 'save/' + managerName,
                data: {
                    entities: JSON.stringify(data)
                }
            });
        };

        var persistPositionTimer = null;

        var classes = {};

        entities.shift();

        $.each(entities,  function()
        {
            var entity = this.entity;

            var attributes = [];
            var height = 50;

            if (displayColumns) {
                height = 50 + 12 * entity.fields.length;
            }

            if (displayColumns) {
                $.each(entity.fields, function() {
                    attributes.push(this.name + ': ' + this.type);
                });
            }

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

        _.each(classes, function(c)
        {
            graph.addCell(c);
        });

        var relations = [];

        joint.shapes.uml.OneToOne = joint.dia.Link.extend({
            defaults: {
                type: 'uml.OneToMany',
                attrs: { '.marker-target': { d: 'M 5 10 L 20 20 L 0 10 L 20 0 z', fill: 'white' }}
                        ,labels: [{ position: -20, attrs: { text: { dy: -8, text: '1' }}}]
            }
        });

        joint.shapes.uml.ManyToOne = joint.dia.Link.extend({
            defaults: {
                type: 'uml.ManyToOne',
                attrs: { '.marker-target': { d: 'M 5 10 L 20 20 L 0 10 L 20 0 z', fill: 'white' }}
                        ,labels: [{ position: -20, attrs: { text: { dy: -8, text: '0..1' }}}]
            }
        });

        joint.shapes.uml.ManyToOneComposition = joint.dia.Link.extend({
            defaults: {
                type: 'uml.ManyToOneComposition',
                attrs: { '.marker-target': { d: 'M 5 10 L 20 20 L 0 10 L 20 0 z', fill: 'white' }}
                        ,labels: [{ position: -10, attrs: { text: { dy: -2, text: '1' }}}]
            }
        });

        joint.shapes.uml.OneToMany = joint.dia.Link.extend({
            defaults: {
                type: 'uml.OneToMany',
                attrs: { '.marker-target': { d: 'M 5 10 L 20 20 L 0 10 L 20 0 z', fill: 'white' }}
                    ,labels: [{ position: -10, attrs: { text: { dy: -2, text: '*' }}}]
            }
        });

        joint.shapes.uml.Extends = joint.dia.Link.extend({
            defaults: {
                type: 'uml.Extends',
                attrs: { '.marker-target': { d: 'M 20 10 L 20 20 L 0 10 L 20 0 z', fill: 'black' }}
            }
        });

        $.each(entities,  function() {
            var me = this.entity;

            if (me.rootEntityName !== null) {
                relations.push(new joint.shapes.uml.Extends({ source: { id: classes[me.uuid].id }, target: { id: classes[me.rootEntityName].id }}));
            }

            $.each(me.targetEntities, function(index, targetEntity) {
                switch (targetEntity.associationType) {
                    case 'MANY_TO_ONE':
                        if (targetEntity.isNullable) {
                                        relations.push(new uml.ManyToOne({ source: { id: classes[me.uuid].id }, target: { id: classes[targetEntity.uuid].id }}));
                        } else {
                                relations.push(new uml.ManyToOneComposition({ source: { id: classes[me.uuid].id }, target: { id: classes[targetEntity.uuid].id }}));
                        }
                        break;
                    case 'MANY_TO_MANY':
                                relations.push(new uml.OneToMany({ source: { id: classes[me.uuid].id }, target: { id: classes[targetEntity.uuid].id }}));
                                relations.push(new uml.OneToMany({ target: { id: classes[me.uuid].id }, source: { id: classes[targetEntity.uuid].id }}));
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

        _.each(relations, function(r)
        {
            graph.addCell(r);
        });
    });
});