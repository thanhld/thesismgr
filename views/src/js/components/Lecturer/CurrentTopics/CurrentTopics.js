import React, { Component } from 'react'
import { Loading, Topic } from 'Components'
import EditTopic from './EditTopic'
import { routeName, TOPIC_STATUS } from 'Config'

const TOPIC_TYPE = {
    [routeName['LECTURER_STUDENT']]: 1,
    [routeName['LECTURER_GRADUATED']]: 2,
    [routeName['LECTURER_RESEARCHER']]: 3
}

const TOPIC_NAME = {
    [routeName['LECTURER_STUDENT']]: 'khóa luận tốt nghiệp',
    [routeName['LECTURER_GRADUATED']]: 'luận văn cao học',
    [routeName['LECTURER_RESEARCHER']]: 'luận án tiến sĩ'
}

const LECTURE_QUOTA = {
    [routeName['LECTURER_STUDENT']]: 'numberOfStudent',
    [routeName['LECTURER_GRADUATED']]: 'numberOfGraduated',
    [routeName['LECTURER_RESEARCHER']]: 'numberOfResearch'
}

const QUOTA_FIELD = {
    [routeName['LECTURER_STUDENT']]: 'maxStudent',
    [routeName['LECTURER_GRADUATED']]: 'maxGraduated',
    [routeName['LECTURER_RESEARCHER']]: 'maxResearcher'
}

class LecturerCurrentTopics extends Component {
    constructor(props) {
        super(props)
        this.state = {
            cur_topic: {
                id: '',
                vietnameseTopicTitle: '',
                isEnglish: false,
                englishTopicTitle: '',
                tags: '',
                description: ''
            }
        }
    }
    reloadData = callback => {
        const { actions, uid } = this.props
        actions.loadLecturerInformation(uid)
        actions.loadTopics(uid).then(callback)
    }
    componentWillMount() {
        const { actions, profile, topics, lecturers, degrees, outOfficers, quotas, uid } = this.props
        if (!degrees.isLoaded) actions.loadDegrees()
        if (!lecturers.isLoaded) actions.loadLecturers()
        if (!outOfficers.isLoaded) actions.loadOutOfficers()
        if (!quotas.isLoaded) actions.loadActiveQuota()
        if (!profile.isLoaded) actions.loadLecturerInformation(uid)
        if (!topics.isLoaded) actions.loadTopics(uid)
    }
    setEditTopic = id => {
        const { topics } = this.props
        const editTopic = topics.list.find(t => t.id == id)
        this.setState({
            cur_topic: editTopic
        })
    }
    render() {
        const { uid, topics, profile, lecturers, degrees, outOfficers, quotas, actions, params: {type} } = this.props
        if (!topics.isLoaded) return <Loading />
        if (!degrees.isLoaded) return <Loading />
        if (!profile.isLoaded) return <Loading />
        if (!lecturers.isLoaded) return <Loading />
        if (!outOfficers.isLoaded) return <Loading />
        const quota = quotas.list.find(q => q.degreeId == profile.lecturer.degreeId)
        const superviseTopics = topics.list.filter(t => !t.expertiseOfficerIds || ( t.expertiseOfficerIds && !t.expertiseOfficerIds.includes(uid)))
        const requestTopics = superviseTopics.filter(t => t.topicStatus == 101 && t.topicType == TOPIC_TYPE[type])
        const currentTopics = superviseTopics.filter(t => t.topicStatus != 0 && t.topicStatus != 2 && t.topicStatus > 101 && t.topicType == TOPIC_TYPE[type])
        const finishTopics = superviseTopics.filter(t => (t.topicStatus == 0 || t.topicStatus == 2) && t.topicType == TOPIC_TYPE[type])
        return <div>
            <div class="row">
                <div class="pull-right">
                    Thầy/cô có thể hướng dẫn thêm {Number(quota[QUOTA_FIELD[type]])-Number(profile.lecturer[LECTURE_QUOTA[type]]).toFixed(2)} {TOPIC_NAME[type]}
                </div>
            </div>
            { requestTopics.length > 0 && <div>
                <div class="row">
                    <div class="col-xs-6 page-title">
                        Đề tài đang đăng ký
                    </div>
                </div>
                { requestTopics.map(topic => { return (
                    <Topic
                        key={topic.id}
                        uid={uid}
                        topic={topic}
                        actions={actions}
                        reloadData={this.reloadData}
                        degrees={degrees.list}
                        lecturers={lecturers.list}
                        outOfficers={outOfficers.list}
                        setEditTopic={this.setEditTopic}
                    />
                )})}
                <EditTopic
                    uid={uid}
                    actions={actions}
                    reloadData={this.reloadData}
                    topic={this.state.cur_topic} />
            </div> }
            <div>
                <div class="row">
                    <div class="col-xs-12 page-title">
                        Đề tài đang hướng dẫn
                    </div>
                </div>
                { currentTopics.length > 0 ?
                    currentTopics.map(topic => { return (
                        <Topic
                            key={topic.id}
                            uid={uid}
                            topic={topic}
                            actions={actions}
                            reloadData={this.reloadData}
                            degrees={degrees.list}
                            lecturers={lecturers.list}
                            outOfficers={outOfficers.list}
                        />
                    )})
                    : <div class="margin-bottom">Thầy/cô chưa hướng dẫn đề tài nào.</div> }
            </div>
            <div>
                <div class="row">
                    <div class="col-xs-12 page-title">
                        Đề tài đã hoàn thành
                    </div>
                </div>
                { finishTopics.length > 0 ?
                    finishTopics.map(topic => { return (
                        <div key={topic.id}><i class="fa fa-genderless fa-fw" aria-hidden="true"></i> {topic.isEnglish ? topic.englishTopicTitle : topic.vietnameseTopicTitle} - {topic.learner.fullname} - { TOPIC_STATUS[topic.topicStatus] }</div>
                    )})
                    : <div class="margin-bottom">Thầy/cô chưa có đề tài hoàn thành.</div> }
            </div>
        </div>
    }
}

export default LecturerCurrentTopics
