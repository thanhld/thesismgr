import React, { Component } from 'react'
import { notify } from 'Components'
import { ADMIN_UPDATE_TOPIC } from 'Constants'

const isTopicChange = status => {
    return status >= 890 && status <= 891
}

class AddNewOutOfficers extends Component {
    constructor(props) {
        super(props)
        this.state = {
            current_out: [null, null],
            current_depart_type: [1, 1],
            current_main: '',
            current_co: '',
            error: false,
            message: ''
        }
    }
    componentWillReceiveProps(nextProps) {
        if (!nextProps.topic) return
        const { outOfficers, topic } = nextProps
        // Set current_main
        const { mainSupervisorId, coSupervisorIds, topicStatus } = topic
        if (mainSupervisorId) this.setState({ current_main: mainSupervisorId })
        // Set current_co
        if (coSupervisorIds) this.setState({ current_co: coSupervisorIds })
        if (isTopicChange(topicStatus)) {
            const { mainSupervisorId, coSupervisorIds } = topic.topicChange
            if (mainSupervisorId) this.setState({ current_main: mainSupervisorId })
            // Set current_co
            if (coSupervisorIds) this.setState({ current_co: coSupervisorIds })
        }
        // Set current_out
        const { outOfficerIds } = topic
        const outList = outOfficerIds.split(',')
        const newOutList = outList.map((o, index) => {
            if (!o) return null
            const tmp = {...outOfficers.list.find(_o => _o.id == o)}
            tmp['outOfficerId'] = o
            delete tmp['id']
            return tmp
        })
        this.setState({
            current_out: newOutList,
            error: false
        })
    }
    handleChange = index => e => {
        const name = e.target.name
        const value = e.target.value
        let newCurrent = [...this.state.current_out]
        newCurrent[index] = {
            ...newCurrent[index],
            [name]: value
        }
        this.setState({
            current_out: newCurrent
        })
    }
    changeDepartmentType = index => e => {
        const value = e.target.value
        let newDepart = [...this.state.current_depart_type]
        newDepart[index] = value
        this.setState({
            current_depart_type: newDepart
        })
    }
    handleSubmit = e => {
        e.preventDefault()
        const { action } = this.props
        if (action == 'create') this.handleCreate()
        else if (action == 'verify') this.handleVerify()
    }
    checkVerify = pos => {
        const { topic: {outOfficerIds} } = this.props
        const outOfficerList = outOfficerIds.split(',')
        return outOfficerList[pos] == ''
    }
    handleVerify = () => {
        let tmp = []
        const { current_main, current_co } = this.state
        let data = {
            mainSupervisorId: this.state.current_main,
            coSupervisorIds: this.state.current_co
        }
        Object.keys(data).forEach(key => {
            if (data[key] == this.props.topic[key]) delete data[key]
        })
        const { actions, reloadData, modalId, topic: {id, topicStatus} } = this.props
        actions.createActivity(ADMIN_UPDATE_TOPIC, [{
            stepId: isTopicChange(topicStatus) ? 212 : 210,
            topicId: id,
            data
        }]).then(res => {
            const { data } = res.action.payload
            if (data.length == 0) {
                reloadData(() => {
                    $(`#${modalId}`).modal('hide')
                    notify.show(`Thầy/cô đã cập nhật đề tài thành công`, 'primary')
                })
            } else {
                notify.show(`Có lỗi xảy ra: ${data[0].error}`, 'danger')
            }
        }).catch(err => {
            console.log(err);
            notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
        })
    }
    handleCreate = () => {
        const data = []
        const { current_depart_type } = this.state
        this.state.current_out.forEach((c, index) => {
            if (!c) return
            if (current_depart_type[index] == 1) delete c['departmentId']
            if (current_depart_type[index] == 2) delete c['departmentName']
            data.push(c)
        })
        const { actions, reloadData, modalId, verifyState } = this.props
        actions.createOutOfficers(data).then(res => {
            const { data } = res.action.payload
            if (data.length == 0) {
                reloadData(() => {
                    verifyState()
                })
            } else {
                this.setState({
                    error: true,
                    message: `${data.map(d => `${d.outOfficerId}: ${d.error}`).join()}`
                })
            }
        }).catch(err => {
            console.log(err);
            notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
        })
    }
    render() {
        const { error, message, current_out, current_main, current_co, current_depart_type } = this.state
        const { action, modalId, topic, outOfficers, degrees, lecturers, departments } = this.props
        const optLecturer = <optgroup label="Giảng viên trong khoa">
            { lecturers && lecturers.list.filter(l => l.role == 3).map(l => {
                const degree = degrees.list.find(d => d.id == l.degreeId)
                return (
                    <option key={l.id} value={l.id}>{degree && `${degree.name}.`} {l.fullname}</option>
                )}) }
        </optgroup>
        const optOutLecturer = <optgroup label="Giảng viên đơn vị ngoài">
            { lecturers && lecturers.list.filter(l => l.role == 5).map(l => {
                const degree = degrees.list.find(d => d.id == l.degreeId)
                const department = departments.list.find(d => d.id == l.departmentId)
                return (
                    <option key={l.id} value={l.id}>{degree && `${degree.name}.`} {l.fullname} {department && `- ${department.name}`}</option>
                )}) }
        </optgroup>
        if (!topic) return false
        const { outOfficerIds } = topic
        const outList = outOfficerIds.split(',')
        return <div id={`${modalId}`} class="modal fade" tabIndex="-1" role="dialog" aria-labelledby="newOutOfficers">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="newOutOfficers">{action == 'create' ? 'Tạo giảng viên đơn vị ngoài' : 'Cập nhật đề tài cho học viên'}</h4>
                    </div>
                    <form class="form-horizontal" onSubmit={this.handleSubmit} autoComplete="off">
                        <div class="modal-body">
                            { error &&
                                <div>
                                    <div class="text-message-error">Có lỗi xảy ra: {message}</div>
                                    <br />
                                </div> }
                            { action == 'create' && outList.map((o, index) => {
                                if (!o) return false
                                if (!current_out[index]) return false
                                const position = index == 0 ? 'Giảng viên chính' : 'Đồng hướng dẫn'
                                return (<div key={index}>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">{position}</label>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Tạo đơn vị theo</label>
                                        <div class="col-sm-8">
                                            <select class="form-control" value={current_depart_type[index]} onChange={this.changeDepartmentType(index)}>
                                                <option value="1">Tên được học viên nhập</option>
                                                <option value="2">Từ danh sách đơn vị đã có</option>
                                            </select>
                                        </div>
                                    </div>
                                    { current_depart_type[index] == 1 && <div class="form-group">
                                        <label class="col-sm-3 control-label">Tên đơn vị</label>
                                        <div class="col-sm-8">
                                            <input name="departmentName" type="text" class="form-control" placeholder="Tên đơn vị" value={current_out[index].departmentName || ''} onChange={this.handleChange(index)} required />
                                        </div>
                                    </div> }
                                    { current_depart_type[index] == 2 && <div class="form-group">
                                        <label class="col-sm-3 control-label">Đơn vị</label>
                                        <div class="col-sm-8">
                                            <select name="departmentId" value={current_out[index].departmentId || ''} class="form-control" onChange={this.handleChange(index)} required>
                                                <option value='' disabled hidden>Chọn đơn vị</option>
                                                { departments.list.map(d => <option key={d.id} value={d.id}>{d.name}</option>) }
                                            </select>
                                        </div>
                                    </div> }
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Mã cán bộ</label>
                                        <div class="col-sm-8">
                                            <input name="officerCode" type="text" class="form-control" placeholder="Mã cán bộ" value={current_out[index].officerCode || ''} onChange={this.handleChange(index)} required />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Tên cán bộ</label>
                                        <div class="col-sm-8">
                                            <input name="fullname" type="text" class="form-control" placeholder="Tên cán bộ" value={current_out[index].fullname || ''} onChange={this.handleChange(index)} required />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Học hàm, học vị</label>
                                        <div class="col-sm-8">
                                            <select name="degreeId" class="form-control" value={current_out[index].degreeId || ''} onChange={this.handleChange(index)}>
                                                <option value="" hidden disabled>Chọn học hàm, học vị</option>
                                                { degrees.list.map(d => <option value={d.id} key={d.id}>{d.name}</option>) }
                                            </select>
                                        </div>
                                    </div>
                                </div>)
                            })}
                            { action == 'verify' && <div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Đề tài</label>
                                    <div class="col-sm-8">
                                        <input class="form-control" value={topic.vietnameseTopicTitle} disabled />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Giảng viên hướng dẫn chính</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" value={current_main || ''} onChange={e => { this.setState({current_main: e.target.value})}} required disabled={this.checkVerify(0)}>
                                            <option value='' hidden disabled>Chọn giảng viên hướng dẫn chính</option>
                                            { optLecturer }
                                            { optOutLecturer }
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Đồng hướng dẫn</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" value={current_co || ''} onChange={e => { this.setState({current_co: e.target.value}) }} disabled={this.checkVerify(1)}>
                                            <option value=''>Không có</option>
                                            { optLecturer }
                                            { optOutLecturer }
                                        </select>
                                    </div>
                                </div>
                            </div> }
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary btn-margin">
                                Đồng ý
                            </button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Bỏ qua</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    }
}

export default AddNewOutOfficers
