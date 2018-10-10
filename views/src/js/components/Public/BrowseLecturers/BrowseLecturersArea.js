import React, { Component } from 'react'

class BrowseLecturesArea extends Component {
    constructor(props){
        super(props);

        this.markCurrentNode = this.markCurrentNode.bind(this);
        this.renderAreas = this.renderAreas.bind(this);
        this.displayChildren = this.displayChildren.bind(this);
    }

    /*  MARK CURRENT NODE TREE */
    markCurrentNode(area, event){
        $(".item__manage").removeClass("mark-current-node");
        $(".item__manage").removeClass("edit-current-node");

        var elem = $(event.currentTarget);
        var parent = elem.parent();

        parent.addClass("mark-current-node");

        //GET LIST OF LECTURERS
        this.props.loadLecturersHasArea(area, event);
    }

    /* SHOW - HIDE CHIRLDEN NODES */
    displayChildren(display, event) {
        var elem = $(event.currentTarget);

        elem.css("display","none");
        elem.siblings().css("display","inline-block");

        var bigParent = elem.parent().parent().parent();

        var children = bigParent.children('.area-tree__item');
        var createdChildren = bigParent.children('.created-child-node');

        children.css("display",display);
        createdChildren.css("display",display);
    }

    displayChildrenById = id => {
        var elem = $(`#node-${id}`).find('.fa-plus').first()

        elem.css("display","none");
        elem.siblings().css("display","inline-block");

        var bigParent = elem.parent().parent().parent();

        var children = bigParent.children('.area-tree__item');
        var createdChildren = bigParent.children('.created-child-node');

        children.css("display", 'block');
        createdChildren.css("display", 'block');
    }

    markCurrentNodeById = id => {
        $(".item__manage").removeClass("mark-current-node");
        $(".item__manage").removeClass("edit-current-node");

        var elem = $(`#node-${id}`).children(".item__manage")

        elem.addClass("mark-current-node");
    }

    componentDidMount() {
        const { areas } = this.props
        let areaNode = this.props.areaFilter
        while (areaNode) {
            areaNode = areas.find(a => a.id == areaNode)
            if (areaNode.parentId) this.displayChildrenById(areaNode.parentId)
            areaNode = areaNode.parentId
        }
        this.markCurrentNodeById(this.props.areaFilter)
    }

    renderAreas(dimensions, root, filter){
        //declare style for root node, parent nodes & children nodes
        var parentOrChild = ( dimensions.parents[root] ? " parent-node" : "" );
        var nodeStyle = (root == 0 ? " tree-root" : parentOrChild);
        const render = (
            <div key={root} id ={"node-" + root}
                class={"area-tree__item tree-node-offset-1" + nodeStyle}>
                <div class="item__manage">
                    { root == 0 ? <div class="fa-empty"></div> :
                        <span>
                            <i class="fa fa-plus" onClick={(e) => this.displayChildren("block",e)}/>
                            <i class="fa fa-minus" onClick={(e) => this.displayChildren("none",e)}/>
                        </span>
                    }

                    { root != 0 && <p class={`item__title root-node-title`}
                            onClick={(e) => this.markCurrentNode(dimensions.items[root],e)}>
                            { dimensions.items[root].name }
                        </p>
                    }

                </div>

                { dimensions.parents[root] ?
                    dimensions.parents[root].map((node) => {
                        return this.renderAreas(dimensions, node, filter)
                    }) : ''
                }
            </div>
        )

        return render;
    }

    render(){
        const { areas, areaFilter } = this.props;
        var dimensions = {
            items: new Array(),
            parents: new Array()
        }

        //convert JSON data to Multidimensional array
        areas.map((area) => {
            dimensions.items[area.id] = area;
            var parentId;

            if(area.parentId == null) {
                parentId = 0;
            } else {
                parentId = area.parentId;
            }

            if(dimensions.parents[parentId] == undefined){
                dimensions.parents[parentId] = new Array();
            }

            dimensions.parents[parentId].push(area.id);
        });

        return (
            <div class="knowledge-area-wrapper">
                <div class="knowledge-area__main-content">
                    { dimensions.parents.length >= 0 ?
                        this.renderAreas(dimensions,0,areaFilter)
                        : <span>Không có lĩnh vực nào!</span>
                    }
                </div>
            </div>
        )
    }
}

export default BrowseLecturesArea
