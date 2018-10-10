import React, { Component } from 'react'
import { Topic, Loading, notify } from 'Components'
import ExpertiseModal from './ExpertiseModal'

class LecturerExpertise extends Component {
    constructor(props) {
        super(props)
        this.state = {
            cur_topic: {}
        }
    }
    reloadData = callback => {
        const { actions, uid } = this.props
        actions.loadTopics(uid).then(callback)
    }
    componentWillMount() {
        const { actions, topics, lecturers, degrees, outOfficers, uid } = this.props
        if (!degrees.isLoaded) actions.loadDegrees()
        if (!lecturers.isLoaded) actions.loadLecturers()
        if (!outOfficers.isLoaded) actions.loadOutOfficers()
        if (!topics.isLoaded) actions.loadTopics(uid)
    }
    changeReviewTarget = topic => {
        this.setState({
            cur_topic: topic
        })
    }
    render() {
        const { uid, topics, lecturers, degrees, outOfficers, actions } = this.props
        const { cur_topic } = this.state
        if (!degrees.isLoaded) return <Loading />
        if (!lecturers.isLoaded) return <Loading />
        if (!outOfficers.isLoaded) return <Loading />
        if (!topics.isLoaded) return <Loading />
        const expertiseTopics = topics.list.filter(t => t.expertiseOfficerIds && t.expertiseOfficerIds.includes(uid))
        return <div>
            <div class="row">
                <div class="col-xs-6 page-title">
                    Phản biện đề cương luận văn cao học
                </div>
            </div>
            { expertiseTopics.length == 0 && <div>Thầy/cô hiện không phản biện đề cương nào.</div> }
            { expertiseTopics.length > 0 && expertiseTopics.map(topic => {
                return <div key={topic.id}>
                    <Topic
                        expertise={true}
                        uid={uid}
                        topic={topic}
                        degrees={degrees.list}
                        lecturers={lecturers.list}
                        outOfficers={outOfficers.list}
                        changeReviewTarget={this.changeReviewTarget} />
                </div> }) }
            <ExpertiseModal
                uid={uid}
                modalId={'reviewTopic'}
                title={'Phản biện đề cương luận văn cao học'}
                actions={actions}
                topic={cur_topic}
                reloadData={this.reloadData}
                />
        </div>
    }
}

export default LecturerExpertise
