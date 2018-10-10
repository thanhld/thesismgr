import React, { Component } from 'react'
import { notify } from 'Components'

class AdminTrainingProgramsUpdate extends Component {
    constructor() {
        super()
        this.state = {
            error: false,
            message: '',
            current_program: {}
        }
        this.handleSubmit = this.handleSubmit.bind(this)
        this.reloadData = this.reloadData.bind(this)
    }
    componentWillReceiveProps(nextProps) {
        this.setState({
            error: false,
            current_program: nextProps.program
        })
        if (nextProps.action == "create") {
            this.setState({
                current_program: {...nextProps.program, isInUse: 1}
            })
        }
    }
    reloadData() {
        const { actions, modalId } = this.props
        actions.loadTrainingPrograms().then(() => {
            $(`#${modalId}`).modal('hide')
        })
    }
    handleSubmit(e) {
        e.preventDefault()
        const { action, actions, addLastestRow } = this.props
        const { current_program } = this.state
        if (action == "create") {
            actions.createTrainingProgram(current_program).then(response => {
                const { id } = response.action.payload.data
                addLastestRow(id)
                this.reloadData()
                notify.show(`Đã thêm mới chương trình đào tạo ${current_program.name}`, 'primary')
            }).catch(err => {
                this.setState({
                    error: true,
                    message: err.response.data.message
                })
            })
        } else if (action == "update") {
            actions.updateTrainingProgram(current_program).then(() => {
                addLastestRow(current_program.id)
                this.reloadData()
                notify.show(`Đã cập nhật chương trình đào tạo ${current_program.name}`, 'primary')
            }).catch(err => {
                this.setState({
                    error: true,
                    message: err.response.data.message
                })
            })
        }
    }
    render() {
        const { modalId, action, areas, types, levels, departments } = this.props
        const { current_program, error, message } = this.state

        return (
            <div id={`${modalId}`} class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="programModal">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="programModal">{ action == "create" ? "Thêm Chương trình đào tạo" : "Cập nhật Chương trình đào tạo" }</h4>
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
                                    <label class="col-sm-4 control-label">Tên Chương trình đào tạo (*)</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" value={current_program.name || ''} onChange={(e) => this.setState({current_program: {...current_program, name: e.target.value}})} required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Mã Chương trình đào tạo (*)</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" value={current_program.programCode || ''} onChange={(e) => this.setState({current_program: {...current_program, programCode: e.target.value}})} required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Ngành đào tạo (*)</label>
                                    <div class="col-sm-7">
                                        <select class="form-control" value={current_program.trainingAreasId || ''} onChange={e => this.setState({current_program: {...current_program, trainingAreasId: e.target.value}})} required>
                                            <option disabled hidden value="">Chọn Ngành đào tạo</option>
                                            { areas && areas.list && areas.list.map(obj => <option key={obj.id} value={obj.id}>{obj.name}</option>) }
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Hệ đào tạo (*)</label>
                                    <div class="col-sm-7">
                                        <select class="form-control" value={current_program.trainingTypesId || ''} onChange={e => this.setState({current_program: {...current_program, trainingTypesId: e.target.value}})} required>
                                            <option value="" disabled hidden>Chọn Hệ đào tạo</option>
                                            { types.list && types.list.map(obj => <option key={obj.id} value={obj.id}>{obj.name}</option>) }
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Bậc đào tạo (*)</label>
                                    <div class="col-sm-7">
                                        <select class="form-control" value={current_program.trainingLevelsId || ''} onChange={e => this.setState({current_program: {...current_program, trainingLevelsId: e.target.value}})} required>
                                            <option value="" disabled hidden>Chọn Bậc đào tạo</option>
                                            { levels.list && levels.list.map(obj => <option key={obj.id} value={obj.id}>{obj.name}</option>) }
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Năm bắt đầu</label>
                                    <div class="col-sm-7">
                                        <input type="number" min="1950" max="2100" class="form-control" value={current_program.startTime || ''} onChange={e => this.setState({current_program: {...current_program, startTime: e.target.value}})} />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Thời gian đào tạo (*)</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" placeholder="Đơn vị năm. Ví dụ: 4, 4.5, ..." value={current_program.trainingDuration || ''} onChange={(e) => this.setState({current_program: {...current_program, trainingDuration: e.target.value}})} required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Tên văn bằng (VN) (*)</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" value={current_program.vietnameseThesisTitle || ''} onChange={(e) => this.setState({current_program: {...current_program, vietnameseThesisTitle: e.target.value}})} required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Tên văn bằng (EN)</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" value={current_program.englishThesisTitle || ''} onChange={(e) => this.setState({current_program: {...current_program, englishThesisTitle: e.target.value}})} />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Bộ môn phụ trách (*)</label>
                                    <div class="col-sm-7">
                                        <select class="form-control" value={current_program.departmentId || ''} onChange={e => this.setState({current_program: {...current_program, departmentId: e.target.value}})} required>
                                            <option value="" disabled hidden>Chọn Bộ môn phụ trách</option>
                                            { departments.list && departments.list.map(obj => (obj.type == "1" || obj.type == "4") && <option key={obj.id} value={obj.id}>{obj.name}</option>) }
                                        </select>
                                    </div>
                                </div>
                                { action == 'create' &&
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Còn hiệu lực? (*)</label>
                                        <div class="col-sm-7">
                                            <select class="form-control" defaultValue="1" disabled required>
                                                <option value="0">Không</option>
                                                <option value="1">Có</option>
                                            </select>
                                        </div>
                                    </div> }
                                { action == 'update' &&
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Còn hiệu lực? (*)</label>
                                        <div class="col-sm-7">
                                            <select class="form-control" value={current_program.isInUse || "1"} onChange={e => this.setState({current_program: {...current_program, isInUse: e.target.value}})} required>
                                                <option value="0">Không</option>
                                                <option value="1">Có</option>
                                            </select>
                                        </div>
                                    </div> }
                                  <div class="form-group">
                                      <label class="col-sm-4 control-label">Hệ số quy đổi về KL/LV luận hệ chuẩn (*)</label>
                                      <div class="col-sm-7">
                                          <input type="text" class="form-control" placeholder="Số thực" value={current_program.thesisNormalizedFactor || ''} onChange={(e) => this.setState({current_program: {...current_program, thesisNormalizedFactor: e.target.value}})} required />
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

export default AdminTrainingProgramsUpdate
