import React, { Component } from 'react'
import axios from 'axios'
import { browserHistory } from 'react-router'
import { Activity, Topic } from 'Components'
import { dateMoment } from 'Helper'

class TopicActivity extends Component {
    constructor(props) {
        super(props)
        this.state = {
            topic: {}
        }
    }
    componentWillMount() {
        const { actions, degrees, lecturers, outOfficers, params: {id} } = this.props
        if (!degrees.isLoaded) actions.loadDegrees()
        if (!lecturers.isLoaded) actions.loadLecturers()
        if (!outOfficers.isLoaded)  actions.loadOutOfficers()
        axios.get(`/topic/${id}`).then(res => {
            const { data } = res
            this.setState({
                topic: data
            })
        }).catch(err => {
            console.log(err);
        })
    }
    handleBack = e => {
        e.preventDefault()
        browserHistory.goBack()
    }
    render() {
        const { topic } = this.state
        const { uid, params: {id}, degrees, lecturers, outOfficers } = this.props
        if (Object.keys(topic).length == 0) return false
        const { reviews } = topic
        return <div>
            <div class="row">
                <div class="col-md-12 page-title">
                    Chi tiết đề cương
                </div>
            </div>
            <Topic
                uid={uid}
                topic={topic}
                degrees={degrees.list}
                lecturers={lecturers.list}
                outOfficers={outOfficers.list}
                alreadyDetail={true}
                />
            { reviews && reviews.length > 0 && <div>
                <div class="row">
                    <div class="col-xs-12 page-title">
                        Thẩm định đề cương
                    </div>
                </div>
                <div class="margin-bottom">
                    { reviews.map(r => {
                        const { reviewStatus, officerId, content } = r
                        const lecturer = lecturers.list.find(l => l.id == officerId)
                        const degree = lecturer && degrees.list.find(d => d.id == lecturer.degreeId)
                        if (reviewStatus == 1) {
                            return <div key={r.id}>
                                {dateMoment(r.created)}: {degree && `${degree.name}.`} {lecturer.fullname} chấp nhận đề cương với nhận xét
                                <div class="review-content wrap-text">
                                    { content }
                                </div>
                            </div>
                        }
                        if (reviewStatus == 3) {
                            return <div key={r.id}>
                                {dateMoment(r.created)}: {degree && `${degree.name}.`} {lecturer.fullname} không chấp nhận đề cương với nhận xét
                                <div class="review-content wrap-text">
                                    { content }
                                </div>
                            </div>
                        }
                        if (reviewStatus == 2) {
                            return <div key={r.id}>
                                {dateMoment(r.created)}: {degree && `${degree.name}.`} {lecturer.fullname} yêu cầu đề cương cần chỉnh sửa thêm với nhận xét
                                <div class="review-content wrap-text">
                                    { content }
                                </div>
                            </div>
                        }
                        if (reviewStatus == 0) {
                            return <div key={r.id}>
                                {degree && `${degree.name}.`} {lecturer.fullname} chưa nhận xét
                            </div>
                        }
                        if (reviewStatus == 4) {
                            return <div key={r.id}>
                                <i class="fa fa-genderless fa-fw" aria-hidden="true"></i>
                                {degree && `${degree.name}.`} {lecturer.fullname} từ chối phản biện đề cương với lý do
                                <div class="review-content wrap-text">
                                    { content }
                                </div>
                            </div>
                        }
                    }) }
                </div>
            </div> }
            <Activity
                topicId={id}
                lecturers={lecturers.list}
                />
            <br />
            <a class="clickable" onClick={this.handleBack}>Quay lại trang trước</a>
        </div>
    }
}

export default TopicActivity
