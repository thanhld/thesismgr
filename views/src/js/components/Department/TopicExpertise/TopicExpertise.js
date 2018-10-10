import React, { Component } from 'react'
import { Tab, Tabs, TabList, TabPanel } from 'react-tabs'
import Pagination from 'react-js-pagination'
import { ITEM_PER_PAGE, PAGE_RANGE } from 'Config'
import { Loading, Modal, notify } from 'Components'
import { DEPARTMENT_LOAD_TOPICS, DEPARTMENT_APPROVE_TOPIC } from 'Constants'
import { getDepartmentMessage, reviewStatusToText } from 'Helper'
import TopicsList from './TopicsList'

const TOPIC_STATUS_RANGE = {
    0: '104',
    1: '896'
}

class TopicExpertise extends Component {
    constructor(props) {
        super(props)
        this.state = {
            activePage: 1,
            currentRange: TOPIC_STATUS_RANGE[0],
            current_topic: {}
        }
    }
    componentWillMount() {
        const { actions, profile, topics, lecturers, degrees, uid } = this.props
        if (!profile.isLoaded) {
            actions.loadLecturerInformation(uid).then(res => {
                const { departmentId } = res.action.payload.data
                const filter = `topicType=2,topicStatus=104,departmentId=${departmentId}`
                if (!topics.isLoaded) actions.loadTopics(DEPARTMENT_LOAD_TOPICS, filter)
            })
        }
        if (!degrees.isLoaded) actions.loadDegrees()
        if (!lecturers.isLoaded) actions.loadLecturers()
    }
    handleSelect = index => {
        const { actions } = this.props
        actions.flushTopics()
        const nextRange = TOPIC_STATUS_RANGE[index]
        this.setState({currentRange: nextRange, activePage: 1}, this.reloadData)
    }
    reloadData = callback => {
        const { actions } = this.props
        const { departmentId } = this.props.profile.lecturer
        const { currentRange } = this.state
        const filter = `topicType=2,topicStatus=${currentRange},departmentId=${departmentId}`
        actions.loadTopics(DEPARTMENT_LOAD_TOPICS, filter).then(() => {
            if (typeof callback === 'function')
                callback()
        })
    }
    handlePageChange = pageNum => {
        this.setState({
            activePage: pageNum
        })
    }
    departmentApprove = (topic, stepId, e) => {
        e.preventDefault()
        const val = confirm(getDepartmentMessage(stepId))
        if (!val) return false
        const { actions } = this.props
        const data = {
            topicId: topic.id,
            topicStatus: topic.topicStatus,
            stepId,
        }
        actions.createActivity(DEPARTMENT_APPROVE_TOPIC, [data]).then(res => {
            const { data } = res.action.payload
            if (data.length > 0) {
                notify.show(`Có lỗi xảy ra: ${data[0].error}`, 'danger')
            } else {
                this.reloadData(() => {
                    notify.show(`Thầy/cô đã chấp nhận đề tài thành công`, 'primary')
                })
            }
        }).catch(err => {
            notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
        })
    }
    changeCurrentTopic = topic => {
        this.setState({current_topic: topic})
    }
    render() {
        const { actions, topics, lecturers, degrees } = this.props
        const { activePage, current_topic } = this.state
        if (!topics.isLoaded) return <Loading />
        if (!lecturers.isLoaded) return <Loading />
        if (!degrees.isLoaded) return <Loading />
        return <div>
            <div class="row">
                <div class="col-xs-9">
                    <div class="page-title">Thẩm định đề cương luận văn cao học</div>
                </div>
            </div>
            <Tabs
                onSelect={this.handleSelect}>
                <TabList>
                    <Tab>Đề tài đăng ký mới</Tab>
                    <Tab>Đề tài xin điều chỉnh</Tab>
                </TabList>
                {/* Register tab */}
                <TabPanel>
                    <TopicsList
                        {...this.props}
                        activePage={activePage}
                        current_topic={current_topic}
                        reloadData={this.reloadData}
                        handlePageChange={this.handlePageChange}
                        departmentApprove={this.departmentApprove}
                        changeCurrentTopic={this.changeCurrentTopic}
                    />
                </TabPanel>
                <TabPanel>
                    <TopicsList
                        {...this.props}
                        activePage={activePage}
                        current_topic={current_topic}
                        reloadData={this.reloadData}
                        handlePageChange={this.handlePageChange}
                        departmentApprove={this.departmentApprove}
                        changeCurrentTopic={this.changeCurrentTopic}
                    />
                </TabPanel>
            </Tabs>
        </div>
    }
}

export default TopicExpertise
