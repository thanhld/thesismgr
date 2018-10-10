import React, { Component } from 'react'
import FacultyRequest from '../FacultyRequest'
import FacultyResponse from '../FacultyResponse'

class TopicsCancel extends Component {
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
                templates="cancel"
                title={`Xuất tờ trình cho dừng đề tài`}
                subtitle="Danh sách đề tài xin thôi thực hiện"
                stepId={2031}
                topics={topics.filter(t => t.topicStatus == 201 && !t.outOfficerIds)}
            />
            <FacultyResponse
                modalId="editResponse"
                {...this.props}
                stepId={303}
                title={`Ghi nhận quyết định cho dừng đề tài`}
                topics={topics.filter(t => t.topicStatus == 202 && !t.outOfficerIds)}
            />
        </div>
    }
}

export default TopicsCancel
