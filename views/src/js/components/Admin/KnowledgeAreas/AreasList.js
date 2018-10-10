import React, { Component } from 'react'
import ReactDOM from 'react-dom'
import { notify } from 'Components'

class AreasList extends Component {
    constructor(props){
        super(props);
        this.state = {
            name: '',
            currElem: '',
            currParentId: ''    //id of current area which is creating a new child.
        }

        this.deleteOneHandler = this.deleteOneHandler.bind(this);
        this.displayChildren = this.displayChildren.bind(this);
        this.markCurrentNode = this.markCurrentNode.bind(this);
        this.createHandler = this.createHandler.bind(this);
        this.submitEditHandler = this.submitEditHandler.bind(this);
        this.cancelEditHandler = this.cancelEditHandler.bind(this);
        this.editHandler = this.editHandler.bind(this);
        this.reloadData = this.reloadData.bind(this);
    }

    componentDidMount() {
        document.addEventListener("ready", this.initialTooltips);
    }

    componentDidUpdate() {
        let { currParentId } = this.state;

        if(currParentId){
            let currParent = $('#' + currParentId);
            //show - hide button
            let plus = currParent.children('.item__manage').find('.fa-plus');
            plus.css('display', 'none');
            plus.siblings().css('display', 'inline-block');
            //show all children
            let children = currParent.children('.area-tree__item');
            children.css('display', 'block');
        }
    }

    initialTooltips() {
        $('[data-toggle="tooltip"]').tooltip();
    }

    reloadData(){
        const { actions } = this.props;
        actions.reloadAreas();
    }

    /*---------------
    HANDLE DOM elements METHODS
    ---------------*/

    /*  MARK CURRENT NODE TREE */
    markCurrentNode(event){
        $(".item__manage").removeClass("mark-current-node");
        $(".item__manage").removeClass("edit-current-node");

        var elem = $(event.currentTarget);
        var parent = elem.parent();

        parent.addClass("mark-current-node");
    }


    /* CREATE NEW NODE */
    createHandler(root, dimensions, event){
        const { actions } = this.props;
        const areaslist = this;

        var createButton = $(event.currentTarget);
        var bigParent = createButton.parent().parent().parent();

        this.setState({
            currParentId: bigParent.attr('id')
        });

        //show parent node name
        $("#parent-node-name").text(
            root == 0 ? "Cây Lĩnh vực" : dimensions.items[root].name
        );

        $("#submit-creating").off("click");

        $("#submit-creating").on("click", function(e){
            if($("#child-node").val() !== ''){
                const input = $("#child-node").val()
                e.preventDefault();

                var area = {
                    name : input,
                    parentId : root == 0 ? null : root
                }

                //Submit data to DB
                actions.createArea(area).then(response => {
                    notify.show(`Lĩnh vực ${area.name} được tạo thành công`, 'primary');
                    $("#child-node").val('')
                    areaslist.reloadData();
                }).catch(error => {
                    notify.show(`Có lỗi xảy ra: ${error.response.data.message}`, 'danger');
                })
            } else {
                $("#create-node").addClass("in");
            }
        });
    }


    /* EDIT NODE */
    editHandler(id, event){
        //Update DOM
        const {dimensions} = this.props;
        var elem =  $(event.currentTarget);
        var bigParent = elem.parent().parent();

        bigParent.addClass("edit-current-node");

        var input = bigParent.children("input");
        this.setState({ name : this.props.dimensions.items[id].name });

        let newState = {
            ...this.state
        };

        newState.name = dimensions.items[id].name;
        newState.currElem = bigParent;

        this.setState(newState);
    }

    /* CANCEL HANDLING */
    cancelEditHandler(){
        //Update DOM
        var bigParent = this.state.currElem;
        bigParent.removeClass("edit-current-node");
    }

    /* ACCEPT HANDLING */
    submitEditHandler(root) {
        var arealist = this;
        //Update DOM
        var bigParent = this.state.currElem;

        bigParent.removeClass("edit-current-node");

        var input = bigParent.children("input");
        var inputText = input.val();

        if(inputText !== ''){
            var area = {
                id : root,
                name : inputText,
                parentId: this.props.dimensions.items[root].parentId
            }

            //Hanlde action update Area on DB
            this.props.actions.updateArea(area).then(response => {

                var node = bigParent.children(".item__title");
                node.text(arealist.state.name);
                notify.show(`Cập nhật lĩnh vực ${area.name} thành công`, 'primary');
                this.reloadData();

            }).catch(function (error) {
                arealist.cancelEditHandler();
                //notify.show(`Có lỗi xảy ra: ${error.response.data.message}`, 'danger');
            });
        }
    }


