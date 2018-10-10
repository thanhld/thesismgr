import React, { Component } from 'react'
import Pagination from 'react-js-pagination'
import { notify } from 'Components'
import { ITEM_PER_PAGE, PAGE_RANGE } from 'Config'
import AdminLearnersUpdate from './LearnersUpdate'
import AdminImportLearnerExcel from './ImportLearner'

class AdminLearners extends Component {
    constructor(props) {
        super(props);
        this.state = {
            current_learner: {},
            current_action: '',
            lastestItem: '',
            filCourse: '',
            activePage: 1
        }
        this.addLastestRow = this.addLastestRow.bind(this);
        this.deleteHandler = this.deleteHandler.bind(this);
    }
    componentWillMount() {
        const { activePage, filCourse } = this.state
        const { learners, courses, actions } = this.props
        if (!courses.isLoaded) actions.loadTrainingCourses()
        if (!learners.isLoaded) actions.loadLearners(activePage, ITEM_PER_PAGE, filCourse)
    }
    addLastestRow(uid) {
        this.setState({
            lastestItem: uid
        })
        setTimeout(() => {
            this.setState({
                lastestItem: ''
            })
        }, 1000)
    }
    deleteHandler(learner) {
        const val = confirm(`Thầy/cô có chắc chắn muốn xóa học viên ${learner.fullname}?`)
        if (val) {
            const { actions } = this.props;
            actions.deleteLearner(learner.id).then(() => {
                actions.loadLearners();
                notify.show(`Đã xóa Học viên ${learner.fullname}`, 'primary')
            })
        }
    }
    handleChangeCourse = e => {
        this.setState({
            filCourse: e.target.value,
            activePage: 1
        }, () => {
            const { activePage, filCourse } = this.state
            const { actions } = this.props
            actions.loadLearners(activePage, ITEM_PER_PAGE, filCourse)
        })
    }
    handlePageChange = pageNum => {
        this.setState({
            activePage: pageNum
        }, () => {
            const { activePage, filCourse } = this.state
            const { actions } = this.props
            actions.loadLearners(activePage, ITEM_PER_PAGE, filCourse)
        })
    }
    render() {
        const { learners, courses } = this.props
        const { activePage, current_learner, current_action, lastestItem, filCourse } = this.state
        const itemLength = learners.count
        return (
            <div>
                <div class="row">
                    <div class="col-xs-9 page-title">Học viên</div>
                    <div class="col-xs-3">
                        <div class="pull-right">
                            <button type="button" class="btn btn-success btn-margin btn-sm" data-toggle="modal" data-target="#updateLearner"
                                onClick={() => this.setState({current_learner: {}, current_action: "create"})}>
                                Thêm mới
                            </button>
                            <button type="button" class="btn btn-success btn-margin btn-sm" data-toggle="modal" data-target="#importLearnerExcel">
                                Thêm từ Excel
                            </button>
                        </div>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="pull-right form-inline">
                        <div class="form-group">
                            <label class="margin-right">Khóa đào tạo</label>
                            <select class="form-control large-right" value={filCourse} onChange={this.handleChangeCourse}>
                                <option value="">Tất cả</option>
                                { courses.list && courses.list.map(obj => <option key={obj.id} value={obj.id}>{obj.courseCode}</option>)}
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
                                <th >Mã học viên</th>
                                <th >Tên học viên</th>
                                <th >Tài khoản</th>
                                <th >Khóa đào tạo</th>
                                <th >VNU email</th>
                                <th >Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            { learners && learners.list.map((learner, index) => {
                                let course = courses.list && courses.list.find(obj => obj.id == learner.trainingCourseId)
                                return (
                                    <tr key={learner.id} class={ 'row-item ' + (lastestItem == learner.id && 'lastest-row-item')}>
                                        <td>{ITEM_PER_PAGE * (activePage - 1) + index+1}</td>
                                        <td>{learner.learnerCode}</td>
                                        <td>{learner.fullname}</td>
                                        <td class="hidden-xs">{learner.username}</td>
                                        <td class="hidden-xs hidden-sm">{course && course.courseCode}</td>
                                        <td class="hidden-xs hidden-sm">{learner.vnuMail}</td>
                                        <td>
                                            <button class="btn btn-primary btn-margin btn-xs" data-toggle="modal" data-target="#updateLearner"
                                                onClick={() => this.setState({current_learner: learner, current_action: "update"})}>
                                                Sửa
                                            </button>
                                            <button class="btn btn-primary btn-xs" onClick={() => this.deleteHandler(learner)}>Xóa</button>
                                        </td>
                                    </tr>
                            )}) }
                        </tbody>
                    </table>
                </div>
                <div class="text-center">
                    <Pagination
                        activePage={activePage}
                        itemsCountPerPage={ITEM_PER_PAGE}
                        totalItemsCount={itemLength}
                        pageRangeDisplayed={PAGE_RANGE}
                        onChange={this.handlePageChange}
                        />
                </div>
                <AdminLearnersUpdate
                    modalId="updateLearner"
                    action={current_action}
                    addLastestRow={this.addLastestRow}
                    actions={this.props.actions}
                    learner={current_learner}
                    courses={courses}
                />
                <AdminImportLearnerExcel
                    modalId="importLearnerExcel"
                    actions={this.props.actions}
                    courses={courses}
                />
            </div>
        )
    }
}

export default AdminLearners
