import React, { Component } from 'react'
import { notify } from 'Components'
import { departmentTypeToName } from 'Helper'
import AdminDepartmentsUpdate from './DepartmentsUpdate'

class AdminDepartments extends Component {
    constructor(props) {
        super(props);
        this.state = {
            current_department: {},
            current_action: '',
            lastestItem: '',
            filType: ''
        }
        this.addLastestRow = this.addLastestRow.bind(this)
        this.deleteHandler = this.deleteHandler.bind(this);
    }
    componentWillMount() {
        const { facultyId, departments, actions } = this.props
        const { isLoaded } = departments
        if (!isLoaded) actions.loadDepartmentOfFaculty(facultyId)
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
    deleteHandler(department) {
        const val = confirm(`Thầy/cô có chắc chắn muốn xóa ${departmentTypeToName(department.type)} ${department.name}?`)
        if (val) {
            const { actions } = this.props;
            actions.deleteDepartment(department.id).then(() => {
                actions.loadDepartments();
                notify.show(`Đã xóa đơn vị ${department.name}`, 'primary')
            }).catch(err => {
                notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
            })
        }
    }
    render() {
        const { facultyId, departments } = this.props
        const { current_department, current_action, lastestItem, filType } = this.state
        return (
            <div>
                <div class="row">
                    <div class="col-xs-9 page-title">Đơn vị</div>
                    <div class="col-xs-3">
                        <div class="pull-right">
                            <button type="button" class="btn btn-success btn-margin btn-sm" data-toggle="modal" data-target="#updateDepartment"
                                onClick={() => this.setState({current_department: {}, current_action: "create"})}>
                                Thêm mới
                            </button>
                        </div>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="pull-right form-inline">
                        <div class="form-group">
                            <label class="margin-right">Loại Đơn vị</label>
                            <select class="form-control" value={filType} onChange={e => this.setState({filType: e.target.value})}>
                                <option value="">Tất cả</option>
                                <option value="4">Văn phòng Khoa</option>
                                <option value="1">Bộ môn</option>
                                <option value="2">Phòng thí nghiệm</option>
                                <option value="3">Đơn vị ngoài</option>
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
                                <th >Tên đơn vị</th>
                                <th >Loại đơn vị</th>
                                <th >Địa chỉ</th>
                                <th >Điện thoại</th>
                                <th >Website</th>
                                <th >Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            { departments.list.filter(d => filType == '' || d.type == filType).map((department, index) => { return (
                                <tr key={department.id} class={'row-item ' + (lastestItem == department.id && 'lastest-row-item')}>
                                    <td>{index+1}</td>
                                    <td>{department.name}</td>
                                    <td>{departmentTypeToName(department.type)}</td>
                                    <td class="hidden-xs hidden-sm">{department.address}</td>
                                    <td class="hidden-xs">{department.phone}</td>
                                    <td class="hidden-xs hidden-sm">{department.website}</td>
                                    <td>
                                        <button class="btn btn-primary btn-margin btn-xs" data-toggle="modal" data-target="#updateDepartment"
                                            onClick={() => this.setState({current_department: department, current_action: "update"})}>
                                            Sửa
                                        </button>
                                        {department.type != "4" && <button class="btn btn-primary btn-margin btn-xs" onClick={() => this.deleteHandler(department)}>
                                            Xóa
                                        </button>}
                                    </td>
                                </tr>
                            )}) }
                        </tbody>
                    </table>
                </div>

                <AdminDepartmentsUpdate
                    modalId="updateDepartment"
                    action={current_action}
                    actions={this.props.actions}
                    facultyId={facultyId}
                    addLastestRow={this.addLastestRow}
                    department ={current_department}
                />
            </div>
        )
    }
}

export default AdminDepartments
