import React, { Component } from 'react'
import TopicRegister from './Register'
import RequestModal from './RequestModal'
import { Activity, Loading, Topic, notify } from 'Components'
import {
    LEARNER_LOAD_TOPICS
} from 'Constants'
import { topicWorking } from 'Helper'

class LearnerTopics extends Component {
    constructor(props) {
        super(props)
        this.state = {
            isRequestChange: false,
            requestType: 'extend'
        }
    }
    componentWillMount() {
        const { uid, actions, topics, degrees, departments, lecturers, outOfficers, quotas } = this.props
        const filter = `learnerId=${uid}`
        if (!degrees.isLoaded) actions.loadDegrees()
        if (!lecturers.isLoaded) actions.loadLecturers()
        if (!departments.isLoaded) actions.loadDepartments()
        if (!outOfficers.isLoaded) actions.loadOutOfficers()
        if (!quotas.isLoaded) actions.loadActiveQuota()
        if (!topics.isLoaded) actions.loadTopics(uid)
    }
    reloadTopic = callback => {
        const { uid, actions } = this.props
        //const filter = `learnerId=${uid}`
        actions.loadOutOfficers()
        actions.loadTopics(uid).then(callback)
    }
    changeRequestType = type => {
        const { topics } = this.props
        if (!topics.isLoaded) return false
        const { data } = topics
        this.setState({
            requestType: type
        }, () => {
            $(`#requestChange`).modal('show')
        })
    }
    cancelRequest = () => {
        const val = confirm(`Bạn có chắc chắn muốn hủy yêu cầu sửa đổi này?`)
        if (!val) return
        const topic = this.props.topics.data[0]
        const { id } = topic
        this.props.actions.cancelChangeRequest(id).then(res => {
            const { data } = res.action.payload
            if (data.length == 0) {
                this.reloadTopic(() => {
                    notify.show(`Bạn đã hủy yêu cầu thành công`, 'primary')
                })
            } else notify.show(`Có lỗi xảy ra: ${data[0].error}`, 'danger')
        }).catch(err => {
            notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
        })
    }
    requestChange = () => {
        const { topics } = this.props
        if (!topics.isLoaded) return false
        const { data } = topics
        if (data[0].topicStatus != 889) return false
        this.setState({
            isRequestChange: true
        })
    }
    finishRequest = () => {
        this.setState({
            isRequestChange: false
        })
    }
    render() {
        const { isRequestChange, requestType } = this.state
        const { actions, uid, topics, lecturers, departments, degrees, outOfficers } = this.props
        const { data } = topics
        if (!topics.isLoaded) return <Loading />
        if (!lecturers.isLoaded) return <Loading />
        if (!degrees.isLoaded) return <Loading />
        if (!departments.isLoaded) return <Loading />
        if (!outOfficers.isLoaded) return <Loading />
        return <div>
            { topics && data && data.length == 0 && <div class="alert alert-info wrap-text" role="alert">
                <b>Lưu ý: </b> Hiện tại bạn đang chưa được tham gia đăng ký và bảo vệ đề tài.
                Vui lòng chờ khoa mở đợt đăng ký đề tài.
            </div> }
            { isRequestChange && <TopicRegister
                {...this.props}
                topics={data[0]}
                action="update"
                finishRequest={this.finishRequest}
                reloadTopic={this.reloadTopic} /> }
            { topics && data && data.length > 0 && data[0].topicStatus == 100 &&
                <TopicRegister
                    {...this.props}
                    topics={data[0]}
                    action="create"
                    reloadTopic={this.reloadTopic}
                /> }
            { !isRequestChange && topics && data && data.length > 0 && data[0].topicStatus != 100 && <div>
                <div class="row">
                    <div class="col-xs-12 page-title">
                        Đề tài của tôi
                    </div>
                </div>
                <Topic uid={uid} topic={data[0]}
                    lecturers={lecturers.list} degrees={degrees.list} outOfficers={outOfficers.list} requestChange={this.requestChange} changeRequestType={this.changeRequestType} cancelRequest={this.cancelRequest}
                />
                <Activity
                    topicId={data[0].id}
                    lecturers={(lecturers && lecturers.list) || []}
                />
            <RequestModal topicId={data[0].id} supervisor={data[0].requestedSupervisorId} requestType={requestType} actions={actions} reloadTopic={this.reloadTopic} />
            </div> }
        </div>
    }
}

export default LearnerTopics
