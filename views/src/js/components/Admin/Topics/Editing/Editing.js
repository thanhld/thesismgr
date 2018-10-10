import React, { Component } from 'react'
import FacultyRequest from '../FacultyRequest'
import FacultyResponse from '../FacultyResponse'

class TopicsEditing extends Component {
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
                templates="edit"
                title={`Xuất tờ trình thay đổi đề tài`}
                subtitle="Danh sách đề tài yêu cầu chỉnh sửa"
                stepId={2011}
                topics={topics.filter(t => t.topicStatus == 891 && !t.outOfficerIds)}
            />
            <FacultyResponse
                modalId="editResponse"
                {...this.props}
                stepId={301}
                title={`Ghi nhận quyết định thay đổi đề tài`}
                topics={topics.filter(t => t.topicStatus == 892 && !t.outOfficerIds)}
            />
        </div>
    }
}

export default TopicsEditing
