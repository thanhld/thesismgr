import React, { Component } from 'react'
import axios from 'axios'
import moment from 'moment'
import Topic from './Topic'
import { getStepName } from 'Config'
import { API_TOPIC } from 'Constants'
import { stepIdToDocumentType } from 'Helper'

class Activity extends Component {
    constructor() {
        super()
        this.state = {
            isLoaded: false,
            data: []
        }
    }
    getLecturerNameById = id => {
        const { lecturers } = this.props
        const tmp = lecturers.find(l => l.id == id)
        return tmp && tmp.fullname
    }
    componentWillMount() {
        const { topicId } = this.props
        const sortActivity = (a, b) => {
            if (a.created > b.created) return 1
            if (a.created < b.created) return 1
            return 0
        }
        axios.get(`${API_TOPIC}/${topicId}/activity`).then(res => {
            this.setState({
                isLoaded: true,
                data: res.data.data.sort(sortActivity)
            })
        })
    }
    render() {
        const { isLoaded, data } = this.state
        const { topicId } = this.props
        return <div>
            <div class="row">
                <div class="col-xs-12 page-title">
                    Lịch sử đề tài
                </div>
            </div>
            <div>
                { data.map(d => {
                    const lecturerId = d.requestedSupervisorId
                    const lecturerName = this.getLecturerNameById(lecturerId)
                    return (
                        <div key={d.id}>
                            <span>{moment(d.created).format('HH:mm, DD/MM/YYYY')}: </span>
                            <span>
                                {getStepName(d.stepId, lecturerName)} {d.document && <span>
                                    theo { stepIdToDocumentType(d.stepId) } {(d.document.attachment && d.document.attachment.url) ?
                                        <a href={d.document.attachment.url} download>{d.document.documentCode}</a>
                                        : d.document.documentCode}, ngày {moment(d.document.createdDate).format('DD/MM/YYYY')}
                                </span>}
                            </span>

                        </div>
                    )}) }
            </div>
        </div>
    }
}

export default Activity
