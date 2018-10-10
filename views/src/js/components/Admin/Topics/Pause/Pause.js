import React, { Component } from 'react'
import FacultyRequest from '../FacultyRequest'
import FacultyResponse from '../FacultyResponse'

class TopicsPause extends Component {
    render() {
        const { topics } = this.props
        return <div class="topic-admin-header">
            <div class="pull-right">
                <button class="btn btn-success btn-sm btn-margin"
                    data-toggle="modal"
                    data-target="#editRequest">
                    Xuất tờ trình
                </button>
                <button class="btn btn-success btn-sm btn-margin"
                    data-toggle="modal"
                    data-target="#editResponse">
                    Trường phê duyệt
                </button>
            </div>
            <div class="clearfix"></div>
            <FacultyRequest
                modalId="editRequest"
                {...this.props}
                templates="pause"
                title={`Xuất tờ trình tạm hoãn đề tài`}
                subtitle="Danh sách đề tài xin tạm hoãn"
                stepId={2041}
                topics={topics.filter(t => t.topicStatus == 301 && !t.outOfficerIds)}
            />
            <FacultyResponse
                modalId="editResponse"
                {...this.props}
                stepId={304}
                title={`Ghi nhận quyết định tạm hoãn đề tài`}
                topics={topics.filter(t => t.topicStatus == 302 && !t.outOfficerIds)}
            />
        </div>
    }
}

export default TopicsPause
