import React, { Component } from 'react'
import { notify } from 'Components'

class TypeUpdate extends Component {
    constructor() {
        super()
        this.state = {
            error: false,
            message: '',
            current_type: {}
        }
        this.handleSubmit = this.handleSubmit.bind(this)
        this.reloadData = this.reloadData.bind(this)
    }
    componentWillReceiveProps(nextProps) {
        this.setState({
            error: false,
            current_type: nextProps.type
        })
    }
    reloadData() {
        const { actions, modalId } = this.props
        actions.loadTrainingTypes().then(() => {
            $(`#${modalId}`).modal('hide')
        })
    }
    handleSubmit(e) {
        e.preventDefault()
        const { action, actions, addLastestType } = this.props
        const { current_type } = this.state
        if (action == "create") {
            actions.createTrainingType(current_type).then(response => {
                const { id } = response.action.payload.data
                addLastestType(id)
                this.reloadData()
                notify.show(`Đã thêm mới Hệ đào tạo ${current_type.name}`, 'primary')
            }).catch(err => {
                this.setState({
                    error: true,
                    message: err.response.data.message
                })
            })
        } else if (action == "update") {
            actions.updateTrainingType(current_type).then(() => {
                addLastestType(current_type.id)
                this.reloadData()
                notify.show(`Đã cập nhật Hệ đào tạo ${current_type.name}`, 'primary')
            }).catch(err => {
                this.setState({
                    error: true,
                    message: err.response.data.message
                })
            })
        }
    }
    render() {
        const { modalId, action, type } = this.props
        const { current_type, error, message } = this.state

        return (
            <div id={`${modalId}`} class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="typeModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="typeModal">{ action == "create" ? "Thêm Hệ đào tạo" : "Cập nhật Hệ đào tạo" }</h4>
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
                                    <label class="col-sm-5 control-label">Tên Hệ đào tạo (*)</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" value={current_type.name || ''} onChange={(e) => this.setState({current_type: {...current_type, name: e.target.value}})} required />
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

export default TypeUpdate
