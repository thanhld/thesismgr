import React, { Component } from 'react'
import { notify } from 'Components'

class KnowledgeAreaModal extends Component {
    constructor(props){
        super(props);

        this.state = {
            checkedArea: {}
        }

        this.handleSubmit = this.handleSubmit.bind(this);
        this.reloadData = this.reloadData.bind(this);
    }

    handleCheckedArea(root) {
        const checked = this.state.checkedArea[root];

        let newState = {
            ...this.state
        };

        newState.checkedArea[root] = !checked;

        this.setState(newState);
    }

    reloadData() {
        this.setState({
            checkedArea: {}
        });

        const { reloadData } = this.props;
        reloadData(() => {
            $('#modalAddResearchFeild').modal('hide');
        })
    }

    handleSubmit(e) {
        e.preventDefault();

        const { checkedArea } = this.state;
        const { actions, lecturerId } = this.props;

        var areaIds = [];

        for(var key in checkedArea) {
            if(checkedArea[key] == true) {
                areaIds.push(parseInt(key));
            }
        }

        if(areaIds.length > 0) {
            actions.addLecturerAreas(areaIds, lecturerId).then(response => {
                this.reloadData()
                notify.show(`Đã thêm mới các lĩnh vực quan tâm`, 'primary')
            }).catch(err => {
                notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
            });
        }
        else { $('#modalAddResearchFeild').modal('hide'); }
    }

    renderAreasList(dimensions, root) {
        const { addedAreas } = this.props;
        const rootClass = (root == 0) ? 'root-node__title' : '';

        //check if area chosen
        const chosenAreas = addedAreas.filter(area => area.id == root);

        const chosen = (chosenAreas.length > 0);

        const disableText = chosen ? 'disable-area' : '';

        //declare style for root node, parent nodes & children nodes
        const render = (
            <div key={root} id ={"node-" + root}
                class="area-tree__item tree-node-offset-1">
                <div class="item__manage">
                    { root == 0 ? '' :
                        (chosen ?
                            <i class="fa fa-check disable-area" />
                            :
                            <input type="checkbox" class="input-check-area"
                                value={this.state.checkedArea[root] || ''}
                                name={dimensions.items[root].name}
                                onChange={() => this.handleCheckedArea(root)} />
                        )
                    }

                    <span class={"item__title " + ((root == 0) ? " item__root-title ": "") + rootClass + disableText}>
                        { (root == 0) ? "Cây danh mục" : dimensions.items[root].name }
                    </span>
                </div>

                { dimensions.parents[root] ?
                    dimensions.parents[root].map((node) => {
                        return this.renderAreasList(dimensions, node)
                    }) : ''
                }
            </div>
        )

        return render;
    }

    render() {
        const { actions, addedAreas, allAreas } = this.props;
        var dimensions = {
            items: new Array(),
            parents: new Array()
        }

        if(addedAreas && allAreas && addedAreas.length >= 0 && allAreas.length > 0){
            //convert JSON data to Multidimensional Array
            allAreas.map((area) => {
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
        }

        return (
            <div id="modalAddResearchFeild" class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="departmentModal">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="departmentModal">Thêm lĩnh vực nghiên cứu</h4>
                        </div>

                        <form class="form-horizontal" onSubmit={this.handleSubmit}>
                            <div class="modal-body">

                                <div class="form-group">
                                    <div class="col-xs-10 col-sm-10 knowledge-area-form">
                                        { this.renderAreasList(dimensions, 0) }
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Đồng ý</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Bỏ qua</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        )
    }
}

export default KnowledgeAreaModal
