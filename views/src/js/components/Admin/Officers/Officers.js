import React, { Component } from 'react'
import Pagination from 'react-js-pagination'
import { ITEM_PER_PAGE, PAGE_RANGE } from 'Config'
import { notify } from 'Components'
import AdminOfficersUpdate from './OfficersUpdate'
import AdminImportOfficerExcel from './ImportOfficer'

class AdminOfficers extends Component {
    constructor() {
        super()
        this.state = {
            current_officer: {},
            current_action: '',
            lastestItem: '',
            filRole: '',
            filDegree: '',
            filDepartment: '',
            activePage: 1
        }

        this.handleRemove = this.handleRemove.bind(this)
        this.addLastestRow = this.addLastestRow.bind(this)
    }
    componentWillMount() {
        const { auth, officers, degrees, departments, actions } = this.props
        if (!degrees.isLoaded) actions.loadDegrees()
        if (!departments.isLoaded) actions.loadDepartmentOfFaculty(auth.user.facultyId)
        if (!officers.isLoaded) actions.loadOfficers()
    }
    handlePageChange = pageNum => {
        this.setState({
            activePage: pageNum
        })
    }
    addLastestRow(uid) {
        if (!uid) return
        this.setState({
            lastestItem: uid
        })
        setTimeout(() => {
            this.setState({
                lastestItem: ''
            })
        }, 1000)
    }
    roleIdToName(id) {
        switch (id) {
            case 3:
                return 'Giảng viên'
            case 4:
                return 'Chuyên viên'
            case 5:
                return 'Cán bộ ngoài'
            case 6:
                return 'Trưởng bộ môn, PTN'
            default:
                return ''
        }
    }
    handleRemove(officer) {
        const val = confirm(`Thầy/cô có chắc chắn muốn xóa Cán bộ ${officer.fullname}?`)
        if (val) {
            const { actions } = this.props
            actions.deleteOfficer(officer).then(() => {
                actions.loadOfficers()
                notify.show(`Đã xóa Cán bộ ${officer.fullname}`, 'primary')
            }).catch(err => {
                notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
            })
        }
    }
    filterOfficers = o => {
        const { filRole, filDegree, filDepartment } = this.state
        if (filRole) {
            if (filRole != 3 && filRole != o.role)
                return false
            if (filRole == 3 && o.role != 3 && o.role != 6)
                return false
        }
        if (filDegree && filDegree != o.degreeId) return false
        if (filDepartment && filDepartment != o.departmentId) return false
        return true
    }
    render() {
        const { officers, departments, degrees, actions } = this.props
        const { activePage, current_officer, current_action, lastestItem, filRole, filDegree, filDepartment } = this.state
        const itemLength = officers.list.filter(this.filterOfficers).length
        return (
            <div>
                <div class="row">
                    <div class="col-xs-9 page-title">Cán bộ</div>
                    <div class="col-xs-3">
                        <div class="pull-right">
                            <button type="button" class="btn btn-success btn-margin btn-sm" data-toggle="modal" data-target="#updateOfficer" onClick={() => {this.setState({current_officer: {}, current_action: "create"})}}>
                                Thêm mới
                            </button>
                            <button type="button" class="btn btn-success btn-margin btn-sm" data-toggle="modal" data-target="#importOfficerExcel">
                                Thêm từ Excel
                            </button>
                        </div>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="pull-right form-inline">
                        <div class="form-group">
                            <label class="margin-right">Loại Cán bộ</label>
                            <select class="form-control large-right" value={filRole} onChange={e => this.setState({filRole: e.target.value, activePage: 1})}>
                                <option value="">Tất cả</option>
                                <option value="3">Giảng viên</option>
                                <option value="4">Chuyên viên</option>
                                <option value="5">Cán bộ ngoài</option>
                                <option value="6">Trưởng bộ môn, PTN</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="margin-right">Học hàm, học vị</label>
                            <select class="form-control large-right" value={filDegree} onChange={e => this.setState({filDegree: e.target.value, activePage: 1})}>
                                <option value="">Tất cả</option>
                                { degrees.list && degrees.list.map(obj => <option key={obj.id} value={obj.id}>{obj.name}</option>) }
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="margin-right">Đơn vị</label>
                            <select class="form-control" value={filDepartment} onChange={e => this.setState({filDepartment: e.target.value, activePage: 1})}>
                                <option value="">Tất cả</option>
                                { departments.list && departments.list.map(obj => <option key={obj.id} value={obj.id}>{obj.name}</option>) }
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
                                <th >Mã CB</th>
                                <th >Họ và tên</th>
                                <th >Tài khoản</th>
                                <th >VNU email</th>
                                <th >Loại CB</th>
                                <th >Học vị</th>
                                <th >Đơn vị công tác</th>
                                <th >Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            { officers.list && officers.list.filter(this.filterOfficers).slice(ITEM_PER_PAGE * (activePage - 1), ITEM_PER_PAGE * activePage).map((officer, index) => {
                                let degree = degrees.list && degrees.list.find(obj => obj.id == officer.degreeId)
                                let department = departments.list && departments.list.find(obj => obj.id == officer.departmentId)
                                return (
                                    <tr key={officer.id} class={ 'row-item ' + (lastestItem == officer.id && 'lastest-row-item')}>
                                        <td>{ITEM_PER_PAGE * (activePage - 1) + index+1}</td>
                                        <td>{officer.officerCode}</td>
                                        <td>{officer.fullname}</td>
                                        <td class="hidden-xs">{officer.username}</td>
                                        <td class="hidden-xs">{officer.vnuMail}</td>
                                        <td class="hidden-xs hidden-sm col-md-1">{this.roleIdToName(officer.role)}</td>
                                        <td class="hidden-xs hidden-sm">{degree && degree.name}</td>
                                        <td class="hidden-sm">{department && department.name}</td>
                                        <td class="col-md-1">
                                            <button class="btn btn-primary btn-margin btn-xs" data-toggle="modal" data-target="#updateOfficer" onClick={() => {this.setState({current_officer: officer, current_action: "update"})}}>Sửa</button>
                                            <button class="btn btn-primary btn-xs" onClick={() => this.handleRemove(officer)}>Xóa</button>
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
                <AdminOfficersUpdate
                    modalId="updateOfficer"
                    action={current_action}
                    actions={actions}
                    addLastestRow={this.addLastestRow}
                    auth={this.props.auth}
                    officer={current_officer}
                    degrees={degrees}
                    departments={departments}
                />
                <AdminImportOfficerExcel
                    modalId="importOfficerExcel"
                    actions={this.props.actions}
                    degrees={degrees}
                    departments={departments}
                />
            </div>
        )
    }
}

export default AdminOfficers
