import React, { Component } from 'react'
import { notify } from 'Components'
import AdminTrainingAreasUpdate from './TrainingAreasUpdate'

class AdminTrainingAreas extends Component {
    constructor(props) {
        super(props);
        this.state = {
            current_area: {},
            current_action: '',
            lastestItem: ''
        }
        this.addLastestRow = this.addLastestRow.bind(this);
        this.deleteHandler = this.deleteHandler.bind(this);
    }
    componentWillMount() {
        const { areas, actions } = this.props
        const { isLoaded } = areas
        if (!isLoaded) actions.loadTrainingAreas();
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
    deleteHandler(area) {
        const val = confirm(`Thầy/cô có chắc chắn muốn xóa ngành đào tạo ${area.name}?`)
        if (val) {
            const { actions } = this.props;
            actions.deleteTrainingArea(area.id).then(() => {
                actions.loadTrainingAreas();
                notify.show(`Đã xóa Ngành đào tạo ${area.name}`, 'primary')
            }).catch(err => {
                notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
            })
        }
    }
    render() {
        const { areas } = this.props;
        const { current_area, current_action, lastestItem } = this.state

        return (
            <div>
                <div class="row">
                    <div class="col-xs-9 page-title">Ngành đào tạo</div>
                    <div class="col-xs-3">
                        <div class="pull-right">
                            <button type="button" class="btn btn-success btn-margin btn-sm" data-toggle="modal" data-target="#updateTrainingAreas"
                                onClick={() => this.setState({current_area: {}, current_action: "create"})}>
                                Thêm mới
                            </button>
                        </div>
                    </div>
                </div>
                <br />
                <div class="col-md-10 col-md-offset-1 table-responsive">
                    <table class="table table-hover table-condensed">
                        <thead>
                            <tr>
                                <th>TT</th>
                                <th>Mã ngành đào tạo</th>
                                <th>Tên ngành đào tạo</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            { areas.list.map((area, index) => { return (
                                <tr key={area.id} class={'row-item ' + (lastestItem == area.id && 'lastest-row-item')}>
                                    <td>{index+1}</td>
                                    <td>{area.areaCode}</td>
                                    <td>{area.name}</td>
                                    <td>
                                        <button class="btn btn-primary btn-margin btn-xs" data-toggle="modal" data-target="#updateTrainingAreas"
                                            onClick={() => this.setState({current_area: area, current_action: "update"})}>
                                            Sửa
                                        </button>
                                        <button class="btn btn-primary btn-margin btn-xs" onClick={() => this.deleteHandler(area)}>
                                            Xóa
                                        </button>
                                    </td>
                                </tr>
                            )}) }
                        </tbody>
                    </table>
                </div>

                <AdminTrainingAreasUpdate
                    modalId="updateTrainingAreas"
                    action={current_action}
                    actions={this.props.actions}
                    addLastestRow={this.addLastestRow}
                    area={current_area}
                />
            </div>
        )
    }
}

export default AdminTrainingAreas
