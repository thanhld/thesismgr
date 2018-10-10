import React, { Component } from 'react'
import { notify } from 'Components'

class LevelUpdate extends Component {
    constructor() {
        super()
        this.state = {
            error: false,
            message: '',
            current_level: {}
        }
        this.handleSubmit = this.handleSubmit.bind(this)
        this.reloadData = this.reloadData.bind(this)
    }
    componentWillReceiveProps(nextProps) {
        if (!nextProps.level) return false
        this.setState({
            error: false,
            current_level: nextProps.level
        })
    }
    reloadData() {
        const { actions, modalId } = this.props
        actions.loadTrainingLevels().then(() => {
            $(`#${modalId}`).modal('hide')
        })
    }
    handleSubmit(e) {
        e.preventDefault()
        const { action, actions, addLastestLevel } = this.props
        const { current_level } = this.state
        if (action == "create") {
            actions.createTrainingLevel({...current_level, levelType: parseInt(current_level.levelType)}).then(response => {
                const { id } = response.action.payload.data
                addLastestLevel(id)
                this.reloadData()
                notify.show(`Đã thêm mới Bậc đào tạo ${current_level.name}`, 'primary')
            }).catch(err => {
                this.setState({
                    error: true,
                    message: err.response.data.message
                })
            })
        } else if (action == "update") {
            actions.updateTrainingLevel(current_level).then(() => {
                addLastestLevel(current_level.id)
                this.reloadData()
                notify.show(`Đã cập nhật Bậc đào tạo ${current_level.name}`, 'primary')
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
        const { current_level, error, message } = this.state
        return (
            <div id={`${modalId}`} class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="levelModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="levelModal">{ action == "create" ? "Thêm Bậc đào tạo" : "Cập nhật Bậc đào tạo" }</h4>
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
                                    <label class="col-sm-5 control-label">Tên bậc đào tạo (*)</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" value={current_level.name || ''} onChange={(e) => this.setState({current_level: {...current_level, name: e.target.value}})} required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-5 control-label">Học viên (*)</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" value={current_level.levelType || '1'} onChange={e => this.setState({current_level: {...current_level, levelType: e.target.value}})} required>
                                            <option value="1">Sinh viên</option>
                                            <option value="2">Học viên cao học</option>
                                            <option value="3">Nghiên cứu sinh</option>
                                        </select>
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

export default LevelUpdate
