import React, { Component } from 'react'
import { notify } from 'Components'

class SuperuserFacultiesUpdate extends Component {
    constructor() {
        super()
        this.state = {
            error: false,
            message: '',
            current_degree: {}
        }
        this.handleSubmit = this.handleSubmit.bind(this)
        this.reloadData = this.reloadData.bind(this)
    }
    componentWillReceiveProps(nextProps) {
        this.setState({
            error: false,
            current_degree: nextProps.degree
        })
    }
    reloadData() {
        const { actions, modalId } = this.props
        actions.loadDegrees().then(() => {
            $(`#${modalId}`).modal('hide')
        })
    }
    handleSubmit(e) {
        e.preventDefault()
        const { action, actions, addLastestRow } = this.props
        const { current_degree } = this.state
        if (action == "create") {
            actions.createDegree(current_degree).then(response => {
                const { id } = response.action.payload.data
                addLastestRow(id)
                this.reloadData()
                notify.show(`Đã thêm mới Học hàm, học vị ${current_degree.name}`, 'primary')
            }).catch(err => {
                this.setState({
                    error: true,
                    message: err.response.data.message
                })
            })
        } else if (action == "update") {
            actions.updateDegree(current_degree).then(() => {
                addLastestRow(current_degree.id)
                this.reloadData()
                notify.show(`Đã cập nhật Học hàm, học vị ${current_degree.name}`, 'primary')
            }).catch(err => {
                this.setState({
                    error: true,
                    message: err.response.data.message
                })
            })
        }
    }
    render() {
        const { modalId, action, degree } = this.props
        const { current_degree, error, message } = this.state

        return (
            <div id={`${modalId}`} class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="degreeModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="degreeModal">{ action == "create" ? "Thêm Chức danh Khoa học" : "Cập nhật Chức danh Khoa học" }</h4>
                        </div>
                        <form class="form-horizontal" onSubmit={this.handleSubmit}>
                            <div class="modal-body">
                                { error &&
                                    <div>
                                        <div class="text-message-error">Có lỗi xảy ra: {message}</div>
                                        <br />
                                    </div>
                                }
                                <div class="form-group">
                                    <label class="col-sm-5 control-label">Tên Chức danh Khoa học (*)</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" value={current_degree.name || ''} onChange={(e) => this.setState({current_degree: {...current_degree, name: e.target.value}})} required />
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary btn-margin">
                                    {action == "create" ? "Tạo mới" : "Cập nhật"}
                                </button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Bỏ qua</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        )
    }
}

export default SuperuserFacultiesUpdate
