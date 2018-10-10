import React, { Component } from 'react'
import { array } from 'Helper'
import { notify, Modal } from 'Components'
import ManageTopicChange from '../ManageTopicChange'
import FacultyRequest from '../FacultyRequest'
import FacultyResponse from '../FacultyResponse'

class RegisterDefense extends Component {
    constructor(props) {
        super(props)
        this.state = {
            error: false,
            message: '',
            checked: []
        }
    }
    filterToOpen = () => {
        const { topics } = this.props
        this.setState({
            checked: array(topics.length, () => true)
        })
    }
    handleToggleCheckAll = e => {
        const length = this.state.checked.length
        this.setState({
            checked: array(length, () => e.target.checked)
        })
    }
    handleCheck = index => e => {
        let newCheck = [...this.state.checked]
        newCheck[index] = !newCheck[index]
        this.setState({
            checked: newCheck
        })
    }
    handleSubmit = e => {
        e.preventDefault()
        const { checked } = this.state
        const { actions, topics, reloadData } = this.props
        let topicData = []
        const stepId = 2054
        checked.forEach((c, index) => {
            if (!c) return
            topicData.push({
                stepId,
                topicId: topics[index].id
            })
        })
        actions.requestProtect(topicData).then(res => {
            const { data } = res.action.payload
            if (data.length > 0) {
                const mess = data.map(d => {
                    const t = cur_topics.find(t => d.topicId == t.id)
                    const e = d.error
                    return `${t && t.learner.learnerCode}: ${e}`
                })
                this.setState({
                    error: true,
                    message: mess.join('; ')
                })
            } else {
                this.props.reloadData(() => {
                    $('#topic-change').modal('hide')
                    notify.show('Thầy/cô cập nhật đề tài thành công', 'primary')
                })
            }
        }).catch(err => {
            notify.show(`Có lỗi xảy ra: ${err.response.data.message}`, 'danger')
        })
    }
    handleCancel = () => {
        this.setState({
            error: false
        })
    }
    render() {
        const { error, message, checked } = this.state
        const { type, topics, lecturers, degrees } = this.props
        return <div class="topic-admin-header">
            <button class="btn btn-sm btn-margin btn-success" data-toggle="modal" data-target="#topic-change" onClick={this.filterToOpen}>Đóng quyền đăng ký bảo vệ</button>
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
                templates="defense"
                title={`Xuất tờ trình đăng ký bảo vệ`}
                subtitle="Danh sách đề tài đăng ký bảo vệ"
                stepId={2052}
                topics={topics.filter(t => t.topicStatus == 669 && !t.outOfficerIds)}
            />
            <FacultyResponse
                modalId="editResponse"
                {...this.props}
                stepId={305}
                title={`Ghi nhận quyết định bảo vệ`}
                topics={topics.filter(t => t.topicStatus == 670 && !t.outOfficerIds)}
            />
            <Modal
                modalId="topic-change"
                title="Đóng quyền bảo vệ"
                onSubmit={this.handleSubmit}
                onCancel={this.handleCancel}>
                <ManageTopicChange
                    type={type}
                    checked={checked}
                    topics={topics}
                    degrees={degrees}
                    lecturers={lecturers}
                    handleCheck={this.handleCheck}
                    handleToggleCheckAll={this.handleToggleCheckAll}
                    error={error}
                    message={message} />
            </Modal>
        </div>
    }
}

export default RegisterDefense
