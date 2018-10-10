import React, { Component } from 'react'
import { Modal, notify } from 'Components'
import { array } from 'Helper'
import { ADMIN_FINISH_TOPIC } from 'Constants'
import ManageTopicChange from '../ManageTopicChange'

class AdminTopicsDefend extends Component {
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
        const stepId = 2081
        checked.forEach((c, index) => {
            if (!c) return
            topicData.push({
                stepId,
                topicId: topics[index].id
            })
        })
        actions.createActivity(ADMIN_FINISH_TOPIC, topicData).then(res => {
            const { data } = res.action.payload
            if (data.length > 0) {
                const mess = data.map(d => {
                    const t = this.props.topics.find(t => d.topicId == t.id)
                    const e = d.error
                    return `${t && t.learner.learnerCode}: ${e}`
                })
                this.setState({
                    error: true,
                    message: mess.join('; ')
                })
            } else {
                this.props.reloadData(() => {
                    $('#topic-success').modal('hide')
                    notify.show('Thầy/cô cập nhật đề tài thành công', 'primary')
                })
            }
        }).catch(err => {
            $('#topic-success').modal('hide')
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
        const { type, topics, degrees, lecturers } = this.props
        return <div class="topic-admin-header">
            <button class="btn btn-sm btn-margin btn-success" data-toggle="modal" data-target="#topic-success" onClick={this.filterToOpen}>Ghi nhận đề tài đã bảo vệ thành công</button>
            <Modal
                modalId="topic-success"
                title="Bảo vệ đề tài thành công"
                onSubmit={this.handleSubmit}
                onCancel={this.handleCancel}>
                <ManageTopicChange
                    type={type}
                    checked={checked}
                    topics={topics.filter(t => t.topicStatus == 700)}
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

export default AdminTopicsDefend
