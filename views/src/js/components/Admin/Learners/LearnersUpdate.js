import React, { Component } from 'react'
import { notify } from 'Components'
import { mailerActions } from 'Actions';

class AdminLearnersUpdate extends Component {
    constructor() {
        super()
        this.state = {
            error: false,
            message: '',
            current_learner: {}
        }

        this.handleSubmit = this.handleSubmit.bind(this);
        this.reloadData = this.reloadData.bind(this);
    }
    componentWillReceiveProps(nextProps) {
        this.setState({
            error: false,
            current_learner : nextProps.learner
        })
    }
    componentDidMount() {
        this.setFormValidation();
    }
    setFormValidation() {
        $("#createLearnerForm").validate({
            rules: {
                learn_username: "required",
                learner_password: "required",
                fullname: "required",
                learnercode: "required",
                vnumail: "required",
                trainingcourse: "required"
            },
            messages: {
                username: "Tên tài khoản không được để trống",
                password: "Mật khẩu không được để trống",
                fullname: "Tên học viên không được để trống",
                learnercode: "Mã học viên không được để trống",
                vnumail: "Địa chỉ mail không được để trống",
                trainingcourse: "Khóa đào tạo không được để trống"
            }
        });
    }
    componentWillReceiveProps(nextProps) {
        this.setState({
            error: false,
            current_learner : nextProps.learner
        })
    }
    reloadData() {
        const { actions, modalId } = this.props
        actions.loadLearners().then(() => {
            $(`#${modalId}`).modal('hide')
        })
    }
    handleSubmit(learnerId, e) {
        e.preventDefault();
        var valid = $("#createLearnerForm").valid();
        if (valid) {
            const { action, actions, addLastestRow } = this.props;
            const { current_learner } = this.state;
            if (action == "create") {
                actions.createLearner(current_learner).then(response => {
                    if (response.value.data[0].error !== undefined) {
                        this.setState({
                            error: true,
                            message: response.value.data[0].error
                        })
                    } else {
                        const { uid } = response.action.payload.data[0];
                        addLastestRow(uid)
                        mailerActions.setPasswordMail();
                        this.reloadData();
                        notify.show(`Đã thêm mới học viên ${current_learner.fullname}`, 'primary')
                    }
                    
                }).catch(err => {
                    this.setState({
                        error: true,
                        message: err.response.data.message
                    })
                })
            } else if (action == "update") {
                actions.updateLearner(current_learner).then(response => {
                    if (learnerId) {
                        addLastestRow(learnerId);
                        this.reloadData();
                        notify.show(`Đã cập nhật học viên ${current_learner.fullname}`, 'primary')
                    }
                }).catch(err => {
                    this.setState({
                        error: true,
                        message: err.response.data.message
                    })
                })
            }
        }
    }

    render() {
        const { modalId, action, learner, courses } = this.props
        const { current_learner, error, message } = this.state

        return (
            <div id={`${modalId}`} class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="learnerModal">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="learnerModal">{ action == "create" ? "Thêm học viên" : "Cập nhật thông tin học viên" }</h4>
                        </div>

                        <form id="createLearnerForm" class="form-horizontal create-learner-form" onSubmit={(e) => this.handleSubmit(current_learner.id, e)} autoComplete="off">
                            <div class="modal-body">
                                { error &&
                                    <div>
                                        <div class="text-message-error">Có lỗi xảy ra: {message}</div>
                                        <br />
                                    </div> }
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Tên tài khoản (*)</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="learner_username" class="form-control" value={current_learner.username || ''} onChange={(e) => this.setState({current_learner: {...current_learner, username: e.target.value}})} required />
                                    </div>
                                </div>
                                { action === 'create' &&
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Mật khẩu (*)</label>
                                        <div class="col-sm-9">
                                            <input type="password" name="learner_password" class="form-control" value={current_learner.password || ''} onChange={(e) => this.setState({current_learner: {...current_learner, password: e.target.value}})} required />
                                        </div>
                                    </div> }
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Tên học viên (*)</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="fullname" class="form-control" value={current_learner.fullname || ''} onChange={(e) => this.setState({current_learner: {...current_learner, fullname: e.target.value}})} required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Mã học viên (*)</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="learnercode" class="form-control" value={current_learner.learnerCode || ''} onChange={(e) => this.setState({current_learner: {...current_learner, learnerCode: e.target.value}})} required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Khóa đào tạo (*)</label>
                                    <div class="col-sm-9">
                                        <select name="trainingcourse" class="form-control" value={current_learner.trainingCourseId || ""} onChange={e => this.setState({current_learner: {...current_learner, trainingCourseId: e.target.value}})} required>
                                            <option disabled hidden value="">Chọn Khóa đào tạo</option>
                                            { courses.list && courses.list.map(obj => <option key={obj.id} value={obj.id}>{obj.courseCode}</option>) }
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">VNU email (*)</label>
                                    <div class="col-sm-9">
                                        <input type="email" name="vnumail" class="form-control" value={current_learner.vnuMail || '@vnu.edu.vn'} onChange={(e) => this.setState({current_learner: {...current_learner, vnuMail: e.target.value}})} required />
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">{action == "create" ? "Tạo mới" : "Cập nhật"} </button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Bỏ qua</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        )
    }
}

export default AdminLearnersUpdate
