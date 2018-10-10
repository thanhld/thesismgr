import React, { Component } from 'react'
import { notify } from 'Components'
import DegreeUpdate from './DegreeUpdate'

class SuperuserDegrees extends Component {
    constructor() {
        super()
        this.state = {
            current_degree: {},
            current_action: '',
            lastestItem: ''
        }
        this.handleDelete = this.handleDelete.bind(this)
        this.addLastestRow = this.addLastestRow.bind(this)
    }
    componentWillMount() {
        const { degrees, actions } = this.props
        const { isLoaded } = degrees
        if (!isLoaded) actions.loadDegrees()
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
    handleDelete(degree) {
        const val = confirm(`Thầy/cô có chắc chắn muốn xóa Học hàm, học vị ${degree.name}?`)
        if (val) {
            const { actions } = this.props
            actions.deleteDegree(degree.id).then(() => {
                actions.loadDegrees()
                notify.show(`Đã xóa Học hàm, học vị ${degree.name}`, 'primary')
            }).catch(err => {
                notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
            })
        }
    }
    render() {
        const { degrees, actions } = this.props
        const { current_degree, current_action, lastestItem } = this.state
        return (
            <div>
                <div class="row">
                    <div class="col-xs-9">
                        <div class="page-title">Danh sách Học hàm, học vị</div>
                    </div>
                    <div class="col-xs-3">
                        <div>
                            <div class="pull-right">
                                <button type="button" class="btn btn-success btn-margin btn-sm" data-toggle="modal" data-target="#updateDegree" onClick={() => {this.setState({current_degree: {}, current_action: "create"})}}>
                                    Thêm mới
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <br />
                <div class="col-md-8 col-md-offset-2 table-responsive">
                    <table class="table table-hover table-condensed">
                        <thead>
                            <tr>
                                <th class="col-xs-9">Tên Học hàm, học vị</th>
                                <th class="col-xs-3">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            { degrees.list && degrees.list.map(degree => { return (
                                <tr key={degree.id} class={'row-item ' + (lastestItem == degree.id && 'lastest-row-item')}>
                                    <td class="col-xs-9">{degree.name}</td>
                                    <td class="col-xs-3">
                                        <button class="btn btn-primary btn-margin btn-xs" data-toggle="modal" data-target="#updateDegree" onClick={() => {this.setState({current_degree: degree, current_action: "update"})}}>Sửa</button>
                                        <button class="btn btn-primary btn-margin btn-xs" onClick={() => this.handleDelete(degree)}>Xóa</button>
                                    </td>
                                </tr>
                            )}) }
                        </tbody>
                    </table>
                </div>
                <DegreeUpdate
                    modalId="updateDegree"
                    addLastestRow={this.addLastestRow}
                    actions={actions}
                    action={current_action}
                    degree={current_degree} />
            </div>
        )
    }
}

export default SuperuserDegrees
