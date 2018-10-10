import React, { Component } from 'react'
import { notify } from 'Components'

class AdminTrainingAreasUpdate extends Component {
    constructor() {
        super()
        this.state = {
            error: false,
            message: '',
            current_area: {}
        }
        this.handleSubmit = this.handleSubmit.bind(this)
        this.reloadData = this.reloadData.bind(this)
    }
    componentWillReceiveProps(nextProps) {
        this.setState({
            error: false,
            current_area: nextProps.area
        })
    }
    reloadData() {
        const { actions, modalId } = this.props
        actions.loadTrainingAreas().then(() => {
            $(`#${modalId}`).modal('hide')
        })
    }
    handleSubmit(e) {
        e.preventDefault()
        const { action, actions, addLastestRow } = this.props
        const { current_area } = this.state
        if (action == "create") {
            actions.createTrainingArea(current_area).then(response => {
                const { id } = response.action.payload.data
                addLastestRow(id)
                this.reloadData()
                notify.show(`Đã thêm mới ngành đào tạo ${current_area.name}`, 'primary')
            }).catch(err => {
                console.log(err);
                this.setState({
                    error: true,
                    message: err.response.data.message
                })
            })
        } else if (action == "update") {
            actions.updateTrainingArea(current_area).then(() => {
                addLastestRow(current_area.id)
                this.reloadData()
                notify.show(`Đã cập nhật ngành đào tạo ${current_area.name}`, 'primary')
            }).catch(err => {
                this.setState({
                    error: true,
                    message: err.response.data.message
                })
            })
        }
    }
    render() {
        const { modalId, action, area } = this.props
        const { current_area, error, message } = this.state

        return (
            <div id={`${modalId}`} class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="areaModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="areaModal">{ action == "create" ? "Thêm ngành đào tạo" : "Cập nhật ngành đào tạo" }</h4>
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
                                    <label class="col-sm-5 control-label">Tên Ngành đào tạo (*)</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" value={current_area.name || ''} onChange={(e) => this.setState({current_area: {...current_area, name: e.target.value}})} required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-5 control-label">Mã Ngành đào tạo (*)</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" value={current_area.areaCode || ''} onChange={(e) => this.setState({current_area: {...current_area, areaCode: e.target.value}})} required />
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

export default AdminTrainingAreasUpdate
