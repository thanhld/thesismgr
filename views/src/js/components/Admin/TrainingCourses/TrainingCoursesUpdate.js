import React, { Component } from 'react'
import { notify } from 'Components'

class AdminTrainingCoursesUpdate extends Component {
    constructor() {
        super()
        this.state = {
            error: false,
            message: '',
            current_course: {}
        }
        this.handleSubmit = this.handleSubmit.bind(this)
        this.reloadData = this.reloadData.bind(this)
    }
    componentWillReceiveProps(nextProps) {
        this.setState({
            error: false,
            current_course: nextProps.course
        })
        if (nextProps.action == "create") {
            this.setState({
                current_course: {...nextProps.course, isCompleted: 0}
            })
        }
    }
    reloadData() {
        const { actions, modalId } = this.props
        actions.loadTrainingCourses().then(() => {
            $(`#${modalId}`).modal('hide')
        })
    }
    handleSubmit(e) {
        e.preventDefault()
        const { action, actions, addLastestRow } = this.props
        const { current_course } = this.state
        if (action == "create") {
            actions.createTrainingCourse(current_course).then(response => {
                const { id } = response.action.payload.data
                addLastestRow(id)
                this.reloadData()
                notify.show(`Đã thêm mới khóa đào tạo ${current_course.courseName}`, 'primary')
            }).catch(err => {
                this.setState({
                    error: true,
                    message: err.response.data.message
                })
            })
        } else if (action == "update") {
            actions.updateTrainingCourse(current_course).then(() => {
                addLastestRow(current_course.id)
                this.reloadData()
                notify.show(`Đã cập nhật khóa đào tạo ${current_course.courseName}`, 'primary')
            }).catch(err => {
                this.setState({
                    error: true,
                    message: err.response.data.message
                })
            })
        }
    }
    render() {
        const { modalId, action, course, programs } = this.props
        const { current_course, error, message } = this.state
        return (
            <div id={`${modalId}`} class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="courseModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="courseModal">{ action == "create" ? "Thêm khóa đào tạo" : "Cập nhật khóa đào tạo" }</h4>
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
                                    <label class="col-sm-5 control-label">Năm tuyển sinh (*)</label>
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" min="2000" max="2100" value={current_course.admissionYear || ''} onChange={(e) => this.setState({current_course: {...current_course, admissionYear: e.target.value}})} required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-5 control-label">Chương trình đào tạo (*)</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" value={current_course.trainingProgramId || ''} onChange={(e) => this.setState({current_course: {...current_course, trainingProgramId: e.target.value}})} required>
                                            <option value="" disabled hidden>Chọn Chương trình đào tạo</option>
                                            { programs.list && programs.list.map(obj => <option value={obj.id} key={obj.id}>{obj.name}</option>) }
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-5 control-label">Tên khóa đào tạo (*)</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" value={current_course.courseName || ''} onChange={(e) => this.setState({current_course: {...current_course, courseName: e.target.value}})} required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-5 control-label">Mã khóa đào tạo (*)</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" value={current_course.courseCode || ''} onChange={(e) => this.setState({current_course: {...current_course, courseCode: e.target.value}})} required />
                                    </div>
                                </div>
                                { action == 'create' &&
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">Đang đào tạo? (*)</label>
                                        <div class="col-sm-6">
                                            <select class="form-control" defaultValue='0' required disabled>
                                                <option value="0">Có</option>
                                                <option value="1">Không</option>
                                            </select>
                                        </div>
                                    </div> }
                                { action == 'update' &&
                                    <div class="form-group">
                                        <label class="col-sm-5 control-label">Đang đào tạo? (*)</label>
                                        <div class="col-sm-6">
                                            <select class="form-control" value={current_course.isCompleted || '0'} onChange={e => this.setState({current_course: {...current_course, isCompleted: e.target.value}})} required >
                                                <option value="0">Có</option>
                                                <option value="1">Không</option>
                                            </select>
                                        </div>
                                    </div> }
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

export default AdminTrainingCoursesUpdate
