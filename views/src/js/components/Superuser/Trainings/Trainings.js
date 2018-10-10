import React, { Component } from 'react'
import { notify } from 'Components'
import TypeUpdate from './TypeUpdate'
import LevelUpdate from './LevelUpdate'

const LEVEL_NAME = {
    "1": 'Sinh viên',
    "2": 'Học viên cao học',
    "3": 'Nghiên cứu sinh'
}

class SuperuserTrainings extends Component {
    constructor() {
        super()
        this.state = {
            current_type: {
                name: ''
            },
            current_level: {
                name: '',
                levelType: 1
            },
            current_type_action: '',
            current_level_action: '',
            lastest_type: '',
            lastest_level: ''
        }
        this.handleDelete = this.handleTypeDelete.bind(this)
        this.addLastestType = this.addLastestType.bind(this)
        this.addLastestLevel = this.addLastestLevel.bind(this)
    }
    componentWillMount() {
        const { types, levels, actions } = this.props
        if (!types.isLoaded) actions.loadTrainingTypes()
        if (!levels.isLoaded) actions.loadTrainingLevels()
    }
    addLastestType(id) {
        this.setState({
            lastest_type: id
        })
        setTimeout(() => {
            this.setState({
                lastest_type: ''
            })
        }, 1000)
    }
    addLastestLevel(id) {
        this.setState({
            lastest_level: id
        })
        setTimeout(() => {
            this.setState({
                lastest_level: ''
            })
        }, 1000)
    }
    handleTypeDelete(type) {
        const val = confirm(`Thầy/cô có chắc chắn muốn xóa hệ đào tạo ${type.name}?`)
        if (val) {
            const { actions } = this.props
            actions.deleteTrainingType(type.id).then(() => {
                actions.loadTrainingTypes()
                notify.show(`Đã xóa hệ đào tạo ${type.name}`, 'primary')
            }).catch(err => {
                notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
            })
        }
    }
    handleLevelDelete(level) {
        const val = confirm(`Thầy/cô có chắc chắn muốn xóa bậc đào tạo ${level.name}?`)
        if (val) {
            const { actions } = this.props
            actions.deleteTrainingLevel(level.id).then(() => {
                actions.loadTrainingLevels()
                notify.show(`Đã xóa bậc đào tạo ${level.name}`, 'primary')
            }).catch(err => {
                notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
            })
        }
    }
    render() {
        const { types, levels, actions } = this.props
        const { current_type, current_type_action, current_level, current_level_action, lastest_type, lastest_level } = this.state
        return (
            <div>
                <div>
                    <div class="row">
                        <div class="col-xs-9">
                            <div class="page-title">Hệ đào tạo</div>
                        </div>
                        <div class="col-xs-3">
                            <div>
                                <div class="pull-right">
                                    <button type="button" class="btn btn-success btn-margin btn-sm" data-toggle="modal" data-target="#updateType" onClick={() => {this.setState({current_type: {name: ''}, current_type_action: "create"})}}>
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
                                    <th class="col-xs-9">Tên hệ đào tạo</th>
                                    <th class="col-xs-3">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                { types.list && types.list.map(type => { return (
                                    <tr key={type.id} class={'row-item ' + (lastest_type == type.id && 'lastest-row-item')}>
                                        <td class="col-xs-9">{type.name}</td>
                                        <td class="col-xs-3">
                                            <button type="button" class="btn btn-primary btn-margin btn-xs" data-toggle="modal" data-target="#updateType" onClick={() => {this.setState({current_type: type, current_type_action: "update"})}}>Sửa</button>
                                            <button type="button" class="btn btn-primary btn-margin btn-xs" onClick={() => this.handleDelete(type)}>Xóa</button>
                                        </td>
                                    </tr>
                                )}) }
                            </tbody>
                        </table>
                    </div>
                    <TypeUpdate
                        modalId="updateType"
                        addLastestType={this.addLastestType}
                        actions={actions}
                        action={current_type_action}
                        type={current_type} />
                </div>
                <br />
                <br />
                <div>
                    <div class="row">
                        <div class="col-xs-9">
                            <div class="page-title">Bậc đào tạo</div>
                        </div>
                        <div class="col-xs-3">
                            <div>
                                <div class="pull-right">
                                    <button type="button" class="btn btn-success btn-margin btn-sm" data-toggle="modal" data-target="#updateLevel" onClick={() => {this.setState({current_level: {name: '', levelType: '1'}, current_level_action: "create"})}}>
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
                                    <th class="col-xs-5">Tên bậc đào tạo</th>
                                    <th class="col-xs-4">Học viên</th>
                                    <th class="col-xs-3">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                { levels.list && levels.list.map(level => { return (
                                    <tr key={level.id} class={'row-item ' + (lastest_level == level.id && 'lastest-row-item')}>
                                        <td class="col-xs-5">{level.name}</td>
                                        <td class="col-xs-4">{LEVEL_NAME[level.levelType]}</td>
                                        <td class="col-xs-3">
                                            <button type="button" class="btn btn-primary btn-margin btn-xs" data-toggle="modal" data-target="#updateLevel" onClick={() => {this.setState({current_level: level, current_level_action: "update"})}}>Sửa</button>
                                            <button type="button" class="btn btn-primary btn-margin btn-xs" onClick={() => this.handleLevelDelete(level)}>Xóa</button>
                                        </td>
                                    </tr>
                                )}) }
                            </tbody>
                        </table>
                    </div>
                    <LevelUpdate
                        modalId="updateLevel"
                        addLastestLevel={this.addLastestLevel}
                        actions={actions}
                        action={current_level_action}
                        level={current_level} />
                </div>
            </div>
        )
    }
}

export default SuperuserTrainings
