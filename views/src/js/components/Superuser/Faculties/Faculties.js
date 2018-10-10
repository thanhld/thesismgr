import React, { Component } from 'react'
import { notify } from 'Components'
import FacultyUpdate from './FacultyUpdate'

class SuperuserFaculties extends Component {
    constructor() {
        super()
        this.state = {
            current_faculty: {},
            current_action: '',
            lastestItem: ''
        }
        this.handleDelete = this.handleDelete.bind(this)
        this.addLastestRow = this.addLastestRow.bind(this)
    }
    componentWillMount() {
        const { faculties, actions } = this.props
        const { isLoaded } = faculties
        if (!isLoaded) actions.loadFaculties()
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
    handleDelete(faculty) {
        const val = confirm(`Thầy/cô có chắc chắn muốn xóa Khoa ${faculty.name}?`)
        if (val) {
            const { actions } = this.props
            actions.deleteFaculty(faculty.id).then(() => {
                actions.loadFaculties()
                notify.show(`Đã xóa Khoa ${faculty.name}`, 'primary')
            }).catch(err => {
                notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
            })
        }
    }
    render() {
        const { faculties, actions } = this.props
        const { current_faculty, current_action, lastestItem } = this.state
        return (
            <div>
                <div class="row">
                    <div class="col-xs-9">
                        <div class="page-title">Danh sách khoa</div>
                    </div>
                    <div class="col-xs-3">
                        <div>
                            <div class="pull-right">
                                <button type="button" class="btn btn-success btn-margin btn-sm" data-toggle="modal" data-target="#updateFaculty" onClick={() => {this.setState({current_faculty: {}, current_action: "create"})}}>
                                    Thêm mới
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <br />
                <div class="table-responsive">
                    <table class="table table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="col-xs-3 col-sm-3 col-md-2">Tên khoa</th>
                                <th class="col-xs-2 col-sm-2 col-md-1">Tài khoản</th>
                                <th class="hidden-xs hidden-sm col-md-1">Tên viết tắt</th>
                                <th class="hidden-xs hidden-sm">VNU Email</th>
                                <th class="hidden-xs hidden-sm col-md-3">Địa chỉ</th>
                                <th class="hidden-xs hidden-sm col-md-1">Điện thoại</th>
                                <th class="hidden-xs hidden-sm">Website</th>
                                <th class="col-xs-2 col-sm-2 col-md-1">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            { faculties.list && faculties.list.map(faculty => { return (
                                <tr key={faculty.id} class={'row-item ' + (lastestItem == faculty.id && 'lastest-row-item')}>
                                    <td>{faculty.name}</td>
                                    <td>{faculty.username}</td>
                                    <td class="hidden-xs hidden-sm">{faculty.shortName}</td>
                                    <td class="hidden-xs hidden-sm">{faculty.vnuMail}</td>
                                    <td class="hidden-xs hidden-sm">{faculty.address}</td>
                                    <td class="hidden-xs hidden-sm">{faculty.phone}</td>
                                    <td class="hidden-xs hidden-sm">{faculty.website}</td>
                                    <td>
                                        <button class="btn btn-primary btn-margin btn-xs" data-toggle="modal" data-target="#updateFaculty" onClick={() => {this.setState({current_faculty: faculty, current_action: "update"})}}>Sửa</button>
                                        <button class="btn btn-primary btn-margin btn-xs" onClick={() => this.handleDelete(faculty)}>Xóa</button>
                                    </td>
                                </tr>
                            )}) }
                        </tbody>
                    </table>
                </div>
                <FacultyUpdate
                    modalId="updateFaculty"
                    addLastestRow={this.addLastestRow}
                    actions={actions}
                    action={current_action}
                    faculty={current_faculty} />
            </div>
        )
    }
}

export default SuperuserFaculties
