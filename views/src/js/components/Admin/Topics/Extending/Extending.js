import React, { Component } from 'react'
import FacultyRequest from '../FacultyRequest'
import FacultyResponse from '../FacultyResponse'

class TopicsExtending extends Component {
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
                templates="extend"
                title={`Xuất tờ trình gia hạn thời gian thực hiện đề tài`}
                subtitle="Danh sách đề tài xin gia hạn"
                stepId={2021}
                topics={topics.filter(t => t.topicStatus == 894 && !t.outOfficerIds)}
            />
            <FacultyResponse
                modalId="editResponse"
                {...this.props}
                stepId={302}
                title={`Ghi nhận quyết định gia hạn thời gian thực hiện đề tài`}
                topics={topics.filter(t => t.topicStatus == 895 && !t.outOfficerIds)}
            />
        </div>
    }
}

export default TopicsExtending
