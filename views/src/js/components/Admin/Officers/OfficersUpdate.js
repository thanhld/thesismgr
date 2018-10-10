import React, { Component } from 'react'
import { notify } from 'Components'
import { isAdmin } from 'Helper'
import { mailerActions } from 'Actions';

class AdminOfficersUpdate extends Component {
    constructor() {
        super()
        this.state = {
            error: false,
            message: '',
            current_officer: {}
        }
        this.reloadData = this.reloadData.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
    }
    componentWillReceiveProps(nextProps) {
        const { current_officer } = this.state
        this.setState({
            error: false,
            current_officer: nextProps.officer
        })
    }
    reloadData() {
        const { actions, modalId } = this.props
        actions.loadOfficers().then(() => {
            $(`#${modalId}`).modal('hide')
        })
    }
    handleSubmit(OfficerId, e) {
        e.preventDefault()
        const { action, actions, addLastestRow } = this.props
        const { current_officer } = this.state
        if (action == "create") {
            actions.createOfficer(current_officer).then(resp => {
                const { data } = resp.action.payload
                if (data.length == 0) {
                    // addLastestRow(uid)
                    mailerActions.setPasswordMail();
                    this.reloadData()
                    notify.show(`Đã thêm mới cán bộ ${current_officer.fullname}`, 'primary')
                } else {
                    this.setState({
                        error: true,
                        message: data[0].error
                    })
                }
            }).catch(err => {
                this.setState({
                    error: true,
                    message: err.response.data.message
                })
            })
        } else if (action == "update") {
            actions.updateOfficer(current_officer).then(resp => {
                if (OfficerId) {
                    addLastestRow(OfficerId)
                    this.reloadData()
                    notify.show(`Đã cập nhật cán bộ ${current_officer.fullname}`, 'primary')
                }
            }).catch(err => {
                this.setState({
                    error: true,
                    message: err.response.data.message
                })
            })
        }
    }
    render() {
        const { modalId, action, auth, officer, degrees, departments } = this.props
        const { current_officer, error, message } = this.state
        return (
            <div id={`${modalId}`} class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="officerModal">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="officerModal">{ action == "create" ? "Thêm cán bộ" : "Cập nhật thông tin cán bộ" }</h4>
                        </div>
                        <form class="form-horizontal" onSubmit={(e) => this.handleSubmit(current_officer.id, e)}>
                            <div class="modal-body">
                                { error &&
                                    <div>
                                        <div class="text-message-error">Có lỗi xảy ra: {message}</div>
                                        <br />
                                    </div> }
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Tên tài khoản (*)</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value={current_officer.username || ''} onChange={(e) => this.setState({current_officer: {...current_officer, username: e.target.value}})} required />
                                    </div>
                                </div>
                                { action == "create" &&
                                    <div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Mật khẩu (*)</label>
                                            <div class="col-sm-9">
                                                <input type="password" class="form-control" value={current_officer.password || ''} onChange={(e) => this.setState({current_officer: {...current_officer, password: e.target.value}})} required />
                                            </div>
                                        </div>
                                        {/* TODO: confirm password */}
                                    </div> }
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Mã cán bộ (*)</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value={current_officer.officerCode || ''} onChange={(e) => this.setState({current_officer: {...current_officer, officerCode: e.target.value}})} required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Tên cán bộ (*)</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value={current_officer.fullname || ''} onChange={(e) => this.setState({current_officer: {...current_officer, fullname: e.target.value}})} required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">VNU email (*)</label>
                                    <div class="col-sm-9">
                                        <input type="email" class="form-control" value={current_officer.vnuMail || '@vnu.edu.vn'} onChange={(e) => this.setState({current_officer: {...current_officer, vnuMail: e.target.value}})} required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Loại cán bộ (*)</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" value={current_officer.role || ""} onChange={e => this.setState({current_officer: {...current_officer, role: e.target.value}})}>
                                            <option disabled hidden value="">Chọn Loại cán bộ</option>
                                            <option value="3">Giảng viên</option>
                                            <option value="4" disabled={!isAdmin(auth && auth.user)}>Chuyên viên</option>
                                            <option value="5">Cán bộ ngoài</option>
                                            <option value="6">Trưởng bộ môn, PTN</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Học hàm, học vị</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" value={current_officer.degreeId || ""} onChange={e => this.setState({current_officer: {...current_officer, degreeId: e.target.value}})}>
                                            <option disabled hidden value="">Chọn Học hàm, học vị</option>
                                            { degrees.list && degrees.list.map(obj => <option key={obj.id} value={obj.id}>{obj.name}</option>) }
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Đơn vị công tác</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" value={current_officer.departmentId || ""} onChange={e => this.setState({current_officer: {...current_officer, departmentId: e.target.value}})} required>
                                            <option disabled hidden value="">Chọn Đơn vị công tác</option>
                                            { departments.list && departments.list.map(obj => <option key={obj.id} value={obj.id}>{obj.name}</option>) }
                                        </select>
                                    </div>
                                </div>
                                { action == "create" &&
                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-9">
                                            <input type="checkbox" checked disabled /> <i>Gửi thông báo qua email xác nhận tài khoản</i>
                                        </div>
                                    </div> }
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">{action == "create" ? "Tạo mới" : "Cập nhật"}</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Bỏ qua</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        )
    }
}

export default AdminOfficersUpdate
