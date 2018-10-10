import React, { Component } from 'react'
import AddLearnersModal from './AddLearnersModal'
import FacultyRequest from '../FacultyRequest'
import FacultyResponse from './FacultyResponse'
import { topicTypeToName, topicTypeToLearner } from 'Helper'

class TopicsRegister extends Component {
	newInter = () => {
		if (comfirm("Thầy/cô chắc chắn mở đợt đăng ký/bảo vệ mới?")) {
		}
	}
    render() {
        const { actions, topics, type, reloadData } = this.props
        return <div class="topic-admin-header">
			<button class="btn btn-success btn-sm" onClick={this.newInter} >
                {`Mở đợt mới`}
            </button> &nbsp;
            <button data-toggle="modal"
                class="btn btn-success btn-sm"
                data-target="#topicAddLearners">
                {`Thêm ${topicTypeToLearner(type)} đủ điều kiện`}
            </button>
            <div class="pull-right">
                <button class="btn btn-success btn-sm btn-margin" data-toggle="modal" data-target="#topicFacultyRequest">
                    Xuất tờ trình
                </button>
                <button class="btn btn-success btn-sm btn-margin" data-toggle="modal" data-target="#topicFacultyResponse">
                    Trường phê duyệt
                </button>
            </div>
            <AddLearnersModal
                modalId="topicAddLearners"
                title={`Thêm ${topicTypeToLearner(type)} đủ điều kiện đăng ký ${topicTypeToName(type)}`}
                {...this.props}
            />
            <FacultyRequest
                modalId="topicFacultyRequest"
                {...this.props}
                templates="register"
                title={`Xuất tờ trình đăng ký ${topicTypeToName(type)}`}
                subtitle={`Danh sách ${topicTypeToName(type)} đăng ký`}
                stepId={200}
                topics={topics.filter(t => t.topicStatus == 102 && !t.outOfficerIds)}
            />
            <FacultyResponse
                modalId="topicFacultyResponse"
                {...this.props}
                stepId={300}
                topics={topics.filter(t => t.topicStatus == 103 && !t.outOfficerIds)}
            />
        </div>
    }
}

export default TopicsRegister
