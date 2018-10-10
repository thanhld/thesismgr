import React, { Component } from 'react'
import { notify } from 'Components'
import { getCurrentYear } from 'Helper'
import AdminTrainingCoursesUpdate from './TrainingCoursesUpdate'

class AdminTrainingCourses extends Component {
    constructor(props) {
        super(props);
        this.state = {
            current_course: {},
            current_action: '',
            lastestItem: '',
            filProgram: '',
            filYear: '',
            filInUse: ''
        }
        this.addLastestRow = this.addLastestRow.bind(this);
        this.deleteHandler = this.deleteHandler.bind(this);
    }
    componentWillMount() {
        const { courses, programs, actions } = this.props
        if (!programs.isLoaded) actions.loadTrainingPrograms()
        if (!courses.isLoaded) actions.loadTrainingCourses()
    }
    addLastestRow(id) {
        this.setState({
            lastestItem: id
        })
        setTimeout(() => {
            this.setState({
                lastestItem: ''
            })
        }, 1000)
    }
    deleteHandler(course) {
        const val = confirm(`Thầy/cô có chắc chắn muốn xóa khóa đào tạo ${course.courseName}?`)
        if (val) {
            const { actions } = this.props;
            actions.deleteTrainingCourse(course.id).then(() => {
                actions.loadTrainingCourses();
                notify.show(`Đã xóa Khóa đào tạo ${course.courseName}`, 'primary')
            })
        }
    }
    filterCourses = c => {
        const { filProgram, filYear, filInUse } = this.state
        if (filProgram && filProgram != c.trainingProgramId) return false
        if (filYear && filYear != c.admissionYear) return false
        if (filInUse && filInUse != c.isCompleted) return false
        return true
    }
    render() {
        const { courses, programs } = this.props;
        const { current_course, current_action, lastestItem, filProgram, filYear, filInUse } = this.state
        const yearOption = []
        for (let i = getCurrentYear(); i >= 2010; i--) yearOption.push(<option key={i} value={i}>{i}</option>)
        return (
            <div>
                <div class="row">
                    <div class="col-xs-9 page-title">Khóa đào tạo</div>
                    <div class="col-xs-3">
                        <div class="pull-right">
                            <button type="button" class="btn btn-success btn-margin btn-sm" data-toggle="modal" data-target="#updateTrainingCourses"
                                onClick={() => this.setState({current_course: {}, current_action: "create"})}>
                                Thêm mới
                            </button>
                        </div>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="pull-right form-inline">
                        <div class="form-group">
                            <label class="margin-right">Chương trình đào tạo</label>
                            <select class="form-control large-right" value={filProgram} onChange={e => this.setState({filProgram: e.target.value})}>
                                <option value="">Tất cả</option>
                                { programs.list && programs.list.map(obj => <option key={obj.id} value={obj.id}>{obj.name}</option>) }
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="margin-right">Năm tuyển sinh</label>
                            <select class="form-control large-right" value={filYear} onChange={e => this.setState({filYear: e.target.value})}>
                                <option value="">Tất cả</option>
                                { yearOption }
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="margin-right">Đang đào tạo?</label>
                            <select class="form-control" value={filInUse} onChange={e => this.setState({filInUse: e.target.value})}>
                                <option value="">Tất cả</option>
                                <option value="0">Có</option>
                                <option value="1">Không</option>
                            </select>
                        </div>
                    </div>
                </div>
                <br />
                <div class="table-responsive">
                    <table class="table table-hover table-condensed">
                        <thead>
                            <tr>
                                <th>TT</th>
                                <th >Mã khóa đào tạo</th>
                                <th >Tên khóa đào tạo</th>
                                <th >Mã CTĐT</th>
                                <th >Năm tuyển sinh</th>
                                <th >Năm kết thúc</th>
                                <th >Đang đào tạo?</th>
                                <th >Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            { courses.list.filter(this.filterCourses).map((course, index) => {
                                let program = programs.list && programs.list.find(obj => obj.id == course.trainingProgramId)
                                return (
                                    <tr key={course.id} class={'row-item ' + (lastestItem == course.id && 'lastest-row-item')}>
                                        <td>{index+1}</td>
                                        <td>{course.courseCode}</td>
                                        <td>{course.courseName}</td>
                                        <td class="hidden-xs">{program && program.name}</td>
                                        <td class="text-center">{course.admissionYear}</td>
                                        <td class="text-center hidden-xs">{parseInt(course.admissionYear) + Math.round(program ? program.trainingDuration : 0)}</td>
                                        <td class="text-center">{course.isCompleted == "1" ? <i class="fa fa-close text-danger" aria-hidden="true"></i> : <i class="fa fa-check text-success" aria-hidden="true"></i>}</td>
                                        <td>
                                            <button class="btn btn-primary btn-margin btn-xs" data-toggle="modal" data-target="#updateTrainingCourses"
                                                onClick={() => this.setState({current_course: course, current_action: "update"})}>
                                                Sửa
                                            </button>
                                            <button class="btn btn-primary btn-margin btn-xs" onClick={() => this.deleteHandler(course)}>
                                                Xóa
                                            </button>
                                        </td>
                                    </tr>
                            )}) }
                        </tbody>
                    </table>
                </div>

                <AdminTrainingCoursesUpdate
                    modalId="updateTrainingCourses"
                    action={current_action}
                    actions={this.props.actions}
                    addLastestRow={this.addLastestRow}
                    course={current_course}
                    programs={programs}
                />
            </div>
        )
    }
}

export default AdminTrainingCourses