    /* REMOVE ONE NODE */
    deleteOneHandler(root,event){
        const { actions } = this.props;
        const { dimensions } = this.props;
        const { arealist } = this;

        var name = dimensions.items[root].name;
        var toDelete = confirm("Bạn đồng ý xóa lĩnh vực: " + name + "?");

        this.setState({
            currParentId: 'node-' + dimensions.items[root].parentId
        });

            //Accept deleting
        if (toDelete == true) {
            actions.deleteArea(root).then(response => {
                notify.show(`Xóa lĩnh vực ${dimensions.items[root].name} thành công`, 'primary');
                this.reloadData();
            }).catch(function (error) {
                notify.show(`Có lỗi xảy ra: ${error.response.data.message}`, 'danger');
            });;
        }
    }

    /* SHOW - HIDE CHIRLDEN NODES */
    displayChildren(display, event) {
        var elem = $(event.currentTarget);

        elem.css("display","none");
        elem.siblings().css("display","inline-block");

        var bigParent = elem.parent().parent().parent();

        var children = bigParent.children('.area-tree__item');
        //var createdChildren = bigParent.children('.created-child-node');

        children.css("display",display);
        //createdChildren.css("display",display);
    }

    /*---------------
    UPDATE STATE
    ---------------*/
    updateState(event){
        this.setState({name: event.target.value});
    }

    /*---------------
    RENDER AREA LIST METHOD
    ---------------*/
    renderNodes(root) {
        const dimensions = this.props.dimensions;

        //declare style for root node, parent nodes & children nodes
        var parentOrChild = ( dimensions.parents[root] ? " parent-node" : "" );
        var nodeStyle = (root == 0 ? " tree-root" : parentOrChild);

        const render = (
            <div key={root} id ={"node-" + root}
                class={"area-tree__item tree-node-offset-1" + nodeStyle}>
                <div class="item__manage">
                    { (root == 0 || !dimensions.parents[root]) ? <div class="fa-empty"></div> :
                        <span>
                            <i class="fa fa-plus" onClick={(e) => this.displayChildren("block", e)}/>
                            <i class="fa fa-minus" onClick={(e) => this.displayChildren("none", e)}/>
                        </span>
                    }

                    <p class="item__title root-node-title"
                        onClick={(e) => this.markCurrentNode(e)}>
                        { (root == 0) ? "Lĩnh vực" : dimensions.items[root].name }
                    </p>

                    { root == 0 ?
                        <button class="btn btn-primary btn-margin btn-xs"
                            data-toggle="modal" data-target="#create-node"
                            onClick={(e) => this.createHandler(root, dimensions, e)}>
                            <span data-toggle="tooltip" title="Thêm một lĩnh vực lớn" data-placement="bottom">Thêm</span>
                        </button>
                        :
                        <div class="item__btns btns-group-1">
                            <button class="btn btn-primary btn-margin btn-xs"
                                data-toggle="modal" data-target="#create-node"
                                onClick={(e) => this.createHandler(root, dimensions, e)}>
                                <span data-toggle="tooltip" data-placement="bottom" title={"Thêm một lĩnh vực con của " + dimensions.items[root].name } >Thêm</span>
                            </button>
                            <button class="btn btn-primary btn-margin btn-xs" data-toggle="tooltip" data-placement="bottom"
                                title={"Sửa lĩnh vực " + dimensions.items[root].name}
                                onClick={(e) => this.editHandler(root, e)}>Sửa</button>
                            <button class="btn btn-primary btn-margin btn-xs" data-toggle="tooltip" data-placement="bottom"
                                title={"Xóa lĩnh vực " + dimensions.items[root].name}
                                onClick={(e) => this.deleteOneHandler(root, e)}>Xóa</button>
                        </div>
                    }

                    { root == 0 ?  "" :
                        <input type="text" class="item__title-edit"
                            value = {this.state.name}
                            onChange = {this.updateState.bind(this)}
                        />
                    }

                    { root == 0 ? "" :
                        <div class="item__btns btns-group-2">
                            <button class="btn btn-primary btn-margin btn-xs" onClick={(e) => this.submitEditHandler(root, e)}>Đồng ý</button>
                            <button class="btn btn-primary btn-margin btn-xs" onClick={(e) => this.cancelEditHandler(e)}>Hủy</button>
                        </div>
                    }
                </div>

                { dimensions.parents[root] ?
                    dimensions.parents[root].map((node) => {
                        return this.renderNodes(node)
                    }) : ''
                }
            </div>
        )

        return render;
    }

    render() {
        return (
            <div>
                {this.renderNodes(0)}

                <div class="container">
                    <div class="modal fade" id="create-node" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Lĩnh vực cha: <b><span id="parent-node-name"></span></b>
                                    </h4>
                                </div>
                                <form name="create-node-form">
                                    <div class="modal-body create-node-modal-body">
                                        Tên lĩnh vực: <input type="text" name="child-node-input" id="child-node" required/>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" id="submit-creating" class="btn btn-primary" data-dismiss="modal">Tạo mới</button>
                                        <button type="button" id="cancel-creating" class="btn btn-default" data-dismiss="modal">Bỏ qua</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}

export default AreasList
