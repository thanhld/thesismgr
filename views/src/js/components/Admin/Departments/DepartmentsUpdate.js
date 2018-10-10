import React, { Component } from 'react'
import { notify } from 'Components'
import { departmentTypeToName } from 'Helper'

class AdminDepartmentsUpdate extends Component {
    constructor() {
        super()
        this.state = {
            error: false,
            message: '',
            current_department: {}
        }

        this.handleSubmit = this.handleSubmit.bind(this);
        this.reloadData = this.reloadData.bind(this);
    }

    componentWillReceiveProps(nextProps) {
        this.setState({
            error: false,
            current_department : nextProps.department
        })
    }

    reloadData() {
        const { facultyId, actions, modalId } = this.props
        actions.loadDepartmentOfFaculty(facultyId).then(() => {
            $(`#${modalId}`).modal('hide')
        })
    }

    handleSubmit(departmentId, e) {
        e.preventDefault()

        const { action, actions, addLastestRow } = this.props;
        const { current_department } = this.state;
        if (action == "create") {
            actions.createDepartment(current_department).then(response => {
                // const { id } = response.action.payload.data;
                // addLastestRow(id);
                this.reloadData();
                notify.show(`Đã thêm mới đơn vị ${current_department.name}`, 'primary')
            }).catch(err => {
                this.setState({
                    error: true,
                    message: err.response.data.message
                })
            })
        } else if (action == "update") {
            actions.updateDepartment(current_department).then(response => {
                if (departmentId) {
                    addLastestRow(departmentId);
                    this.reloadData();
                    notify.show(`Đã cập nhật đơn vị ${current_department.name}`, 'primary')
                }
            }).catch(err => {
                console.log(err);
                this.setState({
                    error: true,
                    message: err.response.data.message
                })
            })
        }
    }

    render() {
        const { modalId, action, department } = this.props
        const { current_department, error, message } = this.state

        return (
            <div id={`${modalId}`} class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="departmentModal">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="departmentModal">{ action == "create" ? "Thêm đơn vị" : "Cập nhật thông tin đơn vị" }</h4>
                        </div>

                        <form class="form-horizontal" onSubmit={(e) => this.handleSubmit(current_department.id, e)}>
                            <div class="modal-body">
                                { error &&
                                    <div>
                                        <div class="text-message-error">Có lỗi xảy ra: {message}</div>
                                        <br />
                                    </div>
                                }
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Tên Đơn vị (*)</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value={current_department.name || ''} onChange={(e) => this.setState({current_department: {...current_department, name: e.target.value}})} required />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Loại đơn vị (*)</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" value={current_department.type || '' } onChange={e => this.setState({current_department: {...current_department, type: e.target.value}})} required disabled={current_department.type == "4"}>
                                            <option disabled hidden value="">Chọn Loại đơn vị</option>
                                            {current_department.type == 4 && <option value="4">Văn phòng Khoa</option>}
                                            <option value="1">Bộ môn</option>
                                            <option value="2">Phòng thí nghiệm</option>
                                            <option value="3">Đơn vị ngoài</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Địa chỉ</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value={current_department.address || ''} onChange={(e) => this.setState({current_department: {...current_department, address: e.target.value}})} />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Số điện thoại</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value={current_department.phone || ''} onChange={(e) => this.setState({current_department: {...current_department, phone: e.target.value}})} />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Website</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value={current_department.website || ''} onChange={(e) => this.setState({current_department: {...current_department, website: e.target.value}})} />
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

export default AdminDepartmentsUpdate
