import React, { Component } from 'react';
import AreasList from './AreasList';

class KnowledgeArea extends Component {
    constructor(props){
        super(props);
    }

    componentWillMount() {
        this.props.actions.loadAreas();
    }

    render() {
        const { numbers, areas } = this.props.knowledgeAreas;

        var dimensions = {
            items: new Array(),
            parents: new Array()
        }

        //convert JSON data to Multidimensional array
        areas.map((area) => {
            dimensions.items[area.id] = area;
            var parentId;

            if (area.parentId == null) {
                parentId = 0;
            } else {
                parentId = area.parentId;
            }

            if (dimensions.parents[parentId] == undefined){
                dimensions.parents[parentId] = new Array();
            }

            dimensions.parents[parentId].push(area.id);
        });

        return (
            <div class="knowledge-area-wrapper">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 page-title">Lĩnh vực nghiên cứu</div>
                </div>
                <br />
                <div class="knowledge-area__main-content">
                    { dimensions.parents.length >= 0 ?
                        <AreasList dimensions={dimensions} actions={this.props.actions}/> : ''
                    }
                </div>
            </div>
        )
    }
}

export default KnowledgeArea
