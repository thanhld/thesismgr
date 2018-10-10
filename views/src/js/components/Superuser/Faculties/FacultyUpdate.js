import React, { Component } from 'react'
import { notify } from 'Components'

class SuperuserFacultiesUpdate extends Component {
    constructor() {
        super()
        this.state = {
            error: false,
            message: '',
            current_faculty: {},
            confirmPassword: ''
        }
        this.checkPassword = this.checkPassword.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this.reloadData = this.reloadData.bind(this)
    }
    componentWillReceiveProps(nextProps) {
        this.setState({
            error: false,
            current_faculty: nextProps.faculty
        })
    }
    reloadData() {
        const { actions, modalId } = this.props
        actions.loadFaculties().then(() => {
            $(`#${modalId}`).modal('hide')
        })
    }
    handleSubmit(e) {
        e.preventDefault()
        // action = ['create', 'update']; actions = redux actions
        const { action, actions, addLastestRow } = this.props
        const { current_faculty, confirmPassword } = this.state
        const { password } = current_faculty
        if (action == "create" && confirmPassword == password) {
            actions.createFaculty(current_faculty).then(response => {
                const { id } = response.action.payload.data
                addLastestRow(id)
                this.reloadData()
                notify.show(`Đã thêm mới khoa ${current_faculty.name}`, 'primary')
            }).catch(err => {
                this.setState({
                    error: true,
                    message: err.response.data.message
                })
            })
        } else if (action == "update") {
            actions.updateFaculty(current_faculty).then(() => {
                addLastestRow(current_faculty.id)
                this.reloadData()
                notify.show(`Đã cập nhật khoa ${current_faculty.name}`, 'primary')
            }).catch(err => {
                this.setState({
                    error: true,
                    message: err.response.data.message
                })
            })
        }
    }
    checkPassword() {
        const { confirmPassword, current_faculty: { password } } = this.state
        if (confirmPassword != password) {
            this.setState({
                error: true,
                message: "Mật khẩu không trùng khớp."
            })
        } else {
            this.setState({
                error: false
            })
        }
    }
    render() {
        const { modalId, action, faculty } = this.props
        const { current_faculty, confirmPassword, error, message } = this.state

        return (
            <div id={`${modalId}`} class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="facultyModal">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="facultyModal">{ action == "create" ? "Thêm khoa" : "Cập nhật thông tin khoa" }</h4>
                        </div>
                        <form class="form-horizontal" onSubmit={this.handleSubmit}>
                            <div class="modal-body">
                                { error &&
                                    <div>
                                        <div class="text-message-error">{message}</div>
                                        <br />
                                    </div>
                                }
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Tên khoa (*)</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value={current_faculty.name || ''} onChange={(e) => this.setState({current_faculty: {...current_faculty, name: e.target.value}})} required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Tài khoản (*)</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value={current_faculty.username || ''} onChange={(e) => this.setState({current_faculty: {...current_faculty, username: e.target.value}})} required />
                                    </div>
                                </div>
                                { action == "create" &&
                                    <div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Mật khẩu (*)</label>
                                            <div class="col-sm-9">
                                                <input type="password" class="form-control" value={current_faculty.password || ''} onChange={(e) => this.setState({current_faculty: {...current_faculty, password: e.target.value}})} required />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Nhập lại mật khẩu (*)</label>
                                            <div class="col-sm-9">
                                                <input type="password" class="form-control" value={confirmPassword} onChange={(e) => this.setState({confirmPassword: e.target.value})} onBlur={this.checkPassword} required />
                                            </div>
                                        </div>
                                    </div>
                                }
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">VNU Email (*)</label>
                                    <div class="col-sm-9">
                                        <input type="email" class="form-control" value={current_faculty.vnuMail || '@vnu.edu.vn'} onChange={(e) => this.setState({current_faculty: {...current_faculty, vnuMail: e.target.value}})} required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Tên viết tắt</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value={current_faculty.shortName || ''} onChange={(e) => this.setState({current_faculty: {...current_faculty, shortName: e.target.value}})} />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Địa chỉ</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value={current_faculty.address || ''} onChange={(e) => this.setState({current_faculty: {...current_faculty, address: e.target.value}})} />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Điện thoại</label>
                                    <div class="col-sm-9">
                                        <input type="tel" class="form-control" value={current_faculty.phone || ''} onChange={(e) => this.setState({current_faculty: {...current_faculty, phone: e.target.value}})} />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Website</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value={current_faculty.website || ''} onChange={(e) => this.setState({current_faculty: {...current_faculty, website: e.target.value}})} />
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary btn-margin">
                                    Đồng ý
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
