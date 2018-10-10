import React, { Component } from 'react'
import { notify } from 'Components'
import AdminTrainingProgramsUpdate from './TrainingProgramsUpdate'

class AdminTrainingPrograms extends Component {
    constructor(props) {
        super(props);
        this.state = {
            current_program: {},
            current_action: '',
            lastestItem: '',
            filArea: '',
            filType: '',
            filLevel: '',
            filDepartment: ''
        }
        this.addLastestRow = this.addLastestRow.bind(this);
        this.deleteHandler = this.deleteHandler.bind(this);
    }
    componentWillMount() {
        const { facultyId, programs, areas, types, levels, departments, actions } = this.props
        if (!departments.isLoaded) actions.loadDepartmentOfFaculty(facultyId)
        if (!areas.isLoaded) actions.loadTrainingAreas()
        if (!types.isLoaded) actions.loadTrainingTypes()
        if (!levels.isLoaded) actions.loadTrainingLevels()
        if (!programs.isLoaded) actions.loadTrainingPrograms()
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
    deleteHandler(program) {
        const val = confirm(`Thầy/cô có chắc chắn muốn xóa chương trình đào tạo ${program.name}?`)
        if (val) {
            const { actions } = this.props;
            actions.deleteTrainingProgram(program.id).then(() => {
                actions.loadTrainingPrograms();
                notify.show(`Đã xóa Chương trình đào tạo ${program.name}`, 'primary')
            })
        }
    }
    filterTrainingPrograms = t => {
        const { filArea, filType, filLevel, filDepartment } = this.state
        if (filArea && filArea != t.trainingAreasId) return false
        if (filType && filType != t.trainingTypesId) return false
        if (filLevel && filLevel != t.trainingLevelsId) return false
        if (filDepartment && filDepartment != t.departmentId) return false
        return true
    }
    render() {
        const { programs, areas, types, levels, departments } = this.props;
        const { current_program, current_action, lastestItem, filArea, filType, filLevel, filDepartment } = this.state

        return (
            <div>
                <div class="row">
                    <div class="col-xs-9 page-title">Chương trình đào tạo</div>
                    <div class="col-xs-3">
                        <div class="pull-right">
                            <button type="button" class="btn btn-success btn-margin btn-sm" data-toggle="modal" data-target="#updateTrainingProgram" onClick={() => {this.setState({current_program: {}, current_action: "create"})}}>
                                Thêm mới
                            </button>
                        </div>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="pull-right">
                        <div class="form-inline program-filter">
                        {/*    <div class="form-group">
                                <label class="margin-right">Ngành ĐT</label>
                                <select class="form-control large-right" value={filArea} onChange={e => this.setState({filArea: e.target.value})}>
                                    <option value="">Tất cả</option>
                                    { areas.list && areas.list.map(obj => <option key={obj.id} value={obj.id}>{obj.name}</option>) }
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="margin-right">Hệ ĐT</label>
                                <select class="form-control large-right" value={filType} onChange={e => this.setState({filType: e.target.value})}>
                                    <option value="">Tất cả</option>
                                    { types.list && types.list.map(obj => <option key={obj.id} value={obj.id}>{obj.name}</option>) }
                                </select>
                            </div>
                              */}
                            <div class="form-group">
                                <label class="margin-right">Bậc ĐT</label>
                                <select class="form-control large-right" value={filLevel} onChange={e => this.setState({filLevel: e.target.value})}>
                                    <option value="">Tất cả</option>
                                    { levels.list && levels.list.map(obj => <option key={obj.id} value={obj.id}>{obj.name}</option>) }
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="margin-right">Bộ môn phụ trách</label>
                                <select class="form-control large-right" value={filDepartment} onChange={e => this.setState({filDepartment: e.target.value})}>
                                    <option value="">Tất cả</option>
                                    { departments.list && departments.list.map(obj => (obj.type == "1" || obj.type == "4") && <option key={obj.id} value={obj.id}>{obj.name}</option>) }
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <br />
                <div class="table-responsive">
                    <table class="table table-hover table-condensed">
                        <thead>
                            <tr>
                                <th>TT</th>
                                <th >Mã CTĐT</th>
                                <th >Tên CTĐT</th>
                            {/*    <th >Ngành ĐT</th>
                                <th >Hệ ĐT</th>

                                <th >Năm bắt đầu</th>*/}
                                <th >Bậc ĐT</th>
                                <th >Số năm ĐT</th>
                                <th >Bộ môn phụ trách</th>
                                <th >Còn hiệu lực</th>
                                <th>Hệ số KL/LV</th>
                                <th >Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            { programs.list.filter(this.filterTrainingPrograms).map((program, index) => {
                                let area = areas.list.find(obj => obj.id == program.trainingAreasId)
                                let type = types.list.find(obj => obj.id == program.trainingTypesId)
                                let level = levels.list.find(obj => obj.id == program.trainingLevelsId)
                                let department = departments.list.find(obj => obj.id == program.departmentId)
                                return (
                                    <tr key={program.id} class={'row-item ' + (lastestItem == program.id && 'lastest-row-item')}>
                                        <td>{index+1}</td>
                                        <td class="hidden-xs">{program.programCode}</td>
                                        <td>{program.name}</td>
                                    {/*    <td class="hidden-xs">{area && area.name}</td>
                                        <td class="hidden-xs hidden-sm">{type && type.name}</td>

                                        <td class="hidden-xs hidden-sm">{program.startTime}</td>*/}
                                        <td class="hidden-xs hidden-sm">{level && level.name}</td>
                                        <td class="text-center">{program.trainingDuration}</td>
                                        <td class="hidden-xs hidden-sm">{department && department.name}</td>
                                        <td class="text-center">{program.isInUse == 0 ? <i class="fa fa-close text-danger" aria-hidden="true"></i> : <i class="fa fa-check text-success" aria-hidden="true"></i>}</td>
                                        <td>{program.thesisNormalizedFactor}</td>
                                        <td>
                                            <button class="btn btn-primary btn-margin btn-xs" data-toggle="modal" data-target="#updateTrainingProgram"
                                                onClick={() => this.setState({current_program: program, current_action: "update"})}>
                                                Sửa
                                            </button>
                                            <button class="btn btn-primary btn-margin btn-xs" onClick={() => this.deleteHandler(program)}>
                                                Xóa
                                            </button>
                                        </td>
                                    </tr>
                            )}) }
                        </tbody>
                    </table>
                </div>

                <AdminTrainingProgramsUpdate
                    modalId="updateTrainingProgram"
                    action={current_action}
                    actions={this.props.actions}
                    addLastestRow={this.addLastestRow}
                    program={current_program}
                    areas={areas}
                    types={types}
                    levels={levels}
                    departments={departments}
                />
            </div>
        )
    }
}

export default AdminTrainingPrograms
