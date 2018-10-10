import React, { Component } from 'react'
import AddNewOutOfficers from './AddNewOutOfficers'
import { Loading } from 'Components'
import { ADMIN_LOAD_OUT_TOPICS } from 'Constants'

class AdminOutOfficers extends Component {
    constructor(props) {
        super(props)
        this.state = {
            current_action: 'create',
            current_topic: null
        }
    }
    reloadData = callback => {
        const { actions } = this.props
        const filter = `outOfficerIds<>null`
        actions.loadLecturers()
        actions.loadTopics(ADMIN_LOAD_OUT_TOPICS, filter).then(() => {
            callback()
        })
    }
    verifyState = () => {
        this.setState({
            current_action: 'verify'
        })
    }
    componentWillMount() {
        const { facultyId, actions, topics, degrees, departments, outOfficers, lecturers } = this.props
        const filter = `outOfficerIds<>null`
        if (!degrees.isLoaded) actions.loadDegrees()
        if (!departments.isLoaded) actions.loadDepartmentOfFaculty(facultyId)
        if (!outOfficers.isLoaded) actions.loadOutOfficers()
        if (!lecturers.isLoaded) actions.loadLecturers()
        if (!topics.isLoaded) actions.loadTopics(ADMIN_LOAD_OUT_TOPICS, filter)
    }
    getVerifyOfficers = outOfficerIds => {
        let result = []
        const outList = outOfficerIds ? outOfficerIds.split(',') : []
        const { actions, degrees, outOfficers } = this.props
        if (!outOfficers.isLoaded) return false
        outList.forEach(o => {
            if (!o) return
            const outOfficer = outOfficers.list.find(_o => _o.id == o)
            if (!outOfficer) return false
            const { fullname, degreeId, departmentName } = outOfficer
            const degree = degrees.list.find(d => d.id == degreeId)
            result.push(`${degree && `${degree.name}.`} ${fullname} - ${departmentName}`)
        })
        return result.join('; ')
    }
    componentWillUnmount() {
        const { actions } = this.props
        actions.flushTopics()
    }
    render() {
        const topics = this.props.topics.list
        const { actions, degrees, departments, outOfficers, lecturers } = this.props
        const { current_action, current_topic } = this.state
        if (!outOfficers.isLoaded) return <Loading />
        if (!departments.isLoaded) return <Loading />
        if (!degrees.isLoaded) return <Loading />
        if (!lecturers.isLoaded) return <Loading />
        return <div>
            <div class="row">
                <div class="col-xs-12 page-title">
                    Đề tài cần xác nhận giảng viên
                </div>
            </div>
            { topics && topics.length == 0 && <div>
                Hiện chưa có yêu cầu xác nhận giảng viên
            </div> }
            { topics && topics.length > 0 && <div class="table-responsive">
                <table class="table table-hover table-condensed">
                    <thead>
                        <tr>
                            <th>Mã học viên</th>
                            <th>Học viên</th>
                            <th class="col-xs-4">Tên đề tài</th>
                            <th class="hidden-xs">Giảng viên cần xác nhận</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        { topics && topics.length > 0 && topics.map(topic => {
                            return (
                                <tr key={topic.id}>
                                    <td>{topic.learner.learnerCode}</td>
                                    <td>{topic.learner.fullname}</td>
                                    <td>{topic.vietnameseTopicTitle}</td>
                                    <td>{this.getVerifyOfficers(topic.outOfficerIds)}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary btn-margin" data-toggle="modal" data-target="#outOfficers" onClick={() => {this.setState({current_action: 'create', current_topic: topic})}}>Tạo giảng viên</button>
                                        <button class="btn btn-sm btn-primary btn-margin" data-toggle="modal" data-target="#outOfficers" onClick={() => {this.setState({current_action: 'verify', current_topic: topic})}}>Xác nhận</button>
                                    </td>
                                </tr>
                            )}) }
                    </tbody>
                </table>
            </div> }
            <AddNewOutOfficers
                action={current_action}
                topic={current_topic}
                degrees={degrees}
                departments={departments}
                lecturers={lecturers}
                outOfficers={outOfficers}
                actions={actions}
                reloadData={this.reloadData}
                verifyState={this.verifyState}
                modalId="outOfficers"
            />
        </div>
    }
}

export default AdminOutOfficers
